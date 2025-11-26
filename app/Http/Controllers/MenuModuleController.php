<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\MenuModule;
use Illuminate\Validation\Rule;
use File;
use Carbon\Carbon;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Support\Facades\Auth;
use DB;

class MenuModuleController extends Controller
{
    public $arrapp_code = array(""=>"");
    public $arrfee_id = array(""=>"");
    public function __construct(){
		$this->MenuModule = new MenuModule(); 
        $this->data = array('id'=>'','menu_group_id'=>'','code'=>'','name'=>'','description'=>'','slug'=>'','is_active'=>'');   
    }
    public function index()
    {
        return view('MenuModule.index');
    }
    
    public function getmenuGroupAjaxList(Request $request){
        $search = $request->input('search');
        $arrRes = $this->MenuModule->getmenuGroupAjaxList($search);
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
        $this->MenuModule->updateActiveInactive($id,$data);
    }
    public function store(Request $request){
       $data = (object)$this->data;
       $arrapp_code = $this->arrapp_code;
       $arrfee_id = $this->arrfee_id;
        if($request->input('id')>0 && $request->input('submit')==""){
            $data = $this->MenuModule->getEditDetails($request->input('id'));
            $arrapp_codes = $this->MenuModule->getmenu_groupsDetails($data->menu_group_id);
            foreach ($arrapp_codes as $val) {
                $arrapp_code[$val->id] = $val->name;
            }
            
        }
		
        if($request->input('submit')!=""){
            foreach((array)$this->data as $key=>$val){
                $this->data[$key] = $request->input($key);
            }
            
            $this->data['updated_by']=Auth::id();
            $this->data['updated_at'] = date('Y-m-d H:i:s');
            if($request->input('id')>0){
                $this->MenuModule->updateData($request->input('id'),$this->data);
                $lastInsertedId = $request->input('id');
                $success_msg = 'Updated successfully.';
            }else{
                $this->data['created_by']=Auth::id();
                $this->data['created_at'] = date('Y-m-d H:i:s');
                $lastInsertedId = $this->MenuModule->addData($this->data);
                $success_msg = 'Added successfully.';
                
            }
            return redirect()->route('MenuModule.index')->with('success', __($success_msg));

        }


        
        return view('MenuModule.create',compact('data','arrapp_code','arrfee_id'));
    }
   
    public function getList(Request $request)
    {
        $query = DB::table('menu_modules AS mm')
            ->Leftjoin('menu_groups AS mg', 'mg.id', '=', 'mm.menu_group_id')
            ->select('mm.id','mg.name AS group_name','mm.name','mm.code','mm.description','mm.slug','mm.is_active');

            if ($request->filled('menu_group_id')) {
                $query->where('mm.menu_group_id', $request->menu_group_id);
            }
            if ($request->filled('status')) {
                $query->where('mm.is_active', $request->status);
            }
        if (!empty($request->q)) {
            $search = $request->q;
            $query->where(function($q) use ($search) {
                $q->where('mg.name', 'like', "%{$search}%")
                ->orWhere(DB::raw('LOWER(mm.name)'),'like',"%".strtolower($search)."%")
                ->orWhere(DB::raw('LOWER(mm.code)'),'like',"%".strtolower($search)."%")
                ->orWhere(DB::raw('LOWER(mm.description)'),'like',"%".strtolower($search)."%")
                ->orWhere(DB::raw('LOWER(mm.slug)'),'like',"%".strtolower($search)."%")
                ->orWhere(DB::raw('LOWER(mm.is_active)'),'like',"%".strtolower($search)."%");
                });
        }
        $totalRecords = DB::table('menu_modules AS d')->count();
        $totalFiltered = $query->count();
        $limit = $request->input('length');   
        $start = $request->input('start');   
        $query->skip($start)->take($limit);
        $orderColumnIndex = $request->input('order.0.column');
        $orderDirection = $request->input('order.0.dir', 'asc'); 

        $columns = [
            0 => null,                      
            1 => 'mg.name',   
            2 => 'mm.name',
            3 => 'mm.code',
            4 => 'mm.description',
            5 => 'mm.slug',
            6 => 'mm.is_active',
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
                    'group_name' => $row->group_name ?? '-',
                    'code' => $row->code,
                    'name' => $row->name,
                    'description' => $row->description,
                    'slug' => $row->slug,
                    'status' => ($row->is_active==1?'<span class="btn btn-success" style="padding: 0.1rem 0.5rem !important;background: none;font-size: 12px;border-radius: 11px;">Active</span>
                                ':'<span class="btn btn-warning" style="padding: 0.1rem 0.5rem !important;background: none;font-size: 12px;border-radius: 11px;">Cancel</span>'),
                    'action' => '<a href="#" 
                                class="mx-3 btn btn-sm align-items-center" 
                                data-url="' . url('/setting/MenuModule/store?id=' . $row->id) . '" 
                                data-ajax-popup="true" 
                                data-size="lg" 
                                data-bs-toggle="tooltip" 
                                title="Edit" 
                                data-title="Manage Menu Module" style="background: #09325d !important;color: #fff;">
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
