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

class DailyReportController extends Controller
{
    public function __construct(){
         
    }
    public function index()
    {
        return view('daily_report.index');
    }
    public function exportAll(Request $request)
    {
        $startdate = $request->input('fromdate');
        $enddate = $request->input('todate');
        $query = DB::table('businesses as a')
                ->select([
                    'a.id as ID',
                    DB::raw("IFNULL(a.trustmark_id,'') AS `SecurityNo`"),
                    DB::raw("IFNULL(a.business_name,'') AS `BusinessName`"),
                    DB::raw("IFNULL(a.reg_num,'') AS `RegistrationNo`"),
                    DB::raw("IFNULL(
                        CASE a.corporation_type
                            WHEN 1 THEN 'Sole Proprietor'
                            WHEN 2 THEN 'Corporation/Partnership'
                            WHEN 3 THEN 'Cooperative'
                        END, ''
                    ) AS `BusinessType`"),
                    DB::raw("IFNULL(a.tin,'') AS `TIN`"),
                    DB::raw("IFNULL(b.name,'') AS `Representative`"),
                    DB::raw("IFNULL(a.amount,'') AS `Payment`"),
                    DB::raw("IFNULL(a.admin_remarks,'') AS `Remarks`"),
                    DB::raw("IFNULL(a.status,'') AS `Status`"),
                    DB::raw("IFNULL(a.pic_email,'') AS `EmailAddress`"),
                    DB::raw("IFNULL(a.pic_ctc_no,'') AS `ContactNo`"),
                    DB::raw("IFNULL(c.name,'') AS `Evaluator`"),
                    DB::raw("IFNULL(DATE(a.submit_date),'') AS `DateSubmitted`"),
                    DB::raw("IFNULL(DATE(a.date_approved),'') AS `DateApproved`"),
                    DB::raw("IFNULL(DATE(a.date_issued),'') AS `DateIssued`"),
                    DB::raw("IFNULL(DATE(a.date_disapproved),'') AS `Datedisapproved`"),
                    DB::raw("IFNULL(DATE(a.date_returned),'') AS `Datereturned`"),
                    DB::raw("IFNULL(DATE(a.created_at),'') AS `Datecreated_at`"),
                    DB::raw("IFNULL(a.payment_channel,'') AS `Channel`"),
                    DB::raw("IFNULL(a.complete_address,'') AS `Complete_Address`"),
                    DB::raw("IFNULL(d.brgy_name,'') AS `Barangay`"),
                    DB::raw("IFNULL(d.mun_desc,'') AS `Municipality_City`"),
                    DB::raw("IFNULL(d.prov_desc,'') AS `Province`"),
                    DB::raw("IFNULL(d.reg_region,'') AS `Region`"),
                    DB::raw("CASE a.is_bmbe 
                        WHEN 0 THEN 'No' 
                        WHEN 1 THEN 'Yes' 
                     END AS `withBMBE`")
                ])
            ->leftJoin('users as b', 'a.user_id', '=', 'b.id')
            ->leftJoin('users as c', 'a.evaluator_id', '=', 'c.id')
            ->leftJoin('barangays as d', 'a.barangay_id', '=', 'd.id')
            ->where('a.is_active', 1)
            ->orderByDesc('a.id');
            if ($request->filled('status')) {
                $query->where('a.status', $request->status);
            }

            $status = $request->status;
            if (!empty($startdate)) {
                $s = explode('-', $startdate);
                $startdate = date('Y-m-d', strtotime("$s[2]-$s[1]-$s[0]"));
            }

            if (!empty($enddate)) {
                $e = explode('-', $enddate);
                $enddate = date('Y-m-d', strtotime("$e[2]-$e[1]-$e[0]"));
            }
            switch ($status) {

                case 'RETURNED':
                    $dateField = 'a.date_returned';
                    break;

                case 'APPROVED':
                    $dateField = 'a.date_approved';
                    break;

                case 'DISAPPROVED':
                    $dateField = 'a.date_disapproved';
                    break;

                case 'UNDER EVALUATION':
                    $dateField = 'a.submit_date';
                    break;

                case 'DRAFT':
                    $dateField = 'a.created_at';
                    break;

                default:
                    $dateField = 'a.created_at';
                    break;
            }
            if (!empty($startdate)) {
                $query->whereDate($dateField, '>=', $startdate);
            }

            if (!empty($enddate)) {
                $query->whereDate($dateField, '<=', $enddate);
            }

        // Search filter
        if (!empty($request->q)) {
            $search = $request->q;
            $query->where(function($q) use ($search) {
                $q->where('a.trustmark_id', 'like', "%{$search}%")
                ->orWhere(DB::raw('LOWER(a.business_name)'),'like',"%".strtolower($search)."%")
                ->orWhere(DB::raw('LOWER(a.reg_num)'),'like',"%".strtolower($search)."%")
                ->orWhere(DB::raw('LOWER(a.building_no)'),'like',"%".strtolower($search)."%")
                ->orWhere(DB::raw('LOWER(a.tin)'),'like',"%".strtolower($search)."%")
                ->orWhere(DB::raw('LOWER(b.name)'),'like',"%".strtolower($search)."%")
                ->orWhere(DB::raw('LOWER(a.status)'),'like',"%".strtolower($search)."%")
                ->orWhere(DB::raw('LOWER(a.pic_email)'),'like',"%".strtolower($search)."%")
                ->orWhere(DB::raw('LOWER(a.pic_ctc_no)'),'like',"%".strtolower($search)."%")
                ->orWhere(DB::raw('LOWER(c.name)'),'like',"%".strtolower($search)."%")
                ->orWhere(DB::raw('LOWER(a.submit_date)'),'like',"%".strtolower($search)."%")
                ->orWhere(DB::raw('LOWER(a.date_approved)'),'like',"%".strtolower($search)."%")
                ->orWhere(DB::raw('LOWER(a.complete_address)'),'like',"%".strtolower($search)."%");
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
                    DB::raw("IFNULL(a.trustmark_id,'') AS `SecurityNo`"),
                    DB::raw("IFNULL(a.business_name,'') AS `BusinessName`"),
                    DB::raw("IFNULL(a.reg_num,'') AS `RegistrationNo`"),
                    DB::raw("IFNULL(
                        CASE a.corporation_type
                            WHEN 1 THEN 'Sole Proprietor'
                            WHEN 2 THEN 'Corporation/Partnership'
                            WHEN 3 THEN 'Cooperative'
                        END, ''
                    ) AS `BusinessType`"),
                    DB::raw("IFNULL(a.tin,'') AS `TIN`"),
                    DB::raw("IFNULL(burl.url,'') AS `business_urls`"),
                    DB::raw("IFNULL(b.name,'') AS `Representative`"),
                    DB::raw("IFNULL(a.amount,'') AS `Payment`"),
                    DB::raw("IFNULL(a.admin_remarks,'') AS `Remarks`"),
                    DB::raw("IFNULL(a.status,'') AS `Status`"),
                    DB::raw("IFNULL(a.pic_email,'') AS `EmailAddress`"),
                    DB::raw("IFNULL(a.pic_ctc_no,'') AS `ContactNo`"),
                    DB::raw("IFNULL(c.name,'') AS `Evaluator`"),
                    DB::raw("IFNULL(DATE(a.submit_date),'') AS `DateSubmitted`"),
                    DB::raw("IFNULL(DATE(a.date_approved),'') AS `DateApproved`"),
                    DB::raw("IFNULL(DATE(a.date_issued),'') AS `DateIssued`"),
                    DB::raw("IFNULL(DATE(a.date_disapproved),'') AS `Datedisapproved`"),
                    DB::raw("IFNULL(DATE(a.date_returned),'') AS `Datereturned`"),
                    DB::raw("IFNULL(DATE(a.created_at),'') AS `Datecreated_at`"),
                    DB::raw("IFNULL(a.payment_channel,'') AS `Channel`"),
                    DB::raw("IFNULL(a.complete_address,'') AS `Complete_Address`"),
                    DB::raw("IFNULL(d.brgy_name,'') AS `Barangay`"),
                    DB::raw("IFNULL(d.mun_desc,'') AS `Municipality_City`"),
                    DB::raw("IFNULL(d.prov_desc,'') AS `Province`"),
                    DB::raw("IFNULL(d.reg_region,'') AS `Region`"),
                    DB::raw("CASE a.is_bmbe 
                        WHEN 0 THEN 'No' 
                        WHEN 1 THEN 'Yes' 
                     END AS `withBMBE`")
                ])
            ->leftJoin('users as b', 'a.user_id', '=', 'b.id')
            ->leftJoin('users as c', 'a.evaluator_id', '=', 'c.id')
            ->leftJoin('barangays as d', 'a.barangay_id', '=', 'd.id')
            ->leftJoin('business_url as burl', 'burl.busn_id', '=', 'a.id')
            ->where('a.is_active', 1);
            // ->orderByDesc('a.id');
            if ($request->filled('status')) {
                $query->where('a.status', $request->status);
            }

            $status = $request->status;
            if (!empty($startdate)) {
                $s = explode('-', $startdate);
                $startdate = date('Y-m-d', strtotime("$s[2]-$s[1]-$s[0]"));
            }

            if (!empty($enddate)) {
                $e = explode('-', $enddate);
                $enddate = date('Y-m-d', strtotime("$e[2]-$e[1]-$e[0]"));
            }
            switch ($status) {

                case 'RETURNED':
                    $dateField = 'a.date_returned';
                    break;

                case 'APPROVED':
                    $dateField = 'a.date_approved';
                    break;

                case 'DISAPPROVED':
                    $dateField = 'a.date_disapproved';
                    break;

                case 'UNDER EVALUATION':
                    $dateField = 'a.submit_date';
                    break;

                case 'DRAFT':
                    $dateField = 'a.created_at';
                    break;

                default:
                    $dateField = 'a.created_at';
                    break;
            }
            if (!empty($startdate)) {
                $query->whereDate($dateField, '>=', $startdate);
            }

            if (!empty($enddate)) {
                $query->whereDate($dateField, '<=', $enddate);
            }

        
        if (!empty($request->q)) {
            $search = $request->q;
            $query->where(function($q) use ($search) {
                $q->where('a.trustmark_id', 'like', "%{$search}%")
                ->orWhere(DB::raw('LOWER(a.business_name)'),'like',"%".strtolower($search)."%")
                ->orWhere(DB::raw('LOWER(a.reg_num)'),'like',"%".strtolower($search)."%")
                ->orWhere(DB::raw('LOWER(a.building_no)'),'like',"%".strtolower($search)."%")
                ->orWhere(DB::raw('LOWER(a.tin)'),'like',"%".strtolower($search)."%")
                ->orWhere(DB::raw('LOWER(b.name)'),'like',"%".strtolower($search)."%")
                ->orWhere(DB::raw('LOWER(a.status)'),'like',"%".strtolower($search)."%")
                ->orWhere(DB::raw('LOWER(a.pic_email)'),'like',"%".strtolower($search)."%")
                ->orWhere(DB::raw('LOWER(a.pic_ctc_no)'),'like',"%".strtolower($search)."%")
                ->orWhere(DB::raw('LOWER(c.name)'),'like',"%".strtolower($search)."%")
                ->orWhere(DB::raw('LOWER(a.submit_date)'),'like',"%".strtolower($search)."%")
                ->orWhere(DB::raw('LOWER(a.date_approved)'),'like',"%".strtolower($search)."%")
                ->orWhere(DB::raw('LOWER(a.complete_address)'),'like',"%".strtolower($search)."%");
                });
        }
        

        $columns = [
            0 => 'a.trustmark_id',                      
            1 => 'a.trustmark_id',   
            2 => 'a.business_name',
            3 => 'a.reg_num',
            4 => 'a.corporation_type',
            5 => 'a.tin',
            6 => 'b.name',
            7 => 'a.amount',
            8 => 'a.admin_remarks',
            9 => 'a.status',        
            10 => 'a.pic_email',     
            11 => 'a.pic_ctc_no',
            12 => 'c.name',     
            13 => 'a.submit_date',
            14 => 'a.date_approved',                
            15 => 'a.date_issued', 
            16 => 'a.date_disapproved',
            17 => 'a.date_returned',
            18 => 'a.created_at', 
            19 =>'a.payment_channel',
            20 =>'a.complete_address',
            21 =>'d.brgy_name',
            22 =>'mun_desc',
            23 =>'prov_desc',
            24 =>'reg_region',
            25=>'is_bmbe',
            26=>'burl.url'

        ];
        $totalRecords = DB::table('businesses as a')->count();
        $totalFiltered = $query->count();
        $limit = $request->input('length');   
        $start = $request->input('start');   
        $orderColumnIndex = $request->input('order.0.column');
        $orderDirection = $request->input('order.0.dir', 'asc'); 

        $orderColumn = $columns[$orderColumnIndex] ?? null;

        if (!empty($orderColumn)) {
            $query->orderBy($orderColumn, $orderDirection);
        }

        $query->skip($start)->take($limit); 
        $query->orderByDesc('a.id');
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
                    'Payment' => $row->Payment ?? ' ',
                    'Channel' => $row->Channel ?? ' ',
                    'Remarks' => $row->Remarks ?? ' ',
                    'Status' => $row->Status ?? ' ',
                    'EmailAddress' => $row->EmailAddress ?? ' ',
                    'ContactNo' => $row->ContactNo ?? ' ',
                    'Evaluator' => $row->Evaluator ?? ' ',
                    'DateSubmitted' => $row->DateSubmitted ?? ' ',
                    'DateApproved' => $row->DateApproved ?? ' ',
                    'DateIssued' => $row->DateIssued ?? ' ',
                    'Datedisapproved' => $row->Datedisapproved ?? ' ',
                    'Datereturned' => $row->Datereturned ?? ' ',
                    'Datecreated_at' => $row->Datecreated_at ?? ' ',
                    'Complete_Address' => $row->Complete_Address ?? ' ',
                    'Barangay' => $row->Barangay ?? ' ',
                    'Municipality_City' => $row->Municipality_City ?? ' ',
                    'Province' => $row->Province ?? ' ',
                    'Region' => $row->Region ?? ' ',
                    'witbemb' => $row->withBMBE ?? ' ',
                    'business_url' => $row->business_urls ?? ' '
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
