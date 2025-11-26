<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Department;
use Illuminate\Validation\Rule;
use File;
use Carbon\Carbon;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Support\Facades\Auth;
use DB;

class DepartmentController extends Controller
{
    public $arrapp_code = array(""=>"");
    public $arrfee_id = array(""=>"");
    public function __construct(){
		$this->Department = new Department(); 
        $this->data = array('id'=>'','name'=>'','head_id'=>'','position'=>'','is_primary'=>'','remarks'=>'','status'=>'');  
    }
    public function index()
    {
        return view('department.index');
    }
    
    public function getUserAjaxList(Request $request){
        $search = $request->input('search');
        $arrRes = $this->Department->getUserAjaxList($search);
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
        $this->Department->updateActiveInactive($id,$data);
    }
    public function store(Request $request){
       $data = (object)$this->data;
       $arrapp_code = $this->arrapp_code;
       $arrfee_id = $this->arrfee_id;
        if($request->input('id')>0 && $request->input('submit')==""){
            $data = $this->Department->getEditDetails($request->input('id'));
            $arrapp_codes = $this->Department->getUserDetails($data->head_id);
            foreach ($arrapp_codes as $val) {
                $arrapp_code[$val->id] = $val->name;
            }
            
        }
		
        if($request->input('submit')!=""){
            foreach((array)$this->data as $key=>$val){
                $this->data[$key] = $request->input($key);
            }
            if ($request->input('is_primary') == 1) {
                DB::table('departments')
                    ->where('id', '!=', $request->input('id'))
                    ->update(['is_primary' => 0]);
            }
            $this->data['modified_by']=Auth::id();
            $this->data['modified_date'] = date('Y-m-d H:i:s');
            if($request->input('id')>0){
                $this->Department->updateData($request->input('id'),$this->data);
                $lastInsertedId = $request->input('id');
                $success_msg = 'Updated successfully.';
            }else{
                $this->data['created_by']=Auth::id();
                $this->data['created_date'] = date('Y-m-d H:i:s');
                $lastInsertedId = $this->Department->addData($this->data);
                $success_msg = 'Added successfully.';
                
            }
            return redirect()->route('department.index')->with('success', __($success_msg));

        }


        
        return view('department.create',compact('data','arrapp_code','arrfee_id'));
    }
   
    public function getList(Request $request)
    {
        $query = DB::table('departments AS d')
            ->Leftjoin('users AS u', 'u.id', '=', 'd.head_id')
            ->select('d.id','u.name AS head_name','d.remarks','d.name','d.status');

            
            if ($request->filled('status')) {
                $query->where('d.status', $request->status);
            }
        if (!empty($request->q)) {
            $search = $request->q;
            $query->where(function($q) use ($search) {
                $q->where('u.name', 'like', "%{$search}%")
                ->orWhere(DB::raw('LOWER(d.remarks)'),'like',"%".strtolower($search)."%")
                ->orWhere(DB::raw('LOWER(d.name)'),'like',"%".strtolower($search)."%");
                });
        }
        $totalRecords = DB::table('departments AS d')->count();
        $totalFiltered = $query->count();
        $limit = $request->input('length');   
        $start = $request->input('start');   
        $query->skip($start)->take($limit);
        $orderColumnIndex = $request->input('order.0.column');
        $orderDirection = $request->input('order.0.dir', 'asc'); 

        $columns = [
            0 => null,                      
            1 => 'd.name',   
            2 => 'u.name',
            3 => 'd.remarks',
            3 => 'd.status',
            4 => null                    
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
                    'name' => $row->name ?? '-',
                    'head_name' => $row->head_name,
                    'remarks' => $row->remarks,
                    'status' => ($row->status==1?'<span class="btn btn-success" style="padding: 0.1rem 0.5rem !important;background: none;font-size: 12px;border-radius: 11px;">Active</span>
                                ':'<span class="btn btn-warning" style="padding: 0.1rem 0.5rem !important;background: none;font-size: 12px;border-radius: 11px;">Cancel</span>'),
                    'action' => '<a href="#" 
                                class="mx-3 btn btn-sm align-items-center" 
                                data-url="' . url('/security/department/store?id=' . $row->id) . '" 
                                data-ajax-popup="true" 
                                data-size="lg" 
                                data-bs-toggle="tooltip" 
                                title="Edit" 
                                data-title="Manage Department" style="background: #09325d !important;color: #fff;">
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
