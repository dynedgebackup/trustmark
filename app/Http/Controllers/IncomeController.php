<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Income;
use Illuminate\Validation\Rule;
use File;
use Carbon\Carbon;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Support\Facades\Auth;
use DB;

class IncomeController extends Controller
{
    public $arrapp_code = array(""=>"");
    public $arrfee_id = array(""=>"");
    public function __construct(){
		$this->MenuGroup = new Income(); 
        $this->data = array('id'=>'','name'=>'','code'=>'','description'=>'','icon'=>'','slug'=>'','is_active'=>'');  
    }
    public function index()
    {
        return view('Income.index');
    }
    public function getFeesAjaxList(Request $request){
        $search = $request->input('search');
        $arrRes = $this->MenuGroup->getFeesAjaxList($search);
        $arr = array();
        foreach ($arrRes['data'] as $key=>$val) {
            $arr['data'][$key]['id']=$val->id;
            $arr['data'][$key]['text']=$val->name;
        }
        $arr['data_cnt']=$arrRes['data_cnt'];
        echo json_encode($arr);
    }
    public function ActiveInactive(Request $request){
        $id = $request->input('id');
        $is_activeinactive = $request->input('is_activeinactive');
        $data=array('status' => $is_activeinactive);
        $this->MenuGroup->updateActiveInactive($id,$data);
    }
    public function store(Request $request){
       $data = (object)$this->data;
       $arrapp_code = $this->arrapp_code;
       $arrfee_id = $this->arrfee_id;
        if($request->input('id')>0 && $request->input('submit')==""){
            $data = $this->MenuGroup->getEditDetails($request->input('id'));
            
        }
		
        if($request->input('submit')!=""){
            foreach((array)$this->data as $key=>$val){
                $this->data[$key] = $request->input($key);
            }
            
            $this->data['updated_by']=Auth::id();
            $this->data['updated_at'] = date('Y-m-d H:i:s');
            if($request->input('id')>0){
                $this->MenuGroup->updateData($request->input('id'),$this->data);
                $lastInsertedId = $request->input('id');
                $success_msg = 'Updated successfully.';
            }else{
                $this->data['created_by']=Auth::id();
                $this->data['created_at'] = date('Y-m-d H:i:s');
                $lastInsertedId = $this->MenuGroup->addData($this->data);
                $success_msg = 'Added successfully.';
                
            }
            return redirect()->route('MenuGroup.index')->with('success', __($success_msg));

        }


        
        return view('MenuGroup.create',compact('data'));
    }
    public function exportAll(Request $request)
    {
        $startdate = $request->input('fromdate');
        $enddate = $request->input('todate');
        $query = DB::table('business_fees as a')
            
            ->join('businesses as b', 'a.busn_id', '=', 'b.id')
            ->join('payments as c', 'a.payment_id', '=', 'c.id')
            ->join('users as d', 'b.user_id', '=', 'd.id')
            ->where('a.payment_id', '>', 0);

            if ($request->filled('fee_id')) {
                $query->where('a.fee_id', $request->fee_id);
            }
            if (!empty($startdate)) {
                $sdate = explode('-', $startdate);
                $startdate = date('Y-m-d', strtotime("{$sdate[2]}-{$sdate[1]}-{$sdate[0]}"));
                $query->whereDate('a.create_date', '>=', trim($startdate));
            }

            if (!empty($enddate)) {
                $edate = explode('-', $enddate);
                $enddate = date('Y-m-d', strtotime("{$edate[2]}-{$edate[1]}-{$edate[0]}"));
                $query->whereDate('a.create_date', '<=', trim($enddate));
            }
            if (!empty($request->q)) {
                $search = $request->q;
                $query->where(function($q) use ($search) {
                    $q->where('b.business_name', 'like', "%{$search}%")
                    ->orWhere(DB::raw('LOWER(b.trustmark_id)'),'like',"%".strtolower($search)."%")
                    ->orWhere(DB::raw('LOWER(a.fee_name)'),'like',"%".strtolower($search)."%")
                    ->orWhere(DB::raw('LOWER(c.transaction_id)'),'like',"%".strtolower($search)."%")
                    ->orWhere(DB::raw('LOWER(a.or_number)'),'like',"%".strtolower($search)."%")
                    ->orWhere(DB::raw('LOWER(a.amount)'),'like',"%".strtolower($search)."%")
                    ->orWhere(DB::raw('LOWER(a.create_date)'),'like',"%".strtolower($search)."%")
                    ->orWhere(DB::raw('LOWER(d.name)'),'like',"%".strtolower($search)."%");
                    });
            }

        $data = $query->select(
            'b.business_name as BusinessName',
            'b.trustmark_id as SecurityNo',
            'a.fee_name as PaymentDescription',
            'c.transaction_id as TransactionID',
            'c.or_number as OR_Number',
            'a.amount as Amount',
            'a.create_date as Date',
            'd.name as PaymentBy',
            'a.fee_id',
            'b.payment_channel'
        )->orderBy('b.trustmark_id', 'asc')->get();

        return response()->json(['data' => $data]);
    }

