<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use App\Models\Business;
use File;
use Carbon\Carbon;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Support\Facades\Auth;
use DB;


class ArchivedApplicationsReportController extends Controller
{
    public function __construct(){
         
    }
    public function index()
    {
        return view('archived-applications.index');
    }
    public function exportAll(Request $request)
    {
        $startdate = $request->input('fromdate');
        $enddate = $request->input('todate');
        $query = DB::table('businesses as a')
            ->select([
                'a.id as ID',
                DB::raw("IFNULL(a.trustmark_id,'') as `SecurityNo`"),
                DB::raw("IFNULL(a.business_name,'') as `BusinessName`"),
                DB::raw("IFNULL(a.reg_num,'') as `RegistrationNo`"),
                DB::raw("IFNULL(
                    CASE a.corporation_type
                        WHEN 1 THEN 'Sole Proprietor'
                        WHEN 2 THEN 'Corporation/Partnership'
                        WHEN 3 THEN 'Cooperative'
                    END, ''
                ) as `BusinessType`"),
                DB::raw("IFNULL(a.tin,'') as `TIN`"),
                DB::raw("IFNULL(b.name,'') as `Representative`"),
                DB::raw("IFNULL(a.admin_remarks,'') as `Remarks`"),
                DB::raw("IFNULL(a.status,'') as `Status`"),
                DB::raw("IFNULL(a.pic_email,'') as `EmailAddress`"),
                DB::raw("IFNULL(a.pic_ctc_no,'') as `ContactNo`"),
                DB::raw("IFNULL(DATE(a.date_archived),'') as DateArchived"),
            ])
            ->leftJoin('users as b', 'a.user_id', '=', 'b.id')
            ->where('a.is_active', 3)
            ->orderByDesc('a.date_archived');
        if (!empty($startdate)) {
            $sdate = explode('-', $startdate);
            $startdate = date('Y-m-d', strtotime("{$sdate[2]}-{$sdate[1]}-{$sdate[0]}"));
            $query->whereDate('a.date_archived', '>=', trim($startdate));
        }

        if (!empty($enddate)) {
            $edate = explode('-', $enddate);
            $enddate = date('Y-m-d', strtotime("{$edate[2]}-{$edate[1]}-{$edate[0]}"));
            $query->whereDate('a.date_archived', '<=', trim($enddate));
        }
        if (!empty($request->q)) {
            $search = $request->q;
            $query->where(function($q) use ($search) {
                $q->where('a.trustmark_id', 'like', "%{$search}%")
                ->orWhere(DB::raw('LOWER(a.business_name)'),'like',"%".strtolower($search)."%")
                ->orWhere(DB::raw('LOWER(a.reg_num)'),'like',"%".strtolower($search)."%")
                ->orWhere(DB::raw('LOWER(a.admin_remarks)'),'like',"%".strtolower($search)."%")
                ->orWhere(DB::raw('LOWER(a.tin)'),'like',"%".strtolower($search)."%")
                ->orWhere(DB::raw('LOWER(b.name)'),'like',"%".strtolower($search)."%")
                ->orWhere(DB::raw('LOWER(a.status)'),'like',"%".strtolower($search)."%")
                ->orWhere(DB::raw('LOWER(a.pic_email)'),'like',"%".strtolower($search)."%")
                ->orWhere(DB::raw('LOWER(a.pic_ctc_no)'),'like',"%".strtolower($search)."%")
                ->orWhere(DB::raw('LOWER(a.date_archived)'),'like',"%".strtolower($search)."%");
                });
        }

        $data = $query->get();

        return response()->json(['data' => $data]);
    }


