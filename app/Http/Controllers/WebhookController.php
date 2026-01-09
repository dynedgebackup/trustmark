<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;
use App\Models\Business;
use App\Models\BusinessFees;
use Illuminate\Support\Facades\Http;

class WebhookController extends Controller
{
    public function handle(Request $request)
    {
        $APP_ENV = app()->environment();
        if($APP_ENV=='prod'){
            $config = config('constants.tlpePaymentConfigProd');
        }else{
            $config = config('constants.tlpePaymentConfig');
        }
        $secret = trim($config['jwtSecret']);
        $transactionId = null; 

        try {
            // Step 1: Extract the actual JWT string from payload
            $json = $request->json()->all();

            if (!isset($json['payload'])) {
                throw new \Exception('Missing payload in request');
            }

            $jwt = $json['payload'];

            // Step 2: Decode JWT and verify signature
            $payload = $this->jwtDecode($jwt, $secret);

            // Step 3: Extract payment info
            $response = $payload['data'] ?? [];
            $tranID = $response['transaction_id'] ?? '';
            $statusCode = $response['result']['status_code'] ?? '';
            $timestamp = $response['result']['timestamp'] ?? '';
            
            $amount = $response['payment']['amount'] ?? '';
            $transactionId = $response['payment']['merchant_reference_id'] ?? '';
            $payment_channel = $response['payment']['option'] ?? '';
            $firstName = $data['customer']['first_name'] ?? '';
            $lastName = $data['customer']['last_name'] ?? '';
            $cardholder = trim($firstName . ' ' . $lastName);

           Log::info($transactionId.' - TLPE Webhook Decoded Payload', $payload);

            $response_data = $this->utf8ize($response);
            $data['response_data'] = json_encode($response_data, JSON_INVALID_UTF8_IGNORE);

            $data['date'] = Carbon::parse($timestamp);
            $data['txnstatus'] = $statusCode;
            $data['cardholder'] = $cardholder;
            $data['payment_in_process'] = 0;
            $data['payment_channel'] = $payment_channel;

            $paymentStatus="";
            $arrPayment = DB::table('payments')->where('transaction_id', $transactionId)->select('business_id', 'id','transaction_id','business_id','tranID')->first();
            if ($statusCode == 'OK.00.00') {
                $paymentStatus="Paid";
                $data['payment_status'] = 1;
                $data['total_paid_amount'] = $amount;
                $data['tranID'] = $tranID;
                $or_serial_number = $this->getPrevORNumber();
                $or_number = str_pad($or_serial_number, 6, '0', STR_PAD_LEFT);
                $data['or_number'] = "TMK-".$or_number;
                $data['or_serial_number'] = $or_serial_number;

                DB::table('payments')->where('transaction_id', $transactionId)->update($data);
                if (isset($arrPayment)) {
                    $_business = new Business();
                    $business = Business::find($arrPayment->business_id);
                    $business_fees = BusinessFees::where('busn_id', $arrPayment->business_id)
                        ->where('app_code', 1)
                        ->get();
                    if (isset($business)) {
                        $business->payment_id = $arrPayment->id;
                        $business->amount = $amount;
                        $business->payment_channel = $payment_channel;
                        $now = Carbon::parse($timestamp);
                        $business->expired_date = $now->copy()->addYear();
                        $business->date_issued = $now;
                        $business->save();
                        
                        foreach ($business_fees as $fee) {
                            $fee->payment_id = $arrPayment->id;
                            $fee->save();
                        }
                       
                        // qr
                        $fileName = $_business->qr($business);
                        $business->qr_code = $fileName;
                        //$business->qr_code = 'storage/document-upload/qr_code/' . $fileName;
                        // certificate
                        $fileName2 = $_business->generateCertificate($business);
                        $business->certificate = $fileName2;
                        //$business->certificate = 'storage/document-upload/certificate/' . $fileName2;
                        $business->save();
                    }
                }
            }else if ($statusCode == 'OK.02.00') {
                $data['tranID'] = $tranID;
                $data['refund_amount'] = $amount;

                $paymentStatus="Refund";
                $arrRefund['payment_status'] = 3;
                $arrRefund['refund_amount']= $data['refund_amount'];
                $arrRefund['refund_response_data']= $data['response_data'];
                DB::table('payments')->where('transaction_id', $transactionId)->update($arrRefund);
                //$this->updateRefundDetails($data,$arrPayment);

            }else if ($statusCode == 'OK.04.00') {
                $data['tranID'] = $tranID;
                $data['refund_amount'] = $amount;

                $paymentStatus="Refund";
                $arrRefund['payment_status'] = 4;
                $arrRefund['refund_amount']= $data['refund_amount'];
                $arrRefund['refund_response_data']= $data['response_data'];
                DB::table('payments')->where('transaction_id', $transactionId)->update($arrRefund);
                //$this->updateRefundDetails($data,$arrPayment);
            }
             else {
                $data['tranID'] = $tranID;
                $paymentStatus="Failed";
                $data['payment_status'] = 2;
                DB::table('payments')->where('transaction_id', $transactionId)->update($data);
            }
            if(isset($arrPayment)){
                $business = Business::find($arrPayment->business_id);
                $arrUser = DB::table('users')->select('name')->where('id', $business->user_id)->first();
                $username = '';
                if(isset($arrUser)){
                    $username = $arrUser->name;
                }
                $carbonTimestamp = Carbon::parse($timestamp);
                $datetime = $carbonTimestamp->format('Y-m-d H:i:s');
                DB::table('user_logs')->insert([
                    'busn_id'        =>$arrPayment->business_id,
                    'action_id'      => '17',
                    'action_name'      => 'paid',
                    'message'          => 'Payment done by '.$username.', Transaction Id - '.$transactionId.' dated ' .$datetime,
                    'public_ip_address'=> $request->ip(),
                    'status'           => $paymentStatus,
                    'remarks'          => '',
                    'longitude'        => '0',
                    'latitude'         => '0',
                    'created_by'       => $business->user_id,
                    'created_by_name'  => $username,
                    'created_date'     => $carbonTimestamp,
                ]);
            }
            return response()->json(['status' => 'Webhook received'], 200);

        } catch (\Exception $e) {
            Log::error('TLPE Webhook Error: ' . $e->getMessage(), [
                'transactionId' => $transactionId ?? 'N/A'
            ]);
            return response()->json(['error' => 'Invalid JWT or signature'], 400);
        }
    }
    public function updateRefundDetails($data,$arrPayment){
        $arrBusiness['refund_amount']=$data['refund_amount'];
        DB::table('businesses')->where('id', $arrPayment->business_id)->update($arrBusiness);
       
        $arrHistory['merchant_reference_id']=$arrPayment->transaction_id;
        $arrHistory['business_id']=$arrPayment->business_id;
        $arrHistory['transaction_reference_number']=$arrPayment->tranID;
        $arrHistory['refund_amount']=$data['refund_amount'];
        $arrHistory['created_at']=now();
        $arrHistory['updated_at']=now();

        if ($data['txnstatus'] == 'OK.02.00') {
            $arrHistory['refund_type']='Full Refund';
            $arrHistory['refund_transaction_reference_number']=$data['tranID'];
        }elseif ($data['txnstatus'] == 'OK.04.00') {
            $arrHistory['refund_type']='Partial Refund';
            $arrHistory['refund_transaction_reference_number']=$data['tranID'];
        }
        $arrExist = DB::table('payment_refund_history')->where('merchant_reference_id', $arrPayment->transaction_id)->where('refund_transaction_reference_number', $data['tranID'])->select('id')->first();
        if(isset($arrExist)){
            DB::table('payment_refund_history')->where('id', $arrExist->id)->update($arrHistory);
        }else{
            DB::table('payment_refund_history')->insert($arrHistory);
        }
    }
    /*public function handle(Request $request)
    {
        $this->manageWebhookDetails('',$request);exit;

        $APP_ENV = app()->environment();
        if($APP_ENV=='prod'){
            $config = config('constants.tlpePaymentConfigProd');
        }else{
            $config = config('constants.tlpePaymentConfig');
        }
        $secret = trim($config['jwtSecret']);
        try {
            $json = $request->json()->all();
            if (!isset($json['payload'])) {
                throw new \Exception('Missing payload in request');
            }
            $jwt = $json['payload'];
            // Step 2: Decode JWT and verify signature
            $payload = $this->jwtDecode($jwt, $secret);
            Log::info('TLPE Webhook Decoded Payload', $payload);
            // Step 3: Extract payment info
            $response = $payload['data'] ?? [];
            $transactionId = $response['payment']['merchant_reference_id'] ?? '';
            if (env('USER_BASE_URL') === 'https://tm.bahayko.app') {
                $this->manageWebhookDetails($transactionId,$request);
            }else{
                $tranID = $response['transaction_id'] ?? '';
                $statusCode = $response['result']['status_code'] ?? '';
                $amount = $response['payment']['amount'] ?? '';
                

                $firstName = $data['customer']['first_name'] ?? '';
                $lastName = $data['customer']['last_name'] ?? '';
                $cardholder = trim($firstName . ' ' . $lastName);

               
                $response_data = $this->utf8ize($response);
                $data['response_data'] = json_encode($response_data, JSON_INVALID_UTF8_IGNORE);

                $data['date'] = Carbon::now();
                $data['txnstatus'] = $statusCode;
                $data['tranID'] = $tranID;
                $data['cardholder'] = $cardholder;
                $data['payment_in_process'] = 0;

                if ($statusCode == 'OK.00.00') {
                    $data['payment_status'] = 1;
                    $data['total_paid_amount'] = $amount;
                    DB::table('payments')->where('transaction_id', $transactionId)->update($data);

                    $arrPayment = DB::table('payments')->where('transaction_id', $transactionId)->select('business_id', 'id')->first();
                    if (isset($arrPayment)) {
                        $_business = new Business();
                        $business = Business::find($arrPayment->business_id);
                        $business_fees = BusinessFees::where('busn_id', $arrPayment->business_id)
                            ->where('app_code', 1)
                            ->get();
                        if (isset($business)) {
                            $business->payment_id = $arrPayment->id;
                            foreach ($business_fees as $fee) {
                                $fee->payment_id = $arrPayment->id;
                                $fee->save();
                            }

                            $now = Carbon::now();
                            $business->expired_date = $now->copy()->addYear();
                            // qr
                            $fileName = $_business->qr($business);
                            $business->qr_code = 'storage/document-upload/qr_code/' . $fileName;
                            // certificate
                            $fileName2 = $_business->generateCertificate($business);
                            $business->certificate = 'storage/document-upload/certificate/' . $fileName2;
                            $business->save();
                        }
                    }
                } else {
                    $data['payment_status'] = 2;
                    DB::table('payments')->where('transaction_id', $transactionId)->update($data);
                }
                return response()->json(['status' => 'Webhook received'], 200);
            }
        } catch (\Exception $e) {
            Log::error('TLPE Webhook Error: ' . $e->getMessage());
            return response()->json(['error' => 'Invalid JWT or signature'], 400);
        }
        
    }*/

