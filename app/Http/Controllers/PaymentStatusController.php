<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\LocationRegion;
use Illuminate\Validation\Rule;
use File;
use Carbon\Carbon;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Support\Facades\Auth;
use App\Models\BusinessFees;
use DB;
use Illuminate\Support\Facades\Hash;

class PaymentStatusController extends Controller
{
    public $arrapp_code = array(""=>"");
    public $arrfee_id = array(""=>"");
    public function __construct(){
		$this->Region = new LocationRegion(); 
        $this->data = array('id'=>'');  
    }
    public function index()
    {
        return view('payment.index');
    }
    public function refundIndex()
    {
        return view('payment.refund');
    }
   
    public function updatePaymentStatus(Request $request)
    {
        $transId=$request->input('transId');   
        sleep(10);
        $arrPayment = DB::table('payments')->where('tranID',$transId)->select('payment_status')->first();
        if(!isset($arrPayment)){
            $APP_ENV = app()->environment();
            if ($APP_ENV == 'prod') {
                $config = config('constants.tlpePaymentConfigProd');
            } else {
                $config = config('constants.tlpePaymentConfig');
            }

            $paymentOptions = [];
            $curl = curl_init();
            curl_setopt_array($curl, [
                CURLOPT_URL => $config['apiBase'] . '/sync',
                CURLOPT_RETURNTRANSFER => true,
                CURLOPT_ENCODING => '',
                CURLOPT_MAXREDIRS => 10,
                CURLOPT_TIMEOUT => 0,
                CURLOPT_FOLLOWLOCATION => true,
                CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
                CURLOPT_CUSTOMREQUEST => 'POST',
                CURLOPT_POSTFIELDS => json_encode([
                    'transaction_id' => $transId,
                    'notify_user' => false,
                ]),
                CURLOPT_HTTPHEADER => [
                    'Authorization: ' . $config['token'], // or add 'Bearer ' if needed
                    'Content-Type: application/json',
                ],
            ]);

            $response = curl_exec($curl);
            curl_close($curl);
            $data = json_decode($response, true);            
            if(isset($data['status']) && $data['status']==200){
                if ($data['data']['status_code'] == 'OK.00.00') {
                    $arrHistory['transaction_reference_number']=$transId;
                    $arrHistory['created_at']=now();
                    $arrHistory['created_by']=Auth::id();
                    $arrHistory['updated_at']=now();
                    $arrHistory['updated_by']=Auth::id();
                    $arrHistory['response_data'] = json_encode($data, JSON_INVALID_UTF8_IGNORE);
                    DB::table('payment_updated_history')->insert($arrHistory);
                    sleep(5);

                    $payment = DB::table('payments')->where('tranID', $transId)->select('payment_status')->first();
                    if ($payment && $payment->payment_status == 1) {
                        $arrReturn['message']=$data['data']['status_description'];
                        $arrReturn['status']=true;
                    } else {
                        $arrReturn['message'] = 'We are unable to verify your payment at the moment. Please contact support.';
                        $arrReturn['status'] = false;
                    }
                }
            }else{
                $arrReturn['message']= $data['data']['status_description'];
                $arrReturn['status']=false;
            }
        }else{
            $paymentStatus = $arrPayment->payment_status==1?'paid':"failed";
            $arrReturn['message']='Payment is already synced, and the status is marked as '.$paymentStatus;
            $arrReturn['status']=false;
        }
        echo json_encode($arrReturn);exit;
    }

