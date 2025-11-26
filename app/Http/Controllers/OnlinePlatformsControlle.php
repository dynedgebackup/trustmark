<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Onlineplatforms;
use Illuminate\Validation\Rule;
use Illuminate\Support\Facades\File;
use Carbon\Carbon;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use DB;

class OnlinePlatformsControlle extends Controller
{
    public $arrapp_code = array(""=>"");
    public $arrfee_id = array(""=>"");
    public function __construct(){
        $this->Onlineplatforms = new Onlineplatforms(); 
        $this->data = array('id'=>'','base_url'=>'','platform_name'=>'','platform_logo'=>'','with_irm'=>'');  
    }
    public function index()
    {
        return view('Onlineplatforms.index');
    }
    public function ActiveInactive(Request $request){
        $id = $request->input('id');
        $is_activeinactive = $request->input('is_activeinactive');
        $data=array('is_active' => $is_activeinactive);
        $this->Onlineplatforms->updateActiveInactive($id,$data);
    }

    public function store(Request $request){
       $data = (object)$this->data;
      
        if($request->input('id')>0 && $request->input('submit')==""){
            $data = $this->Onlineplatforms->getEditDetails($request->input('id'));
        }
        
        if($request->input('submit')!=""){
            foreach((array)$this->data as $key=>$val){
                $this->data[$key] = $request->input($key);
            }
            $with_irm = $request->input('with_irm')?1:0;
            $this->data['with_irm']=$with_irm;
            $this->data['modified_by']=Auth::id();
            $this->data['modified_date'] = date('Y-m-d H:i:s');
             if ($request->hasFile('platform_logo')) {
                $file = $request->file('platform_logo');
                if ($file->isValid()) {
                $originalName = pathinfo($file->getClientOriginalName(), PATHINFO_FILENAME);
                $extension = $file->getClientOriginalExtension();

                $filename = now()->format('YmdHis').'_'.preg_replace('/[^A-Za-z0-9_\-]/', '', $originalName).'.'.$extension;

                $path = $file->storeAs('document-upload/online_platform', $filename, 'public');

                $this->data['platform_logo'] = $path;
               }
            }
            if($request->input('id')>0){
                $this->Onlineplatforms->updateData($request->input('id'),$this->data);
                $lastInsertedId = $request->input('id');
                $success_msg = 'Updated successfully.';
            }else{
                $this->data['created_by']=Auth::id();
                $this->data['created_date'] = date('Y-m-d H:i:s');
                $lastInsertedId = $this->Onlineplatforms->addData($this->data);
                $success_msg = 'Added successfully.';
                
            }
            return redirect()->route('onlineplatforms.index')->with('success', __($success_msg));

        }
        return view('Onlineplatforms.create',compact('data'));
    }
    public function getList(Request $request)
    {
        $query = DB::table('platform_url AS a')->select([
                'a.base_url as Platform_Link_URL',
                'a.platform_name as Name',
                'a.platform_logo',
                'a.id','a.is_active',
                DB::raw("(CASE a.with_irm
                    WHEN 0 THEN 'No'
                    WHEN 1 THEN 'Yes'
                 END) as With_IRM"),
                DB::raw("(CASE a.is_active
                    WHEN 0 THEN 'Cancelled'
                    WHEN 1 THEN 'Active'
                 END) as Status"),
            ]);
          
        if (!empty($request->q)) {
            $search = $request->q;
            $query->where(function($q) use ($search) {
                $q->where('a.base_url', 'like', "%{$search}%")
                ->orWhere(DB::raw('LOWER(a.platform_name)'),'like',"%".strtolower($search)."%");
                });
        }
        $totalRecords = DB::table('platform_url AS a')->count();
        $totalFiltered = $query->count();
        $limit = $request->input('length');   
        $start = $request->input('start');   
        $query->skip($start)->take($limit);
        $orderColumnIndex = $request->input('order.1.column');
        $orderDirection = $request->input('order.1.dir', 'asc'); 

        $columns = [
            0 => null,                      
            1 => 'base_url',   
            2 => 'platform_name',
            3 => 'logo',
            4 => 'with_irm',
            5 => null
                            
        ];
        $orderColumn = $columns[$orderColumnIndex] ?? null;

        if (!empty($orderColumn)) {
            $query->orderBy($orderColumn, $orderDirection);
        }

        $paltform = $query->get();
        $data = [];
        $i = $start + 1;

        foreach ($paltform as $row) {
             $status = ($row->is_active == 1)
                    ? '<a href="#" class="mx-1 btn btn-sm align-items-center activeinactive" 
                          name="stp_print" value="0" id="' . $row->id . '" style="background-color: #dc3545; color: #fff;">
                          <i class="fas fa-trash"></i>
                       </a>'
                    : '<a href="#" class="mx-1 btn btn-sm align-items-center activeinactive" 
                          name="stp_print" value="1" id="' . $row->id . '" style="background-color: #28a745; color: #fff;">
                          <i class="fas fa-redo"></i>
                       </a>';
            $data[] = [
                'no' => $i++,
                    'platform_link' => $row->Platform_Link_URL ?? '-',
                    'platform_name' => $row->Name,
                   // 'logo' => '<img src="' . asset('storage/' . $row->platform_logo) . '" width="30px" height="30px">',
                    'logo' => ($row->is_active=='1'?'<span class="btn btn-success" style="padding: 0.1rem 0.5rem !important;background: none;font-size: 12px;border-radius: 11px;">Active</span>
                                ':'<span class="btn btn-warning" style="padding: 0.1rem 0.5rem !important;background: none;font-size: 12px;border-radius: 11px;">Cancelled</span>'),
                    'withirm' => ($row->With_IRM=='Yes'?'<span class="btn btn-success" style="padding: 0.1rem 0.5rem !important;background: none;font-size: 12px;border-radius: 11px;">Yes</span>
                                ':'<span class="btn btn-warning" style="padding: 0.1rem 0.5rem !important;background: none;font-size: 12px;border-radius: 11px;">No</span>'),
                    'action' => '<a href="#" 
                                class="mx-1 btn btn-sm align-items-center" 
                                data-url="' . url('/master-data/onlineplatforms/store?id=' . $row->id) . '" 
                                data-ajax-popup="true" 
                                data-size="lg" 
                                data-bs-toggle="tooltip" 
                                title="Edit" 
                                data-title="Manage Online Platform" style="background: #09325d !important;color: #fff;">
                                    <i class="fas fa-pencil "></i>
                                </a>'.$status,
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
