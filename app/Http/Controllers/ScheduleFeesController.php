<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\ScheduleFees;
use Illuminate\Validation\Rule;
use File;
use Carbon\Carbon;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Support\Facades\Auth;
use DB;

class ScheduleFeesController extends Controller
{
    public $arrapp_code = array(""=>"");
    public $arrfee_id = array(""=>"");
    public function __construct(){
		$this->ScheduleFees = new ScheduleFees(); 
        $this->data = array('id'=>'','app_code'=>'','fee_id'=>'','amount'=>'','status'=>'','exclude_due_to_bmbe'=>'','is_application_fee'=>'');  
    }
    public function index()
    {
        return view('ScheduleFees.index');
    }
    public function AppcodeAjaxList(Request $request){
        $search = $request->input('search');
        $arrRes = $this->ScheduleFees->AppcodeAjaxList($search);
        $arr = array();
        foreach ($arrRes['data'] as $key=>$val) {
            $arr['data'][$key]['id']=$val->id;
            $arr['data'][$key]['text']=$val->name;
        }
        $arr['data_cnt']=$arrRes['data_cnt'];
        echo json_encode($arr);
    }
    public function feesAjaxList(Request $request){
        $search = $request->input('search');
        $arrRes = $this->ScheduleFees->feesAjaxList($search);
        $arr = array();
        foreach ($arrRes['data'] as $key=>$val) {
            $arr['data'][$key]['id']=$val->id;
            $arr['data'][$key]['text']=$val->name;
        }
        $arr['data_cnt']=$arrRes['data_cnt'];
        echo json_encode($arr);
    }
    public function create()
    {
        return view('ScheduleFees.create');
    }
    public function ActiveInactive(Request $request){
        $id = $request->input('id');
        $is_activeinactive = $request->input('is_activeinactive');
        $data=array('status' => $is_activeinactive);
        $this->Offices->updateActiveInactive($id,$data);
    }
    public function store(Request $request){
       $data = (object)$this->data;
       $arrapp_code = $this->arrapp_code;
       $arrfee_id = $this->arrfee_id;
        if($request->input('id')>0 && $request->input('submit')==""){
            $data = $this->ScheduleFees->getEditDetails($request->input('id'));
            $arrapp_codes = $this->ScheduleFees->getAppcodeDetails($data->app_code);
            foreach ($arrapp_codes as $val) {
                $arrapp_code[$val->id] = $val->name;
            }
            $arrfee_ids = $this->ScheduleFees->getfeesDetails($data->fee_id);
            foreach ($arrfee_ids as $val) {
                $arrfee_id[$val->id] = $val->name;
            }
        }
		
        if($request->input('submit')!=""){
            foreach((array)$this->data as $key=>$val){
                $this->data[$key] = $request->input($key);
            }
            
            $app_code = $request->input('app_code');
            $exclude_due_to_bmbe = $request->input('exclude_due_to_bmbe')?1:0;
            $is_application_fee = $request->input('is_application_fee')?1:0;
            $fee_id = $request->input('fee_id');
            $fee_name = $this->ScheduleFees->getfeesname($fee_id);
            $app_name = $this->ScheduleFees->getAppname($app_code);
            $this->data['app_name']=$app_name->name;
            $this->data['fee_name']=$fee_name->name;
            $this->data['is_application_fee']=$is_application_fee;
            $this->data['exclude_due_to_bmbe']=$exclude_due_to_bmbe;
            $this->data['modified_by']=Auth::id();
            $this->data['modified_date'] = date('Y-m-d H:i:s');
            
            if($request->input('id')>0){
                $this->ScheduleFees->updateData($request->input('id'),$this->data);
                $lastInsertedId = $request->input('id');
                $success_msg = 'Updated successfully.';
            }else{
                $this->data['created_by']=Auth::id();
                $this->data['create_date'] = date('Y-m-d H:i:s');
                $lastInsertedId = $this->ScheduleFees->addData($this->data);
                $success_msg = 'Added successfully.';
                
            }
            if ($is_application_fee == 1) {
                $categories = DB::table('business_category')->get();

                $feeIds     = $request->input('categoryamountfee_id', []); 
                $amounts    = $request->input('categoryamount', []); 
                $defaultId  = $request->input('is_default'); 
                
                foreach ($categories as $index => $category) {
                    $feeId  = $feeIds[$index] ?? null;
                    $amount = $amounts[$index] ?? 0;
                    $isDefaultValue = ($feeId == $defaultId) ? 1 : 0;
                
                    if ($feeId) {
                        DB::table('application_fee_category')
                            ->where('id', $feeId)
                            ->update([
                                'amount'      => $amount,
                                'is_default'  => $isDefaultValue,
                            ]);
                    } else {
                        DB::table('application_fee_category')->insert([
                            'application_fee_id' => $lastInsertedId,
                            'busn_category_id'   => $category->id,
                            'busn_category_name' => $category->name,
                            'amount'             => $amount,
                            'is_default'         => ($defaultId == $category->id) ? 1 : 0,
                            'created_by'         => Auth::id(),
                            'created_date'       => now(),
                        ]);
                    }
                }
            } else {
                DB::table('application_fee_category')
                    ->where('application_fee_id', $lastInsertedId)
                    ->delete();
            }
            
            
            
            return redirect()->route('scheduleFees.index')->with('success', __($success_msg));

        }

        $application_fee_category = DB::table('application_fee_category')->where('application_fee_id', $request->input('id'))->get();
        
        return view('ScheduleFees.create',compact('data','arrapp_code','arrfee_id','application_fee_category'));
    }
   