    public function getList(Request $request)
    {
        $startdate = $request->input('fromdate');
        $enddate = $request->input('todate');
        $query = DB::table('business_fees as a')
        ->select([
            'b.business_name as BusinessName',
            'b.trustmark_id as SecurityNo',
            'a.fee_name as PaymentDescription',
            'c.transaction_id as TransactionID',
            'c.or_number as OR_Number',
            'a.amount as Amount',
            'c.date as Date',
            'd.name as PaymentBy',
            'a.fee_id',
            'b.payment_channel'
        ])
        ->join('businesses as b', 'a.busn_id', '=', 'b.id')
        ->join('payments as c', 'a.payment_id', '=', 'c.id')
        ->join('users as d', 'b.user_id', '=', 'd.id')
        ->where('a.payment_id', '>', 0)
        ->orderBy('b.trustmark_id', 'asc');
        if ($request->filled('fee_id')) {
            $query->where('a.fee_id', $request->fee_id);
        }
        if (!empty($startdate)) {
            $sdate = explode('-', $startdate);
            $startdate = date('Y-m-d', strtotime("{$sdate[2]}-{$sdate[1]}-{$sdate[0]}"));
            $query->whereDate('c.date', '>=', trim($startdate));
        }

        if (!empty($enddate)) {
            $edate = explode('-', $enddate);
            $enddate = date('Y-m-d', strtotime("{$edate[2]}-{$edate[1]}-{$edate[0]}"));
            $query->whereDate('c.date', '<=', trim($enddate));
        }
        if (!empty($request->q)) {
            $search = $request->q;
            $query->where(function($q) use ($search) {
                $q->where('b.business_name', 'like', "%{$search}%")
                ->orWhere(DB::raw('LOWER(b.trustmark_id)'),'like',"%".strtolower($search)."%")
                ->orWhere(DB::raw('LOWER(a.fee_name)'),'like',"%".strtolower($search)."%")
                ->orWhere(DB::raw('LOWER(c.transaction_id)'),'like',"%".strtolower($search)."%")
                ->orWhere(DB::raw('LOWER(c.or_number)'),'like',"%".strtolower($search)."%")
                ->orWhere(DB::raw('LOWER(a.amount)'),'like',"%".strtolower($search)."%")
                ->orWhere(DB::raw('LOWER(a.create_date)'),'like',"%".strtolower($search)."%")
                ->orWhere(DB::raw('LOWER(d.name)'),'like',"%".strtolower($search)."%")
                ->orWhere(DB::raw('LOWER(b.payment_channel)'),'like',"%".strtolower($search)."%");
                });
        }
        $totalRecords = DB::table('business_fees as a')->count();
        $totalFiltered = $query->count();
        $limit = $request->input('length');   
        $start = $request->input('start');   
        $query->skip($start)->take($limit);
        $orderColumnIndex = $request->input('order.0.column');
        $orderDirection = $request->input('order.0.dir', 'asc'); 

        $columns = [
            0 => null,                      
            1 => 'b.business_name',   
            2 => 'b.trustmark_id',
            3 => 'a.fee_name',
            4 => 'c.transaction_id',
            5 => 'c.or_number',
            6 => 'a.amount',
            7 => 'a.create_date',
            8 => 'd.name'                   
        ];
        $orderColumn = $columns[$orderColumnIndex] ?? null;

        if (!empty($orderColumn)) {
            $query->orderBy($orderColumn, $orderDirection);
        }

        $fees = $query->get();
        $data = [];
        $i = $start + 1;

        foreach ($fees as $row) {
            $data[] = [
                'no' => $i++,
                    'BusinessName' => $row->BusinessName ?? ' ',
                    'SecurityNo' => $row->SecurityNo ?? ' ',
                    'PaymentDescription' => $row->PaymentDescription ?? ' ',
                    'TransactionID' => $row->TransactionID ?? ' ',
                    'OR_Number' => $row->or_number ?? ' ',
                    'Amount' => $row->Amount ?? ' ',
                    'Date' => $row->Date ?? ' ',
                    'payment_channel' => $row->payment_channel ?? ' ',
                    'PaymentBy' => $row->PaymentBy ?? ' '
            ];
        }

        return response()->json([
            'draw' => intval($request->input('draw')),
            'recordsTotal' => $totalRecords,
            'recordsFiltered' => $totalFiltered,
            'data' => $data
        ]);
    }

    
}
