<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\FeesDescription;
use Illuminate\Validation\Rule;
use File;
use Carbon\Carbon;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Support\Facades\Auth;
use DB;

class FeesDescriptionController extends Controller
{
    public $arrapp_code = array(""=>"");
    public $arrfee_id = array(""=>"");
    public function __construct(){
		$this->FeesDescription = new FeesDescription(); 
        $this->data = array('id'=>'','name'=>'','amount'=>'');  
    }
    public function index()
    {
        return view('feesDescription.index');
    }
    
    public function ActiveInactive(Request $request){
        $id = $request->input('id');
        $is_activeinactive = $request->input('is_activeinactive');
        $data=array('status' => $is_activeinactive);
        $this->FeesDescription->updateActiveInactive($id,$data);
    }
    public function store(Request $request){
       $data = (object)$this->data;
       $arrapp_code = $this->arrapp_code;
       $arrfee_id = $this->arrfee_id;
        if($request->input('id')>0 && $request->input('submit')==""){
            $data = $this->FeesDescription->getEditDetails($request->input('id'));
            
        }
		
        if($request->input('submit')!=""){
            foreach((array)$this->data as $key=>$val){
                $this->data[$key] = $request->input($key);
            }
            
            $this->data['modified_by']=Auth::id();
            $this->data['modified_date'] = date('Y-m-d H:i:s');
            if($request->input('id')>0){
                $this->FeesDescription->updateData($request->input('id'),$this->data);
                $lastInsertedId = $request->input('id');
                $success_msg = 'Updated successfully.';
            }else{
                $this->data['created_by']=Auth::id();
                $this->data['create_date'] = date('Y-m-d H:i:s');
                $lastInsertedId = $this->FeesDescription->addData($this->data);
                $success_msg = 'Added successfully.';
                
            }
            return redirect()->route('feesDescription.index')->with('success', __($success_msg));

        }


        
        return view('feesDescription.create',compact('data','arrapp_code','arrfee_id'));
    }
   
    public function getList(Request $request)
    {
        $query = DB::table('fees')
            ->select('*');

           
        if (!empty($request->q)) {
            $search = $request->q;
            $query->where(function($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                ->orWhere(DB::raw('LOWER(amount)'),'like',"%".strtolower($search)."%");
                });
        }
        $totalRecords = DB::table('fees AS af')->count();
        $totalFiltered = $query->count();
        $limit = $request->input('length');   
        $start = $request->input('start');   
        $query->skip($start)->take($limit);
        $orderColumnIndex = $request->input('order.0.column');
        $orderDirection = $request->input('order.0.dir', 'asc'); 

        $columns = [
            0 => null,                      
            1 => 'name',   
            2 =>  'amount',
            3 => null                    
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
                    'amount' => number_format($row->amount, 2),
                    'action' => '<a href="#" 
                                class="mx-3 btn btn-sm align-items-center" 
                                data-url="' . url('/master-data/feesDescription/store?id=' . $row->id) . '" 
                                data-ajax-popup="true" 
                                data-size="lg" 
                                data-bs-toggle="tooltip" 
                                title="Edit" 
                                data-title="Manage Fees Description" style="background: #09325d !important;color: #fff;">
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