    public function getList(Request $request)
    {
        $startdate = $request->input('fromdate');
        $enddate = $request->input('todate');

        $query = DB::table('businesses as a')
            ->select([
                'a.id as ID',
                DB::raw("IFNULL(a.trustmark_id,'') as `SecurityNo`"),
                DB::raw("IFNULL(a.business_name,'') as `BusinessName`"),
                DB::raw("IFNULL(a.reg_num,'') as `RegistrationNo`"),
                DB::raw("IFNULL(
                    CASE a.corporation_type
                        WHEN 1 THEN 'Sole Proprietor'
                        WHEN 2 THEN 'Corporation/Partnership'
                        WHEN 3 THEN 'Cooperative'
                    END, ''
                ) as `BusinessType`"),
                DB::raw("IFNULL(a.tin,'') as `TIN`"),
                DB::raw("IFNULL(b.name,'') as `Representative`"),
                DB::raw("IFNULL(a.admin_remarks,'') as `Remarks`"),
                DB::raw("IFNULL(a.status,'') as `Status`"),
                DB::raw("IFNULL(a.pic_email,'') as `EmailAddress`"),
                DB::raw("IFNULL(a.pic_ctc_no,'') as `ContactNo`"),
                DB::raw("IFNULL(DATE(a.date_archived),'') as DateArchived"),
            ])
            ->leftJoin('users as b', 'a.user_id', '=', 'b.id')
            ->where('a.is_active', 3)
            ->orderByDesc('a.date_archived');
        if (!empty($startdate)) {
            $sdate = explode('-', $startdate);
            $startdate = date('Y-m-d', strtotime("{$sdate[2]}-{$sdate[1]}-{$sdate[0]}"));
            $query->whereDate('a.date_archived', '>=', trim($startdate));
        }

        if (!empty($enddate)) {
            $edate = explode('-', $enddate);
            $enddate = date('Y-m-d', strtotime("{$edate[2]}-{$edate[1]}-{$edate[0]}"));
            $query->whereDate('a.date_archived', '<=', trim($enddate));
        }
        if (!empty($request->q)) {
            $search = $request->q;
            $query->where(function($q) use ($search) {
                $q->where('a.trustmark_id', 'like', "%{$search}%")
                ->orWhere(DB::raw('LOWER(a.business_name)'),'like',"%".strtolower($search)."%")
                ->orWhere(DB::raw('LOWER(a.reg_num)'),'like',"%".strtolower($search)."%")
                ->orWhere(DB::raw('LOWER(a.admin_remarks)'),'like',"%".strtolower($search)."%")
                ->orWhere(DB::raw('LOWER(a.tin)'),'like',"%".strtolower($search)."%")
                ->orWhere(DB::raw('LOWER(b.name)'),'like',"%".strtolower($search)."%")
                ->orWhere(DB::raw('LOWER(a.status)'),'like',"%".strtolower($search)."%")
                ->orWhere(DB::raw('LOWER(a.pic_email)'),'like',"%".strtolower($search)."%")
                ->orWhere(DB::raw('LOWER(a.pic_ctc_no)'),'like',"%".strtolower($search)."%")
                ->orWhere(DB::raw('LOWER(a.date_archived)'),'like',"%".strtolower($search)."%");
                });
        }
        $totalRecords = DB::table('businesses as a')->count();
        $totalFiltered = $query->count();
        $limit = $request->input('length');   
        $start = $request->input('start');   
        $query->skip($start)->take($limit);
        $orderColumnIndex = $request->input('order.0.column');
        $orderDirection = $request->input('order.0.dir', 'asc'); 

        $columns = [
            0 => null,                      
            1 => 'SecurityNo',   
            2 => 'BusinessName',
            3 => 'RegistrationNo',
            4 => 'BusinessType',
            5 => 'TIN',
            6 => 'Remarks',
            7 => 'Status',        
            8 => 'EmailAddress',     
            9 => 'ContactNo',
            10 => 'DateArchived',     
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
                'BusinessName' => $row->BusinessName ?? ' ',
                'SecurityNo' => $row->SecurityNo ?? ' ',
                'RegistrationNo' => $row->RegistrationNo ?? ' ',
                'BusinessType' => $row->BusinessType ?? ' ',
                'TIN' => $row->TIN ?? ' ',
                'Representative' => $row->Representative ?? ' ',
                'Remarks' => $row->Remarks ?? ' ',
                'Status' => $row->Status ?? ' ',
                'EmailAddress' => $row->EmailAddress ?? ' ',
                'ContactNo' => $row->ContactNo ?? ' ',
                'DateArchived' => $row->DateArchived ?? ' ',
                'action' => '<a href="javascript:void(0);" 
                    class="btn btn-sm btn-warning"
                    onclick="ActiveInactiveUpdate('.$row->ID.', \''.addslashes($row->BusinessName).'\', \''.addslashes($row->SecurityNo).'\')">
                    <i class="fa fa-refresh"></i>
                </a>',

            ];
        }

        return response()->json([
            'draw' => intval($request->input('draw')),
            'recordsTotal' => $totalRecords,
            'recordsFiltered' => $totalFiltered,
            'data' => $data
        ]);
    }
    public function ActiveArchivedApplications(Request $request){
        $id = $request->input('id');
        $is_active = $request->input('is_active');
        $business = Business::findOrFail($id);
        DB::table('businesses') 
            ->where('id', $id)
            ->update([
                'is_active' => $is_active,
                'admin_updated_by' => Auth::id(),
                'updated_by'       => Auth::id(),
                'admin_updated_at' => now(),
                'updated_at' => now(),
            ]);
        DB::table('business_performance')->insert(
            [
            'busn_id'   => $id,
            'year'      => date('Y'),
            'user_id'   => $business->evaluator_id,
            'process'   => "RE-ACTIVATED",
            'process_date'     => now(),
        ]);
        DB::table('user_logs')->updateOrInsert(
            [
                'busn_id'   => $id,
                'action_id' => 19, 
            ],
            [
            'action_name'      => 're-activate',
            'message'          => Auth::user()->name . ' view the application with Sec-No. ' 
                                    . $business->trustmark_id . ' dated ' . now()->format('Y-m-d H:i:s') . '.',
            'public_ip_address'=> $request->ip(),
            'status'           => $business->status,
            'remarks'          => '',
            'longitude'        => $request->input('longitude'),
            'latitude'         => $request->input('latitude'),
            'created_by'       => Auth::id(),
            'created_by_name'  => Auth::user()->name,
            'created_date'     => now(),
        ]);
    }

    
}