    public function getList(Request $request)
    {
        $params = $_REQUEST;
        $q = $request->input('q');
        $fromdate = $request->input('fromdate');
        $todate = $request->input('todate');

        if (! isset($params['start']) || ! isset($params['length'])) {
            $params['start'] = '0';
            $params['length'] = '10';
        }

        $columns = [
            1 => 'trustmark_id',
            2 => 'business_name',
            3 => 'transaction_id',
            4 => 'final_total_amount',
            5 => 'created_at',
            6 => 'payment_status'
        ];

        $sql = DB::table('businesses as a')
            ->leftJoin('payments as p', 'p.business_id', '=', 'a.id')
            ->select('p.id','trustmark_id','business_name','transaction_id','p.created_at','p.payment_status','final_total_amount')
            ->where('a.is_active', 1)
            ->whereNull('payment_status')
            ->where('p.payment_in_process', 1);
      
        if (! empty($q)) {
            $sql->where(function ($query) use ($q) {
                $query->where(DB::raw('LOWER(trustmark_id)'), 'like', '%'.strtolower($q).'%')
                    ->orWhere(DB::raw('LOWER(business_name)'), 'like', '%'.strtolower($q).'%')
                    ->orWhere(DB::raw('LOWER(transaction_id)'), 'like', '%'.strtolower($q).'%');
            });
        }
        if (! empty($fromdate) && isset($fromdate)) {
            $sql->whereDate('p.created_at', '>=', trim($fromdate));
        }
        if (! empty($todate) && isset($todate)) {
            $sql->whereDate('p.created_at', '<=', trim($todate));
        }

        if (isset($params['order'][0]['column'])) {
            $sql->orderBy($columns[$params['order'][0]['column']], $params['order'][0]['dir']);
        } else {
            $sql->orderBy('p.id', 'DESC');
        }

        $data_cnt = $sql->count();

        if ((int) $params['start'] >= $data_cnt) {
            $params['start'] = 0;
        }

        $sql->offset((int) $params['start'])->limit((int) $params['length']);
        $data = $sql->get();

        // $data=$this->business->getList($request);
        // echo "<pre>"; print_r($data); exit;
        $arr = [];
        $i = '0';
        $sr_no = (int) $request->input('start') - 1;
        $sr_no = $sr_no > 0 ? $sr_no + 1 : 0;
        $role = Auth::user()->role;

        foreach ($data as $row) {
            $sr_no = $sr_no + 1;

            $arr[$i]['srno'] = $sr_no;
            $arr[$i]['trustmark_id'] = $row->trustmark_id ?? 'N/A';
            $arr[$i]['business_name'] = $row->business_name;
            $arr[$i]['transaction_id'] = $row->transaction_id ?? 'N/A';
            $arr[$i]['final_total_amount'] = $row->final_total_amount ?? 'N/A';
            $arr[$i]['payment_status'] = $row->payment_status ?? 'Pending';
            $arr[$i]['date'] = $row->created_at ?? 'N/A';
            $i++;
        }

        $totalRecords = $data_cnt;
        $json_data = [
            'recordsTotal' => intval($totalRecords),
            'recordsFiltered' => intval($totalRecords),
            'data' => $arr,   // total data array
        ];
        echo json_encode($json_data);
    }

    public function refundGetList(Request $request)
    {
        $params = $_REQUEST;
        $q = $request->input('q');
        $fromdate = $request->input('fromdate');
        $todate = $request->input('todate');

        if (! isset($params['start']) || ! isset($params['length'])) {
            $params['start'] = '0';
            $params['length'] = '10';
        }

        $columns = [
            1 => 'trustmark_id',
            2 => 'business_name',
            3 => 'transaction_id',
            3 => 'tranID',
            4 => 'final_total_amount',
            5 => 'created_at',
            6 => 'payment_status'
        ];

        $sql = DB::table('businesses as a')
            ->leftJoin('payments as p', 'p.business_id', '=', 'a.id')
            ->select('p.id','trustmark_id','business_name','transaction_id','p.created_at','p.payment_status','final_total_amount','tranID')
            ->where('a.is_active', 1)
            ->where('payment_status', 1);
      
        if (! empty($q)) {
            $sql->where(function ($query) use ($q) {
                $query->where(DB::raw('LOWER(trustmark_id)'), 'like', '%'.strtolower($q).'%')
                    ->orWhere(DB::raw('LOWER(tranID)'), 'like', '%'.strtolower($q).'%')
                    ->orWhere(DB::raw('LOWER(business_name)'), 'like', '%'.strtolower($q).'%')
                    ->orWhere(DB::raw('LOWER(transaction_id)'), 'like', '%'.strtolower($q).'%');
            });
        }
        if (! empty($fromdate) && isset($fromdate)) {
            $sql->whereDate('p.created_at', '>=', trim($fromdate));
        }
        if (! empty($todate) && isset($todate)) {
            $sql->whereDate('p.created_at', '<=', trim($todate));
        }

        if (isset($params['order'][0]['column'])) {
            $sql->orderBy($columns[$params['order'][0]['column']], $params['order'][0]['dir']);
        } else {
            $sql->orderBy('p.id', 'DESC');
        }

        $data_cnt = $sql->count();

        if ((int) $params['start'] >= $data_cnt) {
            $params['start'] = 0;
        }

        $sql->offset((int) $params['start'])->limit((int) $params['length']);
        $data = $sql->get();

        // $data=$this->business->getList($request);
        // echo "<pre>"; print_r($data); exit;
        $arr = [];
        $i = '0';
        $sr_no = (int) $request->input('start') - 1;
        $sr_no = $sr_no > 0 ? $sr_no + 1 : 0;
        $role = Auth::user()->role;

        foreach ($data as $row) {
            $sr_no = $sr_no + 1;

            $arr[$i]['srno'] = $sr_no;
            $arr[$i]['trustmark_id'] = $row->trustmark_id ?? 'N/A';
            $arr[$i]['business_name'] = $row->business_name;
            $arr[$i]['transaction_id'] = $row->transaction_id ?? 'N/A';
            $arr[$i]['transId'] = $row->tranID ?? 'N/A';
            $arr[$i]['final_total_amount'] = $row->final_total_amount ?? 'N/A';
            $arr[$i]['payment_status'] = '<span class="badge badge-bg-approve px-2 py-1 small text-center d-inline-block"style="min-width: 80px;">Paid</span>';
            $arr[$i]['date'] = $row->created_at ?? 'N/A';


            $arr[$i]['action'] = '<input type ="hidden" id="amt_'.$row->id.'" value="'.$row->final_total_amount.'"><input type ="hidden" id="transId_'.$row->id.'" value="'.$row->tranID.'"><input type ="hidden" id="security_no'.$row->id.'" value="'.$row->trustmark_id.'"><a href="#" class="btn btn-sm btn-success quickRun" style="padding:9px;" pid="'.$row->id.'"><span class="btn-inner--icon" style="color: white;">Refund</span></a>';
            $i++;
        }

        $totalRecords = $data_cnt;
        $json_data = [
            'recordsTotal' => intval($totalRecords),
            'recordsFiltered' => intval($totalRecords),
            'data' => $arr,   // total data array
        ];
        echo json_encode($json_data);
    }

