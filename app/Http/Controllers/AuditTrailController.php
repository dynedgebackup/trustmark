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
use DB;

class AuditTrailController extends Controller
{
    public $arrapp_code = array(""=>"");
    public $arrfee_id = array(""=>"");
    public function __construct(){
		$this->Region = new LocationRegion(); 
        $this->data = array('id'=>'','reg_no'=>'','reg_region'=>'','reg_description'=>'','is_active'=>'');  
    }
    public function index()
    {
        return view('audittrail.index');
    }
   
    public function ActiveInactive(Request $request){
        $id = $request->input('id');
        $is_activeinactive = $request->input('is_activeinactive');
        $data=array('status' => $is_activeinactive);
        $this->Region->updateActiveInactive($id,$data);
    }
    public function store(Request $request){
       $data = (object)$this->data;
       $arrapp_code = $this->arrapp_code;
       $arrfee_id = $this->arrfee_id;
        if($request->input('id')>0 && $request->input('submit')==""){
            $data = $this->Region->getEditDetails($request->input('id'));
            
        }
		
        if($request->input('submit')!=""){
            foreach((array)$this->data as $key=>$val){
                $this->data[$key] = $request->input($key);
            }
            
            $this->data['updated_by']=Auth::id();
            $this->data['updated_at'] = date('Y-m-d H:i:s');
            if($request->input('id')>0){
                $this->Region->updateData($request->input('id'),$this->data);
                $lastInsertedId = $request->input('id');
                $success_msg = 'Updated successfully.';
            }else{
                $this->data['created_by']=Auth::id();
                $this->data['created_at'] = date('Y-m-d H:i:s');
                $lastInsertedId = $this->Region->addData($this->data);
                $success_msg = 'Added successfully.';
                
            }
            return redirect()->route('region.index')->with('success', __($success_msg));

        }


        
        return view('region.create',compact('data'));
    }
   
    public function getList(Request $request)
    {
        $startdate = $request->input('fromdate');
        $enddate = $request->input('todate');
        $query = DB::table('user_logs as a')
            ->select(
                'a.created_date as date_time',
                'a.created_by_name as user_name',
                'a.action_name as action_name',
                'a.message as audit_description',
                'a.longitude as longitude',
                'a.latitude as latitude',
                'a.status as status',
                'a.remarks as remarks'
            )
            ->orderByDesc('a.created_date');
        if (! empty($startdate)) {
            $sdate = explode('-', $startdate);
            $startdate = date('Y-m-d', strtotime("{$sdate[2]}-{$sdate[1]}-{$sdate[0]}"));
            $query->whereDate('a.created_date', '>=', trim($startdate));
        }

        if (! empty($enddate)) {
            $edate = explode('-', $enddate);
            $enddate = date('Y-m-d', strtotime("{$edate[2]}-{$edate[1]}-{$edate[0]}"));
            $query->whereDate('a.created_date', '<=', trim($enddate));
        }
        if (! empty($request->q)) {
            $search = $request->q;
            $query->where(function ($q) use ($search) {
                $q->where('a.action_name', 'like', "%{$search}%")
                    ->orWhere(DB::raw('LOWER(a.created_by_name)'), 'like', '%'.strtolower($search).'%')
                    ->orWhere(DB::raw('LOWER(a.message)'), 'like', '%'.strtolower($search).'%')
                    ->orWhere(DB::raw('LOWER(a.status)'), 'like', '%'.strtolower($search).'%')
                    ->orWhere(DB::raw('LOWER(a.remarks)'), 'like', '%'.strtolower($search).'%');
            });
        }
        $totalRecords = DB::table('user_logs as a')->count();
        $totalFiltered = $query->count();
        $limit = $request->input('length');
        $start = $request->input('start');
        $query->skip($start)->take($limit);
        $orderColumnIndex = $request->input('order.0.column');
        $orderDirection = $request->input('order.0.dir', 'asc');

        $columns = [
            0 => null,
            1 => 'date_time',
            2 => 'user_name',
            3 => 'action_name',
            4 => 'name',
            5 => 'status',
            6 => 'remarks',

        ];
        $orderColumn = $columns[$orderColumnIndex] ?? null;

        if (! empty($orderColumn)) {
            $query->orderBy($orderColumn, $orderDirection);
        }

        $fees = $query->get();
        $data = [];
        $i = $start + 1;

        foreach ($fees as $row) {
            $location = '';
            if (! empty($row->latitude)) {
                $location = '<a href="#" class="viewlocation"  title="view"  data-title="View" latitude='.$row->latitude.' longitude='.$row->longitude.'>
                    <i class="custom-eye-icon fa fa-eye" style="color: #eea236;"></i>
                    </a>';
            }
            $data[] = [
                'no' => $i++,
                'date_time' => $row->date_time
                ? date('d-m-Y h:i A', strtotime($row->date_time))
                : ' ',
                'user_name' => $row->user_name ?? ' ',
                'action_name' => $row->action_name ?? ' ',
                'audit_description' => $row->audit_description ?? '',
                'name' => $row->name ?? ' ',
                'status' => $row->status ?? ' ',
                'remarks' => $row->remarks ?? ' ',
                'location' => $location,
            ];
        }

        return response()->json([
            'draw' => intval($request->input('draw')),
            'recordsTotal' => $totalRecords,
            'recordsFiltered' => $totalFiltered,
            'data' => $data,
        ]);
    }

    public function destroy($id)
    {
        DB::transaction(function () use ($id) {
            DB::table('businesses')->where('user_id', $id)->delete();
            DB::table('users')->where('id', $id)->delete(); 
        });

        return response()->json(['success' => true]);
    }


    
}
