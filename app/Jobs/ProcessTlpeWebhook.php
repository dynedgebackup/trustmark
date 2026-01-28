<?php

namespace App\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;
use App\Models\Business;
use App\Models\BusinessFees;
use Illuminate\Support\Facades\Log;


class ProcessTlpeWebhook implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    protected $payload;

    public function __construct($payload)
    {
        $this->payload = $payload;
    }

    public function handle()
    {
        Log::info('Job reached handle()');

        $response = $this->payload['data'] ?? [];
        $transactionId = $response['payment']['merchant_reference_id'] ?? '';
        $statusCode = $response['result']['status_code'] ?? '';
        $timestamp = $response['result']['timestamp'] ?? '';
        $amount = $response['payment']['amount'] ?? '';
        $tranID = $response['transaction_id'] ?? '';
        $payment_channel = $response['payment']['option'] ?? '';
        $firstName = $response['customer']['first_name'] ?? '';
        $lastName = $response['customer']['last_name'] ?? '';
        $cardholder = trim($firstName . ' ' . $lastName);

        // Common data
        $data = [
            'response_data' => json_encode($response, JSON_INVALID_UTF8_IGNORE),
            'date' => Carbon::parse($timestamp),
            'txnstatus' => $statusCode,
            'cardholder' => $cardholder,
            'payment_channel' => $payment_channel,
            'payment_in_process' => 0,
        ];

        $arrPayment = DB::table('payments')->where('transaction_id', $transactionId)->first();

        if ($statusCode === 'OK.00.00') {
            $data['payment_status'] = 1;
            $data['total_paid_amount'] = $amount;
            $data['tranID'] = $tranID;

            $or_serial_number = $this->getPrevORNumber();
            $or_number = str_pad($or_serial_number, 6, '0', STR_PAD_LEFT);
            $data['or_number'] = "TMK-" . $or_number;
            $data['or_serial_number'] = $or_serial_number;

            DB::table('payments')->where('transaction_id', $transactionId)->update($data);

            if ($arrPayment) {
                $business = Business::find($arrPayment->business_id);
                $_business = new Business();

                $business->payment_id = $arrPayment->id;
                $business->amount = $amount;
                $business->payment_channel = $payment_channel;
                $now = Carbon::parse($timestamp);
                $business->expired_date = $now->copy()->addYear();
                $business->date_issued = $now;
                $business->save();

                $business_fees = BusinessFees::where('busn_id', $arrPayment->business_id)
                    ->where('app_code', 1)->get();
                foreach ($business_fees as $fee) {
                    $fee->payment_id = $arrPayment->id;
                    $fee->save();
                }

                $business->qr_code = $_business->qr($business);
                $business->certificate = $_business->generateCertificate($business);
                $business->save();
            }
            Log::info('TLPE Webhook Success',$data);
        } elseif (in_array($statusCode, ['OK.02.00','OK.04.00'])) {
            $arrRefund = [
                'payment_status' => ($statusCode == 'OK.02.00') ? 3 : 4,
                'refund_amount' => $amount,
                'refund_response_data' => json_encode($response, JSON_INVALID_UTF8_IGNORE),
                'tranID' => $tranID
            ];
            DB::table('payments')->where('transaction_id', $transactionId)->update($arrRefund);
        } else {
            $data['payment_status'] = 2;
            $data['tranID'] = $tranID;
            DB::table('payments')->where('transaction_id', $transactionId)->update($data);
        }
    }

    private function getPrevORNumber()
    {
        $number = 1;
        $arrPrev = DB::table('payments')
            ->where('payment_status', 1)
            ->where('or_serial_number', '<>', 0)
            ->orderBy('id', 'DESC')
            ->first();
        return $arrPrev ? ((int)$arrPrev->or_serial_number + 1) : 1;
    }
}