    public function refundAmount(Request $request)
    {
        $transId=$request->input('transId');   
        $partialRefund=$request->input('partialRefund');   
        $fullRefund=$request->input('fullRefund');   
        $refund_amount=$request->input('refund_amount');   
        $reason=$request->input('reason');   
        $pid =$request->input('pid');   
        $feeData =$request->input('feeData');   

        $user = Auth::user(); // Get logged-in user
        $enteredPassword = $request->input('user_password');

        // 🧠 Step 1: Check password match
        if (!Hash::check($enteredPassword, $user->password)) {
            $arrReturn['message']='Invalid password. Please try again.';
            $arrReturn['status']=false;
            echo json_encode($arrReturn);exit;
        }


        $arrReturn=[];
        $arrPayment = DB::table('payments')->where('id',$pid)->select('payment_status','tranID','business_id','transaction_id')->first();
        if(isset($arrPayment)){
            if($arrPayment->tranID==$transId){
                $APP_ENV = app()->environment();
                if ($APP_ENV == 'prod') {
                    $config = config('constants.tlpePaymentConfigProd');
                } else {
                    $config = config('constants.tlpePaymentConfig');
                }

                $paymentOptions = [
                    'transaction_id' => $transId,
                    'notify_user' => false,
                    "reason"=>$reason
                ];

                if($partialRefund){
                    $paymentOptions['amount'] = $refund_amount;
                }
                $curl = curl_init();
                curl_setopt_array($curl, [
                    CURLOPT_URL => $config['apiBase'] . '/refund',
                    CURLOPT_RETURNTRANSFER => true,
                    CURLOPT_ENCODING => '',
                    CURLOPT_MAXREDIRS => 10,
                    CURLOPT_TIMEOUT => 0,
                    CURLOPT_FOLLOWLOCATION => true,
                    CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
                    CURLOPT_CUSTOMREQUEST => 'POST',
                    CURLOPT_POSTFIELDS => json_encode($paymentOptions),
                    CURLOPT_HTTPHEADER => [
                        'Authorization: ' . $config['token'], // or add 'Bearer ' if needed
                        'Content-Type: application/json',
                    ],
                ]);

                $response = curl_exec($curl);
                curl_close($curl);
                $data = json_decode($response, true);    
                $arrReturn['data']= $data;

                if(isset($data['status']) && $data['status']==200){
                    $arrHistory['response_data'] = json_encode($data);
                    $arrHistory['merchant_reference_id']=$arrPayment->transaction_id;
                    $arrHistory['business_id']=$arrPayment->business_id;
                    $arrHistory['transaction_reference_number']=$transId;
                    $arrHistory['refund_reason']=$reason;
                    $arrHistory['created_at']=now();
                    $arrHistory['created_by']=Auth::id();
                    $arrHistory['updated_at']=now();
                    $arrHistory['updated_by']=Auth::id();

                    if ($data['data']['status_code'] == 'OK.02.00') {
                        $arrHistory['refund_type']='Full Refund';
                        $arrHistory['refund_transaction_reference_number']=$data['data']['transaction_id'];
                        $this->updateRefundDetails($arrHistory,$feeData);
                        $arrReturn['message']=$data['data']['status_description'];
                        $arrReturn['status']=true;
                    }elseif ($data['data']['status_code'] == 'OK.04.00') {
                        $arrHistory['refund_type']='Partial Refund';
                        $arrHistory['refund_transaction_reference_number']=$data['data']['transaction_id'];
                        $this->updateRefundDetails($arrHistory,$feeData);

                        $arrReturn['message']=$data['data']['status_description'];
                        $arrReturn['status']=true;
                    }else{
                        $arrReturn['message']= $data['data']['status_description'];
                        $arrReturn['status']=false;
                    }
                }else{
                    $arrReturn['message']= $data['data']['status_description'];
                    $arrReturn['status']=false;
                }
            }else{
                $arrReturn['message']='This transaction not found.';
                $arrReturn['status']=false;
            }
        }else{
            $arrReturn['message']='This transaction not found.';
            $arrReturn['status']=false;
        }
        echo json_encode($arrReturn);exit;
    }
    public function updateRefundDetails($arrHistory,$feeData){
        if(isset($feeData)){
            foreach($feeData as $fee){
                if((int)$fee['refund_amount']>0){
                    $arrHistory['busn_fee_id']=$fee['fee_id'];
                    $arrHistory['refund_amount']=$fee['refund_amount'];
                    $arrExist = DB::table('payment_refund_history')->where('merchant_reference_id', $arrHistory['merchant_reference_id'])->where('busn_fee_id', $fee['fee_id'])->where('refund_transaction_reference_number', $arrHistory['refund_transaction_reference_number'])->select('id')->first();
                    if(isset($arrExist)){
                        DB::table('payment_refund_history')->where('id', $arrExist->id)->update($arrHistory);
                    }else{
                        DB::table('payment_refund_history')->insert($arrHistory);
                    }
                }
            }
        }
    }

