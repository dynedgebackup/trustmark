<?php

namespace App\Http\Controllers;

use App\Models\Business;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use DB;
use Hashids\Hashids;
class DashboardController extends Controller
{
    public function index(Request $request)
    {
        $startdate = $request->input('fromdate');
        $enddate = $request->input('todate');
        // $businesses = Business::select([
        //     DB::raw("NULLIF(id,'') as `id`"),
        //     DB::raw("NULLIF(business_name,'') as `BusinessName`"),
        //     DB::raw("NULLIF(reg_num,'') as `RegistrationNo`"),
        //     DB::raw("NULLIF(tin,'') as `TIN`"),
        //     DB::raw("(CASE corporation_type
        //                 WHEN 1 THEN 'Sole Proprietorship'
        //                 WHEN 2 THEN 'Corporation/Partnership'
        //                 WHEN 4 THEN 'Cooperative'
        //               END) as `BusinessType`"),
        //     DB::raw("DATE_FORMAT(submit_date, '%m/%d/%Y') as `DateSubmitted`"),
        //     DB::raw("NULLIF(status,'') as `status`")
        // ])
        // ->where('is_active', 1)
        // ->orderByDesc('submit_date');
        // if (Auth::check() && Auth::user()->role == 1) {
        //     $businesses = $businesses->where('user_id', Auth::id());
        //         //->where('status', 'RETURNED');
        // } else {
        //     $businesses = $businesses->where('status', 'UNDER EVALUATION')->whereRaw('IFNULL(evaluator_id,0)=0');
        // }

        // if (!empty($startdate)) {
        //     $sdate = explode('-', $startdate);
        //     $startdate = date('Y-m-d', strtotime("{$sdate[2]}-{$sdate[1]}-{$sdate[0]}"));
        //     $businesses->whereDate('submit_date', '>=', trim($startdate));
        // }

        // if (!empty($enddate)) {
        //     $edate = explode('-', $enddate);
        //     $enddate = date('Y-m-d', strtotime("{$edate[2]}-{$edate[1]}-{$edate[0]}"));
        //     $businesses->whereDate('submit_date', '<=', trim($enddate));
        // }
        // if (Auth::check() && Auth::user()->role != 1) {
        //     if (!request()->has('fromdate')) {
        //         $startdate = date("Y-m-d");
        //         $sdate = explode('-', $startdate);
        //         $startdate = date('Y-m-d', strtotime("{$sdate[2]}-{$sdate[1]}-{$sdate[0]}"));
        //         $businesses->whereDate('submit_date', '>=', trim($startdate));
        //     }
        //     if (!request()->has('todate')) {
        //         $enddate = date("Y-m-d");
        //         $edate = explode('-', $enddate);
        //         $enddate = date('Y-m-d', strtotime("{$edate[2]}-{$edate[1]}-{$edate[0]}"));
        //         $businesses->whereDate('submit_date', '<=', trim($enddate));
        //     }
        // }

        // // Search filter
        // if ($request->filled('details')) {
        //     $search = strtolower($request->details);
        //     $businesses->where(function($q) use ($search) {
        //         $q->orWhereRaw('LOWER(business_name) like ?', ["%$search%"])
        //         ->orWhereRaw('LOWER(reg_num) like ?', ["%$search%"])
        //         ->orWhereRaw('LOWER(tin) like ?', ["%$search%"])
        //         ->orWhereRaw('LOWER(corporation_type) like ?', ["%$search%"])
        //         ->orWhereRaw('LOWER(submit_date) like ?', ["%$search%"])
        //         ->orWhereRaw('LOWER(status) like ?', ["%$search%"]);
        //     });
        // }

        // $businesses = $businesses->orderBy('id', 'DESC')->get();

        $businesses  = array();

        // Leave others unchanged
        $under_evaluations = Business::where('status', 'UNDER EVALUATION')->where('is_active', 1)->select('id');
        if (Auth::check() && Auth::user()->role == 1) {
            $under_evaluations = $under_evaluations->where('user_id', Auth::id())
                ->count();
        } else {
            $under_evaluations = $under_evaluations->count();
        }

        $approves = Business::where('status', 'APPROVED')->where('is_active', 1)->select('id');
        if (Auth::check() && Auth::user()->role == 1) {
            $approves = $approves->where('user_id', Auth::id())->count();
        } else {
            $approves = $approves->count();

        }

        $paid = Business::where('status', 'APPROVED')->where('is_active', 1)->select('id');
        if (Auth::check() && Auth::user()->role == 1) {
            $paid = $paid->where('user_id', Auth::id())->whereNotNull('payment_id')->count();
        } else {
            $paid = $paid->whereNotNull('payment_id')->count();
        }

        $returns = Business::where('status', 'RETURNED')->where('is_active', 1)->select('id');
        if (Auth::check() && Auth::user()->role == 1) {
            $returns = $returns->where('user_id', Auth::id())->count();
        } else {
            $returns = $returns->count();
        }

        $drafts = Business::where('status', 'DRAFT')->where('is_active', 1)->select('id');
        if (Auth::check() && Auth::user()->role == 1) {
            $drafts = $drafts->where('user_id', Auth::id())->count();
        } else {
            $drafts = $drafts->count();
        }

        $disapproves = Business::where('status', 'DISAPPROVED')->where('is_active', 1)->select('id');
        if (Auth::check() && Auth::user()->role == 1) {
            $disapproves = $disapproves->where('user_id', Auth::id())->count();
        } else {
            $disapproves = $disapproves->count();
        }

        if (Auth::check() && Auth::user()->role == 1) {
            $allApplicationCount = Business::where('is_active', 1)
                ->where('user_id', Auth::id())
                ->whereNotNull('status')
                // ->whereNotNull('corporation_type')
                ->select('id')
                ->count();
        } else {
            $allApplicationCount = Business::where('is_active', 1)
                // ->whereNotNull('corporation_type')
                ->whereNotNull('status')
                ->select('id')
                ->count();
        }
        $startdate = $request->input('fromdate');
        $enddate = $request->input('todate');
        
        $displayStartDate=date("Y-m-d");
        $displayEndDate=date("Y-m-d");
        if (request()->has('fromdate') && empty($startdate)) {
            $displayStartDate="";
        }
        if (request()->has('todate') && empty($enddate)) {
            $displayEndDate="";
        }

       if (Auth::check() && Auth::user()->role == 1) { 
        return view('dashboard', compact(
            'businesses',
            'under_evaluations',
            'approves',
            'paid',
            'returns',
            'drafts',
            'allApplicationCount',
            'disapproves',
            'displayStartDate',
            'displayEndDate'
        ));
       }else{
             return view('dashboardadmin', compact(
            'businesses',
            'under_evaluations',
            'approves',
            'paid',
            'returns',
            'drafts',
            'allApplicationCount',
            'disapproves',
            'displayStartDate',
            'displayEndDate'
        ));
       }

    }

