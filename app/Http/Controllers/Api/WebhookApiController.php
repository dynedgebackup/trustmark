<?php

namespace App\Http\Controllers\Api;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;
use App\Models\Business;
use App\Models\BusinessFees;
use App\Jobs\ProcessTlpeWebhook; // New Job
use Illuminate\Support\Facades\Http;

class WebhookApiController extends Controller
{
    public function handle(Request $request)
    {
        $APP_ENV = app()->environment();
        $config = ($APP_ENV == 'prod') 
            ? config('constants.tlpePaymentConfigProd') 
            : config('constants.tlpePaymentConfig');
        $secret = trim($config['jwtSecret']);
        $transactionId = null;

        try {
            $json = $request->json()->all();
            if (!isset($json['payload'])) {
                throw new \Exception('Missing payload in request');
            }

            $jwt = $json['payload'];

            // Decode JWT and verify signature
            $payload = $this->jwtDecode($jwt, $secret);

            // Extract transaction ID for logging
            $response = $payload['data'] ?? [];
            $transactionId = $response['payment']['merchant_reference_id'] ?? null;

            Log::info("TLPE Webhook Received: $transactionId", $payload);

            // Dispatch all heavy processing to queue
            ProcessTlpeWebhook::dispatch($payload);

            // Immediate 200 response to TLPE
            return response()->json(['status' => 'Webhook received'], 200);

        } catch (\Exception $e) {
            Log::error('TLPE Webhook Error: ' . $e->getMessage(), [
                'transactionId' => $transactionId ?? 'N/A'
            ]);
            // Only fail if signature invalid
            return response()->json(['error' => 'Invalid JWT or signature'], 400);
        }
    }

    public function generateCertificates(Request $request){
        $id = $request->input('id');
        if($id > 0){
            $business = Business::find($id);
            if ($business) {
                $_business = new Business();
                $business->qr_code = $_business->qr($business);
                $business->certificate = $_business->generateCertificate($business);
                $business->save();
                echo "Certificate generated.";
            } else {
                echo "Business not found.";
            }
        } else {
            echo "Business not found.";
        }
    }

    private function jwtDecode($jwt, $secret)
    {
        $parts = explode('.', $jwt);
        if (count($parts) !== 3) {
            throw new \Exception('Invalid JWT structure');
        }

        [$header64, $payload64, $signature64] = $parts;

        $base64url_decode = fn($input) => json_decode(base64_decode(strtr($input, '-_', '+/')), true);

        $payload = $base64url_decode($payload64);
        $signedData = "$header64.$payload64";

        $expectedSignature = rtrim(strtr(
            base64_encode(hash_hmac('sha256', $signedData, $secret, true)),
            '+/', '-_'
        ), '=');

        if (!hash_equals($expectedSignature, $signature64)) {
            throw new \Exception('Signature verification failed');
        }

        return $payload;
    }

    private function utf8ize($mixed)
    {
        if (is_array($mixed)) {
            foreach ($mixed as $key => $value) {
                $mixed[$key] = $this->utf8ize($value);
            }
        } elseif (is_string($mixed)) {
            return mb_convert_encoding($mixed, 'UTF-8', 'UTF-8');
        }
        return $mixed;
    }

    public function getPrevORNumber(){
        $number = 1;
        $arrPrev = DB::table('payments')
            ->select('or_serial_number')
            ->where('payment_status', '1')
            ->where('or_serial_number', '<>', 0) 
            ->orderBy('id', 'DESC')
            ->first();

        if ($arrPrev) {
            $number = (int)$arrPrev->or_serial_number + 1;
        }
        return $number;
    }

    public function updateRefundDetails($data, $arrPayment){
        $arrBusiness['refund_amount'] = $data['refund_amount'];
        DB::table('businesses')->where('id', $arrPayment->business_id)->update($arrBusiness);

        $arrHistory = [
            'merchant_reference_id' => $arrPayment->transaction_id,
            'business_id' => $arrPayment->business_id,
            'transaction_reference_number' => $arrPayment->tranID,
            'refund_amount' => $data['refund_amount'],
            'created_at' => now(),
            'updated_at' => now(),
        ];

        if ($data['txnstatus'] == 'OK.02.00') {
            $arrHistory['refund_type'] = 'Full Refund';
            $arrHistory['refund_transaction_reference_number'] = $data['tranID'];
        } elseif ($data['txnstatus'] == 'OK.04.00') {
            $arrHistory['refund_type'] = 'Partial Refund';
            $arrHistory['refund_transaction_reference_number'] = $data['tranID'];
        }

        $arrExist = DB::table('payment_refund_history')
            ->where('merchant_reference_id', $arrPayment->transaction_id)
            ->where('refund_transaction_reference_number', $data['tranID'])
            ->select('id')
            ->first();

        if ($arrExist) {
            DB::table('payment_refund_history')->where('id', $arrExist->id)->update($arrHistory);
        } else {
            DB::table('payment_refund_history')->insert($arrHistory);
        }
    }
}
