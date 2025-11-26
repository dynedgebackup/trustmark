<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Provinces;
use Illuminate\Validation\Rule;
use File;
use Carbon\Carbon;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Support\Facades\Auth;
use DB;

class ProvincesController extends Controller
{
    public $arrapp_code = array(""=>"");
    public $arrfee_id = array(""=>"");
    public function __construct(){
		$this->Provinces = new Provinces(); 
        $this->data = array('id'=>'','prov_code'=>'','reg_no'=>'','prov_no'=>'','prov_desc'=>'','uacs_code'=>'','is_active'=>'');  
    }
    public function index()
    {
        return view('provinces.index');
    }
    
    public function regionAjaxList(Request $request){
        $search = $request->input('search');
        $arrRes = $this->Provinces->regionAjaxList($search);
        $arr = array();
        foreach ($arrRes['data'] as $key=>$val) {
            $arr['data'][$key]['id']=$val->id;
            $arr['data'][$key]['text']=$val->reg_region.'-'.$val->reg_description;
        }
        $arr['data_cnt']=$arrRes['data_cnt'];
        echo json_encode($arr);
    }
   
    public function ActiveInactive(Request $request){
        $id = $request->input('id');
        $is_activeinactive = $request->input('is_activeinactive');
        $data=array('status' => $is_activeinactive);
        $this->Provinces->updateActiveInactive($id,$data);
    }
    public function store(Request $request){
       $data = (object)$this->data;
       $arrapp_code = $this->arrapp_code;
       $arrfee_id = $this->arrfee_id;
        if($request->input('id')>0 && $request->input('submit')==""){
            $data = $this->Provinces->getEditDetails($request->input('id'));
            $arrapp_codes = $this->Provinces->getregionsDetails($data->reg_no);
            foreach ($arrapp_codes as $val) {
                $arrapp_code[$val->id] = $val->reg_region.'-'.$val->reg_description;
            }
            
        }
		
        if($request->input('submit')!=""){
            foreach((array)$this->data as $key=>$val){
                $this->data[$key] = $request->input($key);
            }
            
            $this->data['updated_by']=Auth::id();
            $this->data['updated_at'] = date('Y-m-d H:i:s');
            if($request->input('id')>0){
                $this->Provinces->updateData($request->input('id'),$this->data);
                $lastInsertedId = $request->input('id');
                $success_msg = 'Updated successfully.';
            }else{
                $this->data['created_by']=Auth::id();
                $this->data['created_at'] = date('Y-m-d H:i:s');
                $lastInsertedId = $this->Provinces->addData($this->data);
                $success_msg = 'Added successfully.';
                
            }
            return redirect()->route('provinces.index')->with('success', __($success_msg));

        }


        
        return view('provinces.create',compact('data','arrapp_code','arrfee_id'));
    }
   
    public function getList(Request $request)
    {
        $query = DB::table('provinces AS p')
            ->join('regions AS r', 'r.id', '=', 'p.reg_no')
            ->select('p.id','p.prov_desc','r.reg_region','p.is_active','r.reg_description');

            if ($request->filled('reg_no')) {
                $query->where('p.reg_no', $request->reg_no);
            }
            if ($request->filled('status')) {
                $query->where('p.is_active', $request->status);
            }
        if (!empty($request->q)) {
            $search = $request->q;
            $query->where(function($q) use ($search) {
                $q->where('p.prov_desc', 'like', "%{$search}%")
                ->orWhere(DB::raw('LOWER(r.reg_region)'),'like',"%".strtolower($search)."%")
                ->orWhere(DB::raw("CONCAT(r.reg_region, '-', r.reg_description)"), 'like', "%" . $search . "%");
                });
        }
        $totalRecords = DB::table('provinces AS p')->count();
        $totalFiltered = $query->count();
        $limit = $request->input('length');   
        $start = $request->input('start');   
        $query->skip($start)->take($limit);
        $orderColumnIndex = $request->input('order.0.column');
        $orderDirection = $request->input('order.0.dir', 'asc'); 

        $columns = [
            0 => null,                      
            1 => 'r.reg_region',   
            2 => 'p.prov_desc',
            3 =>  'p.is_active',
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
                    'reg_region' => $row->reg_region.'-'.$row->reg_description ?? '-',
                    'prov_desc' => $row->prov_desc,
                    'status' => ($row->is_active==1?'<span class="btn btn-success" style="padding: 0.1rem 0.5rem !important;background: none;font-size: 12px;border-radius: 11px;">Active</span>
                                ':'<span class="btn btn-warning" style="padding: 0.1rem 0.5rem !important;background: none;font-size: 12px;border-radius: 11px;">Cancel</span>'),
                    'action' => '<a href="#" 
                                class="mx-3 btn btn-sm align-items-center" 
                                data-url="' . url('/location/provinces/store?id=' . $row->id) . '" 
                                data-ajax-popup="true" 
                                data-size="lg" 
                                data-bs-toggle="tooltip" 
                                title="Edit" 
                                data-title="Manage Provinces" style="background: #09325d !important;color: #fff;">
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