    public function getList(Request $request)
    {
        $query = DB::table('application_fees AS af')
            ->join('application_code AS ac', 'ac.id', '=', 'af.app_code')
            ->join('fees AS f', 'f.id', '=', 'af.fee_id')
            ->select('af.id','ac.name AS application_name','f.name AS fees_name','af.amount','af.exclude_due_to_bmbe','af.is_application_fee','af.status');

            if ($request->filled('app_code')) {
                $query->where('af.app_code', $request->app_code);
            }
            if ($request->filled('status')) {
                $query->where('af.status', $request->status);
            }
        if (!empty($request->q)) {
            $search = $request->q;
            $query->where(function($q) use ($search) {
                $q->where('ac.name', 'like', "%{$search}%")
                ->orWhere(DB::raw('LOWER(f.name)'),'like',"%".strtolower($search)."%")
                ->orWhere(DB::raw('LOWER(af.amount)'),'like',"%".strtolower($search)."%");
                });
        }
        $totalRecords = DB::table('application_fees AS af')->count();
        $totalFiltered = $query->count();
        $limit = $request->input('length');   
        $start = $request->input('start');   
        $query->skip($start)->take($limit);
        $orderColumnIndex = $request->input('order.0.column');
        $orderDirection = $request->input('order.0.dir', 'asc'); 

        $columns = [
            0 => null,                      
            1 => 'application_name',   
            2 => 'fees_name',
            3 => 'amount',
            4 => 'is_application_fee',
            5 => 'exclude_due_to_bmbe',
            6 => 'status',
            7 => null                    
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
                    'application_type' => $row->application_name ?? '-',
                    'fee_description' => $row->fees_name,
                    'amount' => number_format($row->amount, 2),
                    'is_application_fee' => ($row->is_application_fee==1?'<span class="btn btn-success" style="padding: 0.1rem 0.5rem !important;background: none;font-size: 12px;border-radius: 11px;">Yes</span>
                                ':'<span class="btn btn-warning" style="padding: 0.1rem 0.5rem !important;background: none;font-size: 12px;border-radius: 11px;">No</span>'),
                    'exclude_due_to_bmbe' => ($row->exclude_due_to_bmbe==1?'<span class="btn btn-success" style="padding: 0.1rem 0.5rem !important;background: none;font-size: 12px;border-radius: 11px;">Yes</span>
                                ':'<span class="btn btn-warning" style="padding: 0.1rem 0.5rem !important;background: none;font-size: 12px;border-radius: 11px;">No</span>'),
                    'status' => ($row->status==1?'<span class="btn btn-success" style="padding: 0.1rem 0.5rem !important;background: none;font-size: 12px;border-radius: 11px;">Active</span>
                                ':'<span class="btn btn-warning" style="padding: 0.1rem 0.5rem !important;background: none;font-size: 12px;border-radius: 11px;">Cancel</span>'),
                    'action' => '<a href="#" 
                                class="mx-3 btn btn-sm align-items-center" 
                                data-url="' . url('/master-data/scheduleFees/store?id=' . $row->id) . '" 
                                data-ajax-popup="true" 
                                data-size="lg" 
                                data-bs-toggle="tooltip" 
                                title="Edit" 
                                data-title="Manage Schedule of Fees" style="background: #09325d !important;color: #fff;">
                                    <i class="fas fa-pencil "></i>
                                </a>'
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
