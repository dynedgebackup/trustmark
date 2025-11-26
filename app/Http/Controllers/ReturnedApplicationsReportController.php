<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use File;
use Carbon\Carbon;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Support\Facades\Auth;
use DB;

class ReturnedApplicationsReportController extends Controller
{
    public function __construct(){
         
    }
    public function index()
    {
        return view('returned-applications.index');
    }
    public function exportAll(Request $request)
    {
        $startdate = $request->input('fromdate');
        $enddate = $request->input('todate');

        $query = DB::table('business_compliance as d')
        ->select([
            'a.id as ID',
            'd.*',
            DB::raw("COALESCE(a.trustmark_id, '') as SecurityNo"),
            DB::raw("COALESCE(a.business_name, '') as BusinessName"),
            DB::raw("COALESCE(c.name, '') as evaluator_name"),
            DB::raw("COALESCE(b.name, '') as Representative"),
            DB::raw("COALESCE(a.admin_remarks, '') as Remarks"),
        ])
        ->leftJoin('businesses as a', 'a.id', '=', 'd.busn_id')
        ->leftJoin('users as b', 'a.user_id', '=', 'b.id')
        ->leftJoin('users as c', 'a.evaluator_id', '=', 'c.id')
        ->where('a.is_active', 1)->where('a.status', 'RETURNED')->whereNotNull('a.evaluator_id')->whereNull('a.payment_id')
        ->orderByDesc('d.id');
        if (!empty($startdate)) {
            $sdate = explode('-', $startdate);
            $startdate = date('Y-m-d', strtotime("{$sdate[2]}-{$sdate[1]}-{$sdate[0]}"));
            $query->whereDate('a.date_returned', '>=', trim($startdate));
        }

        if (!empty($enddate)) {
            $edate = explode('-', $enddate);
            $enddate = date('Y-m-d', strtotime("{$edate[2]}-{$edate[1]}-{$edate[0]}"));
            $query->whereDate('a.date_returned', '<=', trim($enddate));
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
                ->orWhere(DB::raw('LOWER(c.name)'),'like',"%".strtolower($search)."%")
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

        $query = DB::table('business_compliance as d')
        ->select([
            'a.id as ID',
            'd.*',
            DB::raw("COALESCE(a.trustmark_id, '') as SecurityNo"),
            DB::raw("COALESCE(a.business_name, '') as BusinessName"),
            DB::raw("COALESCE(c.name, '') as evaluator_name"),
            DB::raw("COALESCE(b.name, '') as Representative"),
            DB::raw("COALESCE(a.admin_remarks, '') as Remarks"),
        ])
        ->leftJoin('businesses as a', 'a.id', '=', 'd.busn_id')
        ->leftJoin('users as b', 'a.user_id', '=', 'b.id')
        ->leftJoin('users as c', 'a.evaluator_id', '=', 'c.id')
        ->where('a.is_active', 1)->where('a.status', 'RETURNED')->whereNotNull('a.evaluator_id')->whereNull('a.payment_id')
        ->orderByDesc('d.id');
        if (!empty($startdate)) {
            $sdate = explode('-', $startdate);
            $startdate = date('Y-m-d', strtotime("{$sdate[2]}-{$sdate[1]}-{$sdate[0]}"));
            $query->whereDate('a.date_returned', '>=', trim($startdate));
        }

        if (!empty($enddate)) {
            $edate = explode('-', $enddate);
            $enddate = date('Y-m-d', strtotime("{$edate[2]}-{$edate[1]}-{$edate[0]}"));
            $query->whereDate('a.date_returned', '<=', trim($enddate));
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
                ->orWhere(DB::raw('LOWER(c.name)'),'like',"%".strtolower($search)."%")
                ->orWhere(DB::raw('LOWER(a.status)'),'like',"%".strtolower($search)."%")
                ->orWhere(DB::raw('LOWER(a.pic_email)'),'like',"%".strtolower($search)."%")
                ->orWhere(DB::raw('LOWER(a.pic_ctc_no)'),'like',"%".strtolower($search)."%")
                ->orWhere(DB::raw('LOWER(a.date_archived)'),'like',"%".strtolower($search)."%");
                });
        }
        $totalRecords = DB::table('business_compliance_history as d')->count();
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
                    'evaluator_name' => $row->evaluator_name ?? ' ',
                    'Representative' => $row->Representative ?? ' ',
                    'busn_type_is_compliance' => $row->busn_type_is_compliance == 1 ?'Yes': 'No ',
                    'busn_type_remarks' => $row->busn_type_remarks ?? ' ',
                    'busn_name_is_compliance' => $row->busn_name_is_compliance == 1 ?'Yes': 'No ',
                    'busn_name_remarks' => $row->busn_name_remarks ?? ' ',
                    'busn_trade_is_compliance' => $row->busn_trade_is_compliance == 1 ?'Yes': 'No ',
                    'busn_trade_remarks' => $row->busn_trade_remarks ?? ' ',
                    'busn_category_is_compliance' => $row->busn_category_is_compliance == 1 ?'Yes': 'No ',
                    'busn_category_remarks' => $row->busn_category_remarks ?? ' ',
                    'busn_regno_is_compliance' => $row->busn_regno_is_compliance == 1 ?'Yes': 'No ',
                    'busn_regno_remarks' => $row->busn_regno_remarks ?? ' ',
                    'tin_is_compliance' => $row->tin_is_compliance == 1 ?'Yes': 'No ',
                    'tin_remarks' => $row->tin_remarks ?? ' ',
                    'url_is_compliance' => $row->url_is_compliance == 1 ?'Yes': 'No ',
                    'url_remarks' => $row->url_remarks ?? ' ',
                    'authrep_name_is_compliance' => $row->authrep_name_is_compliance == 1 ?'Yes': 'No ',
                    'authrep_name_remarks' => $row->authrep_name_remarks ?? ' ',
                    'authrep_mobile_is_compliance' => $row->authrep_mobile_is_compliance == 1 ?'Yes': 'No ',
                    'authrep_mobile_remarks' => $row->authrep_mobile_remarks ?? ' ',
                    'authrep_email_is_compliance' => $row->authrep_email_is_compliance == 1 ?'Yes': 'No ',
                    'authrep_email_remarks' => $row->authrep_email_remarks ?? ' ',
                    'authrep_govtid_is_compliance' => $row->authrep_govtid_is_compliance == 1 ?'Yes': 'No ',
                    'authrep_govtid_remarks' => $row->authrep_govtid_remarks ?? ' ',
                    'authrep_govtid_doc_is_compliance' => $row->authrep_govtid_doc_is_compliance == 1 ?'Yes': 'No ',
                    'authrep_govtid_doc_remarks' => $row->authrep_govtid_doc_remarks ?? ' ',
                    'authrep_govtid_expiry_is_compliance' => $row->authrep_govtid_expiry_is_compliance == 1 ?'Yes': 'No ',
                    'authrep_govtid_expiry_remarks' => $row->authrep_govtid_expiry_remarks ?? ' ',
                    'add_comp_is_compliance' => $row->add_comp_is_compliance == 1 ?'Yes': 'No ',
                    'add_comp_remarks' => $row->add_comp_remarks ?? ' ',
                    'add_barangay_is_compliance' => $row->add_barangay_is_compliance == 1 ?'Yes': 'No ',
                    'add_barangay_remarks' => $row->add_barangay_remarks ?? ' ',
                    'add_muncity_is_compliance' => $row->add_muncity_is_compliance == 1 ?'Yes': 'No ',
                    'add_muncity_remarks' => $row->add_muncity_remarks ?? ' ',
                    'add_province_is_compliance' => $row->add_province_is_compliance == 1 ?'Yes': 'No ',
                    'add_province_remarks' => $row->add_province_remarks ?? ' ',
                    'add_region_is_compliance' => $row->add_region_is_compliance == 1 ?'Yes': 'No ',
                    'add_region_remarks' => $row->add_region_remarks ?? ' ',
                    'doc_busnreg_is_compliance' => $row->doc_busnreg_is_compliance == 1 ?'Yes': 'No ',
                    'doc_busnreg_remarks' => $row->doc_busnreg_remarks ?? ' ',
                    'doc_bir_is_compliance' => $row->doc_bir_is_compliance == 1 ?'Yes': 'No ',
                    'doc_bir_remarks' => $row->doc_bir_remarks ?? ' ',
                    'doc_irm_is_compliance' => $row->doc_irm_is_compliance == 1 ?'Yes': 'No ',
                    'doc_irm_remarks' => $row->doc_irm_remarks ?? ' ',
                    'doc_bmbe_is_compliance' => $row->doc_bmbe_is_compliance == 1 ?'Yes': 'No ',
                    'doc_bmbe_remarks' => $row->doc_bmbe_remarks ?? ' ',
                    'asset_category_is_compliance' => $row->asset_category_is_compliance == 1 ?'Yes': 'No ',
                    'asset_category_remarks' => $row->asset_category_remarks ?? ' ',
                    'asset_valuation_is_compliance' => $row->asset_valuation_is_compliance == 1 ?'Yes': 'No ',
                    'asset_valuation_remarks' => $row->asset_valuation_remarks ?? ' ',
                    'doc_addpermit_is_compliance' => $row->doc_addpermit_is_compliance == 1 ?'Yes': 'No ',
                    'doc_addpermit_remarks' => $row->doc_addpermit_remarks ?? ' ',
                    'Remarks' => $row->Remarks ?? ' ',
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
