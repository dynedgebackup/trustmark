<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Evaluator;
use Illuminate\Validation\Rule;
use File;
use Carbon\Carbon;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Support\Facades\Auth;
use DB;

class EvaluatorController extends Controller
{
    public $users = array(""=>"");
    public function __construct(){
		$this->Evaluator = new Evaluator(); 
        $this->data = array('id'=>'','user_id'=>'','is_admin'=>'','is_evaluator'=>'');  
    }
    public function index()
    {
        return view('Evaluator.index');
    }
    public function userAjaxList(Request $request){
        $search = $request->input('search');
        $arrRes = $this->Evaluator->userAjaxList($search);
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
        return view('Evaluator.create');
    }
    public function ActiveInactive(Request $request){
        $id = $request->input('id');
        $is_activeinactive = $request->input('is_activeinactive');
        $data=array('status' => $is_activeinactive);
        $this->Offices->updateActiveInactive($id,$data);
    }
    public function store(Request $request){
       $data = (object)$this->data;
       $users = $this->users;
        if($request->input('id')>0 && $request->input('submit')==""){
            $data = $this->Evaluator->getEditDetails($request->input('id'));
            $user = $this->Evaluator->getuserDetails($data->user_id);
            foreach ($user as $val) {
                $users[$val->id] = $val->name;
            }
        }
		
        if($request->input('submit')!=""){
            foreach((array)$this->data as $key=>$val){
                $this->data[$key] = $request->input($key);
            }
            
            $this->data['is_admin'] = $request->input('is_admin')?1:0;
            $this->data['is_evaluator'] = $request->input('is_evaluator')?1:0;
            $this->data['modified_by']=Auth::id();
            $this->data['modified_date'] = date('Y-m-d H:i:s');
            if($request->input('id')>0){
                $this->Evaluator->updateData($request->input('id'),$this->data);
                $lastInsertedId = $request->input('id');
                $success_msg = 'Updated successfully.';
            }else{
                $this->data['created_by']=Auth::id();
                $this->data['created_date'] = date('Y-m-d H:i:s');
                $lastInsertedId = $this->Evaluator->addData($this->data);
                $success_msg = 'Added successfully.';
                
            }
            return redirect()->route('evaluator.index')->with('success', __($success_msg));

        }


        
        return view('Evaluator.create',compact('data','users'));
    }
   
    public function getList(Request $request)
    {
        $query = DB::table('user_admins AS a')
            ->join('users AS b', 'b.id', '=', 'a.user_id')
            ->select(
                'a.id',
                'b.name AS full_name',
                'b.email',
                'b.ctc_no',
                DB::raw("(CASE a.is_admin WHEN 0 THEN '' WHEN 1 THEN 'Yes' END) AS is_admin"),
                DB::raw("(CASE a.is_evaluator WHEN 0 THEN '' WHEN 1 THEN 'Yes' END) AS is_evaluator")
            )
            ->where('b.is_active', 1);
        if (!empty($request->q)) {
            $search = strtolower($request->q);
            $query->where(function ($q) use ($search) {
                $q->where(DB::raw('LOWER(b.name)'), 'like', "%{$search}%")
                ->orWhere(DB::raw('LOWER(b.email)'), 'like', "%{$search}%")
                ->orWhere(DB::raw('LOWER(b.ctc_no)'), 'like', "%{$search}%");
            });
        }
        $totalRecords = DB::table('user_admins AS a')
            ->join('users AS b', 'b.id', '=', 'a.user_id')
            ->where('b.is_active', 1)
            ->count();

        $totalFiltered = $query->count();

        $limit = $request->input('length');
        $start = $request->input('start');

        $query->skip($start)->take($limit);
        $orderColumnIndex = $request->input('order.0.column');
        $orderDirection = $request->input('order.0.dir', 'asc');

        $columns = [
            0 => null,
            1 => 'full_name',
            2 => 'email',
            3 => 'ctc_no',
            4 => 'is_admin',
            5 => 'is_evaluator',
            6 => null,
        ];

        $orderColumn = $columns[$orderColumnIndex] ?? 'full_name';
        if (!empty($orderColumn)) {
            $query->orderBy($orderColumn, $orderDirection);
        }

        $users = $query->get();
        $data = [];
        $i = $start + 1;

        foreach ($users as $row) {
            $data[] = [
                'no'          => $i++,
                'full_name'   => $row->full_name,
                'email'       => $row->email,
                'contact_no'  => $row->ctc_no ?? '-',
                'is_admin'    => $row->is_admin == 'Yes'
                    ? '<span class="badge bg-success">Yes</span>'
                    : '<span class="badge bg-secondary">No</span>',
                'is_evaluator'=> $row->is_evaluator == 'Yes'
                    ? '<span class="badge bg-success">Yes</span>'
                    : '<span class="badge bg-secondary">No</span>',
                    'action' => '<a href="#" 
                                class="mx-3 btn btn-sm align-items-center" 
                                data-url="' . url('/user/evaluator/store?id=' . $row->id) . '" 
                                data-ajax-popup="true" 
                                data-size="lg" 
                                data-bs-toggle="tooltip" 
                                title="Edit" 
                                data-title="Manage Admin and Evaluator" style="background: #09325d !important;color: #fff;">
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
