<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\AdminBarangay;
use Illuminate\Validation\Rule;
use File;
use Carbon\Carbon;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Support\Facades\Auth;
use DB;

class AdminBarangayController extends Controller
{
    public $arrapp_code = array(""=>"");
    public $arrfee_id = array(""=>"");
    public function __construct(){
		$this->AdminBarangay = new AdminBarangay(); 
        $this->data = array('id'=>'','brgy_code'=>'','reg_no'=>'','reg_region'=>'','prov_no'=>'','prov_desc'=>'',
        'mun_no'=>'','mun_desc'=>'','brgy_name'=>'','is_active'=>'');  
    }
    public function index()
    {
        return view('barangay.index');
    }
    public function getBarngayMunProvRegionAjaxList(Request $request){
        $search = $request->input('search');
        $arrRes = $this->AdminBarangay->getBarngayMunProvRegionAjaxList($search);
        $arr = array();
        foreach ($arrRes['data'] as $key=>$val) {
            $arr['data'][$key]['id']=$val->id;
            $arr['data'][$key]['text']=$val->mun_desc.", ".$val->prov_desc.", ".$val->reg_region;
        }
        $arr['data_cnt']=$arrRes['data_cnt'];
        echo json_encode($arr);
    }
    public function ActiveInactive(Request $request){
        $id = $request->input('id');
        $is_activeinactive = $request->input('is_activeinactive');
        $data=array('status' => $is_activeinactive);
        $this->AdminBarangay->updateActiveInactive($id,$data);
    }
    public function store(Request $request){
       $data = (object)$this->data;
       $arrapp_code = $this->arrapp_code;
       $arrfee_id = $this->arrfee_id;
        if($request->input('id')>0 && $request->input('submit')==""){
            $data = $this->AdminBarangay->getEditDetails($request->input('id'));
            $arrapp_codes = $this->AdminBarangay->getregionsDetails($data->mun_no);
            foreach ($arrapp_codes as $val) {
                $arrapp_code[$val->id] = $val->mun_desc.", ".$val->prov_desc.", ".$val->reg_region;
            }
            
        }
		
        if($request->input('submit')!=""){
            foreach((array)$this->data as $key=>$val){
                $this->data[$key] = $request->input($key);
            }

            $mun_no = $request->input('mun_no');
            $reg_no = $this->AdminBarangay->getRegionId($mun_no);
            $reg_region = $this->AdminBarangay->getRegionName($reg_no->reg_no);
            $prov_desc = $this->AdminBarangay->getProvincesName($reg_no->prov_no);

            $this->data['reg_no']=$reg_no->reg_no;
            $this->data['reg_region']=$reg_region->reg_region;
            $this->data['prov_no']=$reg_no->prov_no;
            $this->data['prov_desc']=$prov_desc->prov_desc;
            $this->data['mun_desc']=$reg_no->mun_desc;

            
            $this->data['brgy_display_for_bplo']=0;
            $this->data['updated_by']=Auth::id();
            $this->data['updated_at'] = date('Y-m-d H:i:s');
            if($request->input('id')>0){
                $this->AdminBarangay->updateData($request->input('id'),$this->data);
                $lastInsertedId = $request->input('id');
                $success_msg = 'Updated successfully.';
            }else{
                $this->data['created_by']=Auth::id();
                $this->data['created_at'] = date('Y-m-d H:i:s');
                $lastInsertedId = $this->AdminBarangay->addData($this->data);
                $success_msg = 'Added successfully.';
                
            }
            return redirect()->route('barangay.index')->with('success', __($success_msg));

        }


        
        return view('barangay.create',compact('data','arrapp_code','arrfee_id'));
    }
   
    public function getList(Request $request)
    {
        $query = DB::table('barangays AS bgf')
        ->join('regions AS pr', 'pr.id', '=', 'bgf.reg_no')
        ->join('provinces AS pp', 'pp.id', '=', 'bgf.prov_no')
        ->join('municipalities AS pm', 'pm.id', '=', 'bgf.mun_no')
        ->select('bgf.id','pm.mun_desc','pp.prov_desc','pr.reg_region','pr.reg_description','brgy_code','brgy_name','bgf.is_active');

            if ($request->filled('mun_no')) {
                $query->where('bgf.mun_no', $request->mun_no);
            }
            if ($request->filled('status')) {
                $query->where('bgf.is_active', $request->status);
            }
        if (!empty($request->q)) {
            $search = $request->q;
            $query->where(function($q) use ($search) {
                $q->where('pp.prov_desc', 'like', "%{$search}%")
                ->orWhere(DB::raw('LOWER(pr.reg_region)'),'like',"%".strtolower($search)."%")
                ->orWhere(DB::raw('LOWER(pm.mun_desc)'),'like',"%".strtolower($search)."%")
                ->orWhere(DB::raw('LOWER(brgy_name)'),'like',"%".strtolower($search)."%")
                ->orWhere(DB::raw("CONCAT(pr.reg_region, '-', pr.reg_description)"), 'like', "%" . $search . "%");
                });
        }
        $totalRecords = DB::table('barangays AS bgf')->count();
        $totalFiltered = $query->count();
        $limit = $request->input('length');   
        $start = $request->input('start');   
        $query->skip($start)->take($limit);
        $orderColumnIndex = $request->input('order.0.column');
        $orderDirection = $request->input('order.0.dir', 'asc'); 

        $columns = [
            0 => null,                      
            1 => 'pr.reg_region',   
            2 => 'pp.prov_desc',
            3 => 'pm.mun_desc',
            4 => 'brgy_name',
            5 => 'mbgf.is_active',
            6 => null                    
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
                    'mun_desc' => $row->mun_desc,
                    'brgy_name' => $row->brgy_name,
                    'status' => ($row->is_active==1?'<span class="btn btn-success" style="padding: 0.1rem 0.5rem !important;background: none;font-size: 12px;border-radius: 11px;">Active</span>
                                ':'<span class="btn btn-warning" style="padding: 0.1rem 0.5rem !important;background: none;font-size: 12px;border-radius: 11px;">Cancel</span>'),
                    'action' => '<a href="#" 
                                class="mx-3 btn btn-sm align-items-center" 
                                data-url="' . url('/location/barangay/store?id=' . $row->id) . '" 
                                data-ajax-popup="true" 
                                data-size="lg" 
                                data-bs-toggle="tooltip" 
                                title="Edit" 
                                data-title="Manage Barangay" style="background: #09325d !important;color: #fff;">
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