    public function getList(Request $request){
        $params = $_REQUEST;
        $q = $request->input('q');
        $fromdate = $request->input('fromdate');
        $todate = $request->input('todate');

        if (!isset($params['start']) || !isset($params['length'])) {
            $params['start'] = "0";
            $params['length'] = "10";
        }

        $columns = [
            1 =>"trustmark_id",
            2 =>"business_name",
            3 =>"reg_num",
            4 =>"business_type",
            5 =>"tin",
            6 =>"representative", 
            7 =>"date_submitted"
        ];

        $sql = DB::table('businesses as a')
            ->leftJoin('users as b', 'a.user_id', '=', 'b.id')
            ->select([
                DB::raw("NULLIF(a.id,'') as id"), // fixed here
                DB::raw("NULLIF(a.trustmark_id,'') as trustmark_id"),
                DB::raw("NULLIF(a.business_name,'') as business_name"),
                DB::raw("NULLIF(a.reg_num,'') as reg_num"),
                DB::raw("NULLIF(a.on_hold,'') as on_hold"),
                DB::raw("NULLIF(a.tin,'') as tin"),
                DB::raw("(CASE a.corporation_type
                            WHEN 1 THEN 'Sole Proprietorship'
                            WHEN 2 THEN 'Corporation/Partnership'
                            WHEN 4 THEN 'Cooperative'
                        END) as business_type"),
                DB::raw("b.name as representative"),
                DB::raw("DATE_FORMAT(a.submit_date, '%m/%d/%Y') as date_submitted"),
                DB::raw("DATE_FORMAT(a.created_at, '%m/%d/%Y') as date_generated"),
                DB::raw("NULLIF(a.status,'') as status"),
                DB::raw("NULLIF(a.corporation_type,'') as corporation_type"),
            ])
            ->where('a.is_active', 1);
       
            // $sql->where('a.status', 'UNDER EVALUATION')
            // ->whereRaw('IFNULL(a.evaluator_id,0)=0');
           
        if (!empty($q)) {
            $sql->where(function ($query) use ($q) {
                $query->where(DB::raw('LOWER(trustmark_id)'), 'like', '%' . strtolower($q) . '%')
                      ->orWhere(DB::raw('LOWER(business_name)'),'like',"%".strtolower($q)."%")
                      ->orWhere(DB::raw('LOWER(tin)'),'like',"%".strtolower($q)."%")
                      ->orWhere(DB::raw('LOWER(b.name)'),'like',"%".strtolower($q)."%")
                      ->orWhere(DB::raw('LOWER(admin_remarks)'),'like',"%".strtolower($q)."%")
                      ->orWhere(DB::raw('LOWER(reg_num)'),'like',"%".strtolower($q)."%");
            });
        }
        if(!empty($fromdate) && isset($fromdate)){
            $sql->whereDate('submit_date','>=',trim($fromdate));  
        }
        if(!empty($todate) && isset($todate)){
            $sql->whereDate('submit_date','<=',trim($todate));  
        }
        if (Auth::check() && Auth::user()->role == 1) {
           $sql->where('user_id', Auth::id());
                //->where('status', 'RETURNED');
        } 

        if (isset($params['order'][0]['column'])) {
            $sql->orderBy($columns[$params['order'][0]['column']], $params['order'][0]['dir']);
        } else {
            $sql->orderBy('a.id', 'DESC');
        }

        $data_cnt = $sql->count();

        if ((int) $params['start'] >= $data_cnt) {
            $params['start'] = 0;
        }

        $sql->offset((int) $params['start'])->limit((int) $params['length']);
        $data = $sql->get();

        //$data=$this->business->getList($request);
        //echo "<pre>"; print_r($data); exit;
        $arr=array();
        $i="0";    
        $sr_no=(int)$request->input('start')-1; 
        $sr_no=$sr_no>0? $sr_no+1:0;
        $role = Auth::user()->role;

        foreach ($data as $row){
            $status = $row->status;
            $sr_no=$sr_no+1;
             $id = encrypt($row->id);
             $hashids = new Hashids(env('APP_KEY'), 10);
            $ids = $hashids->encode($row->id);
            if ($role == 1) {
                if (in_array($status, ['UNDER EVALUATION', 'APPROVED'])) {
                    $actions = '<a href="' . route('business.view', $ids) . '" 
                                    data-bs-toggle="tooltip" data-bs-placement="bottom" 
                                    title="View"><i class="custom-eye-icon fa fa-eye"></i></a>';
                } elseif ($status == 'RETURNED') {
                    $actions = '<a href="' . route('business.edit', $ids) . '" 
                                    data-bs-toggle="tooltip" data-bs-placement="bottom" 
                                    title="Edit"><i class="custom-pencil-icon fa fa-pencil"></i></a>';
                } elseif ($status == 'DISAPPROVED') {
                    $actions = '<a href="' . route('business.disapproved_view', $ids) . '" 
                                    data-bs-toggle="tooltip" data-bs-placement="bottom" 
                                    title="View"><i class="custom-eye-icon fa fa-eye"></i></a>';
                } else {
                    $actions = '<a href="' . route('business.create', $ids) . '" 
                                    data-bs-toggle="tooltip" data-bs-placement="bottom" 
                                    title="Continue"><i class="custom-pencil-icon fa fa-arrow-right"></i></a>';
                }
            } else {
                if ($status == 'UNDER EVALUATION') {
                    $actions = '<a href="' . route('business.view', $ids) . '" 
                                    data-bs-toggle="tooltip" data-bs-placement="bottom" 
                                    title="Update"><i class="custom-pencil-icon fa fa-pencil"></i></a>';
                } elseif ($status == 'DISAPPROVED') {
                    $actions = '<a href="' . route('business.disapproved_view', $ids) . '" 
                                    data-bs-toggle="tooltip" data-bs-placement="bottom" 
                                    title="View"><i class="custom-eye-icon fa fa-eye"></i></a>';
                } else {
                    $actions = '<a href="' . route('business.view', $ids) . '" 
                                    data-bs-toggle="tooltip" data-bs-placement="bottom" 
                                    title="View"><i class="custom-eye-icon fa fa-eye"></i></a>';
                }
            }
            

            $arr[$i]['srno']=$sr_no;
            $arr[$i]['trustmark_id']=$row->trustmark_id ?? 'N/A';
            $arr[$i]['business_name']= $row->business_name;
            $arr[$i]['reg_num']=$row->reg_num ?? 'N/A';
            $arr[$i]['tin']=$row->tin ?? 'N/A';
            $arr[$i]['business_type']=$row->business_type ?? 'N/A';
            $arr[$i]['representative']=$row->representative ?? 'N/A';
            $arr[$i]['generated']=$row->date_generated ?? 'N/A';
            $arr[$i]['date_submitted']=$row->date_submitted ?? 'N/A';
            $displayStatus = $status;

            $badgeClass = match ($status) {
                'APPROVED'     => 'badge badge-bg-approve p-2',
                'UNDER EVALUATION' => 'badge badge-bg-evaluation p-2',
                'ON-HOLD'      => 'badge badge-bg-evaluation p-2',
                'REJECTED'     => 'badge badge-bg-rejected p-2',
                'RETURNED'     => 'badge badge-bg-returned p-2',
                'DISAPPROVED'  => 'badge badge-bg-disapproved p-2',
                'DRAFT'        => 'badge badge-bg-draft p-2',
                default        => 'badge badge-bg-draft',
            };

            //condition for ROLE = 2 and ON-HOLD
            if ($status == 'UNDER EVALUATION' && $row->on_hold == 1 && $role == 2) {
                $displayStatus = 'UNDER EVALUATION <span style="color:#ec6868 !important; font-weight:bold;">(On-Hold)</span>';
            }elseif ($status == 'UNDER EVALUATION' && $row->on_hold == 1 && $role == 1){
                $displayStatus = 'UNDER EVALUATION';
            }
            // $arr[$i]['status'] = '<button class=" '.$badgeClass.' ">'.$status.'<button>';
            $arr[$i]['status'] = '<span class="'.$badgeClass.'" style="
                text-align:center;
                white-space: normal;
                word-break: break-word;
            ">'.$displayStatus.'</span>';
                                // $badgeClass = match ($status) {
                                //     'APPROVED' => 'badge badge-bg-approve p-2 px-3',
                                //     'UNDER EVALUATION' => 'badge badge-bg-evaluation p-2 px-3',
                                //     'RETURNED' => 'badge badge-bg-returned p-2 px-3',
                                //     'DISAPPROVED' => 'badge badge-bg-disapproved p-2 px-3',
                                //     'DRAFT' => 'badge badge-bg-draft p-2 px-3',
                                //     ''=>'badge badge-bg-draft',
                                // };
            // $arr[$i]['status'] = '<span class="'.$badgeClass.'">'.$status.'</span>';
            $arr[$i]['action']=$actions;
            $i++;
        }
        
        $totalRecords=$data_cnt;
        $json_data = array(
            "recordsTotal"    => intval( $totalRecords ),  
            "recordsFiltered" => intval($totalRecords),
            "data"            => $arr   // total data array
        );
        echo json_encode($json_data);
    }
}