    public function getFeeDetails(Request $request)
    {
        $pid=$request->input('pid');   
        $arrPayment = DB::table('payments')->where('id',$pid)->select('business_id')->first();
        if(isset($arrPayment)){
             $busines_fee = BusinessFees::where('busn_id', $arrPayment->business_id)->get();
            if(count($busines_fee)>0){
                $finalAmount=0;
                $finalRefund=0;
                $finalRemaining=0;
                foreach($busines_fee as $fee){
                    $finalAmount +=$fee->amount;
                    $finalRemaining+=$fee->amount;
                    ?>
                    <tr>
                        <td class="table-label name"><?= $fee->fee_name ?></td>
                        <td class="table-label" id="amount_<?= $fee->id ?>" data-original="<?= $fee->amount ?>">
                            <?= number_format($fee->amount, 2) ?>
                        </td>
                        <td class="table-label">
                            <input type="number" class="form-control refund-input"
                                   id="refund_<?= $fee->id ?>"
                                   value=""
                                   min="0"
                                   style="text-align: center;">
                        </td>
                        <td class="table-label" id="remaining_<?= $fee->id ?>">
                            <?= number_format($fee->amount, 2) ?>
                        </td>
                    </tr><?php 
                } ?>
                <tr>
                    <td class="table-label name"><strong>Total</strong></td>
                    <td class="table-label" id="final_amount"><?= number_format($finalAmount, 2) ?></td>
                    <td class="table-label" id="final_refund"><?= number_format($finalRefund, 2) ?></td>
                    <td class="table-label" id="final_remaining"><?= number_format($finalRemaining, 2) ?></td>
                </tr><?php

            }else{ ?>
                <tr>
                    <td colspan="2" class="table-label">No fees found.</td>
                </tr><?php 
            }
        }else{ ?>
            <tr>
                <td colspan="2" class="table-label">No fees found.</td>
            </tr><?php 
        }
    }
}