    /*public function manageWebhookDetails($transactionId='',$request=''){
        $transactionId = "BNRS17553077162229";
        $dummyPayload = [
            "customer" => [
            "first_name" => "Rogelmar",
            "last_name" => "Denopol",
            "contact" => [
                "email" => "rogelmardenopol@gmail.com",
                "mobile" => "+639658175177"
            ],
            "billing_address" => [
                "line1" => "123 Main Street",
                "line2" => null,
                "city_municipality" => "Metro City",
                "zip" => "1000",
                "state_province_region" => "Metro",
                "country_code" => "PH",
                "country_name" => "Philippines"
            ],
            "shipping_address" => [
                "line1" => "123 Main Street",
                "line2" => null,
                "city_municipality" => "Metro City",
                "zip" => "1000",
                "state_province_region" => "Metro",
                "country_code" => "PH",
                "country_name" => "Philippines"
            ]
            ],
            "payment" => [
            "amount" => "1130",
            "currency" => "PHP",
            "currency_name" => "Philippine Peso",
            "option" => "Visa",
            "description" => "Checkout Payment",
            "merchant_reference_id" => "TRUSTMARK17549257716223"
            ],
            "result" => [
            "timestamp" => "2025-08-11 15:23:18 +00:00",
            "status_code" => "OK.00.00",
            "message" => "Payment successful",
            "processor_reference_id" => "8ac7a4a298972c4d019899ba951e14e5"
            ],
            "custom_parameters" => [],
            "processor_parameters" => [],
            "transaction_id" => "250811152251KEF4571"
        ];
        try {
            $forwardUrl = null;
            if (str_starts_with($transactionId, 'BNRS')) {
                //$forwardUrl = "https://tm.bahayko.app/unified-services/bnrs/payment/webhook";
                $forwardUrl = "http://127.0.0.1:8000/api/bnrs/payment/webhook";
            } elseif (str_starts_with($transactionId, 'VAPE')) {
                $forwardUrl = "https://tm.bahayko.app/unified-services/vape/payment/webhook";
            }
            //$request->all()
            $forwardResponse = Http::withHeaders($request->headers->all())
                ->post($forwardUrl, $dummyPayload);

            echo $forwardResponse->body();exit;

            Log::info('Sub-webhook Forwarded', [
                'transaction_id' => $transactionId,
                'url'    => $forwardUrl,
                'status' => $forwardResponse->status(),
                'body'   => $forwardResponse->body(),
            ]);
        }catch (\Throwable $e) {
            echo "ddd<pre>";
            dd($e->getMessage());
            Log::error("Sub-webhook forwarding failed for {$transactionId}: " . $e->getMessage());
        }
    }*/
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

        /*Log::info('JWT Decode Debug', [
            'signedData' => $signedData,
            'expectedSignature' => $expectedSignature,
            'signatureFromJWT' => $signature64,
        ]);*/

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
    public function generateCertificates(Request $request){
         $id = $request->input('id');
         if($id>0){
            $business = Business::find($id);
            if (isset($business)) {
                $_business = new Business();
                // qr
                $fileName = $_business->qr($business);
                $business->qr_code = $fileName;
                //$business->qr_code = 'storage/document-upload/qr_code/' . $fileName;
                // certificate
                $fileName2 = $_business->generateCertificate($business);
                $business->certificate = $fileName2;
                //$business->certificate = 'storage/document-upload/certificate/' . $fileName2;
                $business->save();
                echo "Certificate generated.";
            }else{
                echo "Business not found.";
            }
        }else{
            echo "Business not found.";
        }
    }
    public function getPrevORNumber(){
        $number=1;
        $arrPrev = DB::table('payments')->select('or_serial_number')->orderby('id','DESC')->first(); 
        if(isset($arrPrev)){
            $number = (int)$arrPrev->or_serial_number+1;
        }
        return $number;
    }


}
