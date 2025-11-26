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

class CustomerProfileController extends Controller
{
    public $arrapp_code = array(""=>"");
    public $arrfee_id = array(""=>"");
    public function __construct(){
		$this->Region = new LocationRegion(); 
        $this->data = array('id'=>'','reg_no'=>'','reg_region'=>'','reg_description'=>'','is_active'=>'');  
    }
    public function index()
    {
        return view('CustomerProfile.index');
    }
   
    public function ActiveInactive(Request $request){
        $id = $request->input('id');
        $is_activeinactive = $request->input('is_activeinactive');
        $data=array('status' => $is_activeinactive);
        $this->Region->updateActiveInactive($id,$data);
    }
    public function getCheckRecordBusinessRegNum($id)
    {
        $records = DB::table('businesses as a')
            ->leftJoin('users as b', 'a.user_id', '=', 'b.id')
            ->leftJoin('users as c', 'a.user_id', '=', 'c.id')
            ->select(
                DB::raw("NULLIF(a.trustmark_id,'') as `SecurityNo`"),
                DB::raw("NULLIF(a.business_name,'') as `BusinessName`"),
                DB::raw("NULLIF(a.reg_num,'') as `RegistrationNo`"),
                DB::raw("NULLIF((CASE a.corporation_type
                        WHEN 1 THEN 'Sole Proprietorship'
                        WHEN 2 THEN 'Corporation/Partnership'
                        WHEN 4 THEN 'Cooperative'
                    END),'') as `BusinessType`"),
                DB::raw("NULLIF(c.name,'') as `Evaluator`"),
                DB::raw("NULLIF(a.tin,'') as `TIN`"),
                DB::raw('b.name as `Representative`'),
                DB::raw("DATE_FORMAT(a.submit_date, '%m/%d/%Y') as `Submitted`"),
                DB::raw("DATE_FORMAT(a.date_returned, '%m/%d/%Y') as `Returned`"),
                DB::raw("DATE_FORMAT(a.date_approved, '%m/%d/%Y') as `Approved`"),
                DB::raw("DATE_FORMAT(a.date_issued, '%m/%d/%Y') as `Paid`"),
                DB::raw("a.amount as `Amount`"),
                DB::raw("a.payment_channel as `Channel`"),
                DB::raw("NULLIF(a.admin_remarks,'') as `Remarks`"),
                DB::raw("NULLIF(a.status,'') as `Status`")
            )
            ->where('a.user_id', $id) 
            // ->whereIn('a.status', ['APPROVED', 'UNDER EVALUATION', 'DRAFT'])
            ->where('a.is_active', 1)
            ->orderByDesc('a.submit_date')
            ->get();

        return response()->json(['data' => $records]);
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
        $query = DB::table('users')->select('*')->where('role', 1);

        $startdate = $request->input('fromdate');
        $enddate = $request->input('todate');

        if ($request->filled('status')) {
            if ($request->status == 'Verified') {
                $query->whereNotNull('email_verified_at');
            } else {
                $query->whereNull('email_verified_at');
            }
        }

        if (!empty($startdate)) {
            $sdate = explode('-', $startdate);
            $startdate = date('Y-m-d', strtotime("{$sdate[2]}-{$sdate[1]}-{$sdate[0]}"));
            $query->whereDate('created_at', '>=', trim($startdate));
        }

        if (!empty($enddate)) {
            $edate = explode('-', $enddate);
            $enddate = date('Y-m-d', strtotime("{$edate[2]}-{$edate[1]}-{$edate[0]}"));
            $query->whereDate('created_at', '<=', trim($enddate));
        }

        if (!empty($request->q)) {
            $search = $request->q;
            $query->where(function($q) use ($search) {
                $q->where('email', 'like', "%{$search}%")
                ->orWhere(DB::raw('LOWER(name)'), 'like', "%".strtolower($search)."%")
                ->orWhere(DB::raw('LOWER(created_at)'), 'like', "%".strtolower($search)."%")
                ->orWhere(DB::raw('LOWER(email_verified_at)'), 'like', "%".strtolower($search)."%");
            });
        }

        $totalRecords = DB::table('users')->where('role', 1)->count();
        $totalFiltered = $query->count();

        $limit = $request->input('length');   
        $start = $request->input('start');   
        $query->skip($start)->take($limit);

        $orderColumnIndex = $request->input('order.0.column');
        $orderDirection = $request->input('order.0.dir', 'asc');

        $columns = [
            0 => null,                      
            1 => 'name',   
            2 => 'email',
            3 => 'created_at',
            4 => 'email_verified_at',
            5 => 'is_active',
            6 => 'is_active',
            7 => 'is_active',
            8 => 'is_active',
            9 => null                   
        ];
        $orderColumn = $columns[$orderColumnIndex] ?? null;

        if (!empty($orderColumn)) {
            $query->orderBy($orderColumn, $orderDirection);
        }

        $fees = $query->get();
        $businessCounts = DB::table('businesses')
            ->select('user_id', 'status', DB::raw('COUNT(id) as total'))
            ->whereIn('user_id', $fees->pluck('id'))
            ->groupBy('user_id', 'status')
            ->get()
            ->groupBy('user_id');
        
        $data = [];
        $i = $start + 1;
        
        foreach ($fees as $row) {
            $userBusiness = $businessCounts[$row->id] ?? collect();

            $approved = $userBusiness->firstWhere('status', 'APPROVED')->total ?? ' ';
            $underEval = $userBusiness->firstWhere('status', 'UNDER EVALUATION')->total ?? ' ';
            $draft = $userBusiness->firstWhere('status', 'DRAFT')->total ?? ' ';
           
            $data[] = [
                'no' => $i++,
                'name' => $row->name ?? ' ',
                'email' => $row->email ?? ' ',
                'created_at' => $row->created_at ?? ' ',
                'email_verified_at' => $row->email_verified_at ?? ' ',
                'approved' => $approved,
                'under_evaluation' => $underEval,
                'draft' => $draft,
                'status' => ($row->is_active == 1
                    ? '<span class="btn btn-success" style="padding: 0.1rem 0.5rem !important;background: none;font-size: 12px;border-radius: 11px;">Active</span>'
                    : '<span class="btn btn-warning" style="padding: 0.1rem 0.5rem !important;background: none;font-size: 12px;border-radius: 11px;">Cancel</span>'),
                'action' => '<a href="#" 
                                class="btn btn-sm btn-edit-business" 
                                data-id="'.$row->id.'" 
                                title="Edit" 
                                data-title="Manage CustomerProfile" 
                                style="background: #09325d !important;color: #fff;">
                                <i class="fas fa-pencil"></i>
                            </a>'
                            . (is_null($row->email_verified_at) ? 
                            '<a href="javascript:void(0);" 
                                class="btn btn-sm btn-danger delete-btn" 
                                data-id="'.$row->id.'" 
                                data-name="'.$row->name.'" 
                                title="Delete" style="margin-left: 2px;">
                                <i class="fas fa-trash"></i>
                            </a>' : ''),
            ];
        }

        return response()->json([
            'draw' => intval($request->input('draw')),
            'recordsTotal' => $totalRecords,
            'recordsFiltered' => $totalFiltered,
            'data' => $data
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
