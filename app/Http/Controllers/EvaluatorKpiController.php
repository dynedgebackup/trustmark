<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\EvaluatorKpi;
use Illuminate\Validation\Rule;
use File;
use Carbon\Carbon;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Support\Facades\Auth;
use DB;

class EvaluatorKpiController extends Controller
{
    public $arrapp_code = array(""=>"");
    public $arrfee_id = array(""=>"");
    public function __construct(){
		$this->MenuGroup = new EvaluatorKpi(); 
    }
    public function index()
    {
        $user = DB::table('user_admins')
        ->where('user_id', Auth::id())
        ->select('is_evaluator', 'is_admin')
        ->first();

        $is_evaluator = (int) ($user->is_evaluator ?? 0);
        $is_admin     = (int) ($user->is_admin ?? 0);
        return view('Evaluator-KPI.index', compact('is_evaluator','is_admin'));
    }
    
    public function exportAll(Request $request)
    {
        $startdate = $request->input('fromdate');
        $enddate   = $request->input('todate');

        $query = DB::table('business_performance as bp')
            ->leftJoin('users as u', 'u.id', '=', 'bp.user_id')
            ->whereNotNull('bp.user_id');
        if (!empty($startdate)) {
            $sdate = date('Y-m-d', strtotime(str_replace('/', '-', $startdate)));
            $query->whereDate('bp.process_date', '>=', $sdate);
        }
        if (!empty($enddate)) {
            $edate = date('Y-m-d', strtotime(str_replace('/', '-', $enddate)));
            $query->whereDate('bp.process_date', '<=', $edate);
        }
        
        if (!empty($request->q)) {
            $search = strtolower($request->q);
            $query->where(function ($q) use ($search) {
                $q->where(DB::raw('LOWER(u.name)'), 'like', "%{$search}%")
                    ->orWhere(DB::raw('LOWER(bp.process)'), 'like', "%{$search}%")
                    ->orWhere(DB::raw('LOWER(DATE(bp.process_date))'), 'like', "%{$search}%");
            });
        }

        $user = DB::table('user_admins')
        ->where('user_id', Auth::id())
        ->select('is_evaluator', 'is_admin')
        ->first();
        if ($user && $user->is_evaluator == 1 && $user->is_admin == 0) {
            $query->where('bp.user_id', Auth::id());
        }
        else {
            if ($request->filled('user_id_filter')) {
                $query->where('bp.user_id', $request->user_id_filter);
            }
        }
        $query->select([
            'u.id as Evaluator_ID',
            'u.name as Evaluator',
            DB::raw("MAX(DATE(bp.process_date)) AS LastDate"),
            DB::raw("SUM(CASE WHEN bp.process='APPROVED' THEN 1 ELSE 0 END) AS Approved"),
            DB::raw("SUM(CASE WHEN bp.process='RETURNED' THEN 1 ELSE 0 END) AS Returned"),
            DB::raw("SUM(CASE WHEN bp.process='DISAPPROVED' THEN 1 ELSE 0 END) AS Disapproved"),
            DB::raw("SUM(CASE WHEN bp.process='ON-HOLD' THEN 1 ELSE 0 END) AS `On-Hold`"),
            DB::raw("SUM(CASE WHEN bp.process='RE-ACTIVATED' THEN 1 ELSE 0 END) AS `Re-Activated`"),
            DB::raw("SUM(CASE WHEN bp.process='ARCHIVED' THEN 1 ELSE 0 END) AS Archived"),
        ])
        ->groupBy('u.id', 'u.name')
        ->orderBy('u.name', 'asc');

        $data = $query->get();

        return response()->json([
            'data' => $data
        ]);
    }


    public function getEvaluatorKpiList(Request $request)
    {
        $startdate = $request->input('fromdate');
        $enddate   = $request->input('todate');

        $query = DB::table('business_performance as bp')
        ->select([
            'u.id as Evaluator_ID',
            'u.name as Evaluator',
            DB::raw("MAX(DATE(bp.process_date)) AS LastDate"),
            DB::raw("SUM(CASE WHEN bp.process='APPROVED' THEN 1 ELSE 0 END) AS Approved"),
            DB::raw("SUM(CASE WHEN bp.process='RETURNED' THEN 1 ELSE 0 END) AS Returned"),
            DB::raw("SUM(CASE WHEN bp.process='DISAPPROVED' THEN 1 ELSE 0 END) AS Disapproved"),
            DB::raw("SUM(CASE WHEN bp.process='ON-HOLD' THEN 1 ELSE 0 END) AS `On-Hold`"),
            DB::raw("SUM(CASE WHEN bp.process='RE-ACTIVATED' THEN 1 ELSE 0 END) AS `Re-Activated`"),
            DB::raw("SUM(CASE WHEN bp.process='ARCHIVED' THEN 1 ELSE 0 END) AS Archived"),
        ])
        ->leftJoin('users as u', 'u.id', '=', 'bp.user_id')
        ->whereNotNull('bp.user_id')
        ->groupBy('u.id', 'u.name');
        $user = DB::table('user_admins')
        ->where('user_id', Auth::id())
        ->select('is_evaluator', 'is_admin')
        ->first();
        if ($user && $user->is_evaluator == 1 && $user->is_admin == 0) {
            $query->where('bp.user_id', Auth::id());
        }
        else {
            if ($request->filled('user_id_filter')) {
                $query->where('bp.user_id', $request->user_id_filter);
            }
        }
        if (!empty($startdate)) {
            $sdate = date('Y-m-d', strtotime(str_replace('/', '-', $startdate)));
            $query->whereDate('bp.process_date', '>=', $sdate);
        }
        if (!empty($enddate)) {
            $edate = date('Y-m-d', strtotime(str_replace('/', '-', $enddate)));
            $query->whereDate('bp.process_date', '<=', $edate);
        }
        if (!empty($request->q)) {
            $search = strtolower($request->q);

            $query->where(function ($q) use ($search) {
                $q->where(DB::raw('LOWER(u.name)'), 'like', "%{$search}%")
                    ->orWhere(DB::raw('LOWER(bp.process)'), 'like', "%{$search}%")
                    ->orWhere(DB::raw('LOWER(DATE(bp.process_date))'), 'like', "%{$search}%");
            });
        }

        $totalFiltered = $query->count();
        $limit = $request->input('length');
        $start = $request->input('start');
        $query->skip($start)->take($limit);
        $orderColumnIndex = $request->input('order.0.column');
        $orderDirection   = $request->input('order.0.dir', 'asc');

        $columns = [
            0 => 'u.id',
            1 => 'u.name',
            2 => 'bp.process_date',
            3 => 'Approved',
            4 => 'Returned',
            5 => 'Disapproved', 
            6 => 'On-Hold',
            7 => 'Archived',
            8 => 'Re-Activated',
            9 => null,
        ];

        if (!empty($columns[$orderColumnIndex])) {
            $query->orderBy($columns[$orderColumnIndex], $orderDirection);
        }

        $rows = $query->get();
        $data = [];
        $i = $start + 1;

        foreach ($rows as $row) {
            $data[] = [
                'no'            => $i++,
                'Evaluator_ID'  => $row->Evaluator_ID,
                'Evaluator'     => $row->Evaluator,
                'Date'         => $row->LastDate,  
                'Approved'      => $row->Approved,
                'Returned'      => $row->Returned,
                'Disapproved'   => $row->Disapproved,
                'On-Hold'       => $row->{'On-Hold'},
                'acrhived'  => $row->{'Archived'},
                'Re-Activated'  => $row->{'Re-Activated'},
                'action' => '<a href="#" 
                    class="mx-3 btn btn-sm align-items-center viewEvaluatorBtn"
                    data-id="'.$row->Evaluator_ID.'"
                    title="View Business" 
                    style="background:#09325d;color:#fff;">
                    <i class="fas fa-search"></i>
                </a>',
            ];
        }

        return response()->json([
            'draw'            => intval($request->input('draw')),
            'recordsTotal'    => $totalFiltered,
            'recordsFiltered' => $totalFiltered,
            'data'            => $data
        ]);
    }


    public function getEvaluatorBusinessList(Request $request, $id)
    {
        $query = DB::table('business_performance as bp')
            ->leftJoin('businesses as b', 'b.id', '=', 'bp.busn_id')
            ->select([
                'b.trustmark_id',
                'b.business_name',
                'bp.process',
                'bp.process_date',
            ])
            ->where('bp.user_id', $id);
        if (!empty($request->status)) {
            $query->where('bp.process', $request->status);
        }

        if (!empty($request->viewfromdate)) {
            $query->whereDate('bp.process_date', '>=', $request->viewfromdate);
        }

        if (!empty($request->viewtodate)) {
            $query->whereDate('bp.process_date', '<=', $request->viewtodate);
        }

        if (!empty($request->viewq)) {
            $search = strtolower($request->viewq);
            $query->where(DB::raw('LOWER(b.business_name)'), 'like', "%{$search}%")
            ->orWhere(DB::raw('LOWER(b.trustmark_id)'), 'like', "%{$search}%");
        }
        $totalFiltered = $query->count();
        $limit = $request->input('length');
        $start = $request->input('start');
        $query->skip($start)->take($limit);
        $orderColumnIndex = $request->input('order.0.column');
        $orderDirection   = $request->input('order.0.dir', 'asc');

        $columns = [
            1 => 'b.trustmark_id',
            2 => 'b.business_name',
            3 => 'bp.process',
            4 => 'bp.process_date',
        ];

        if (!empty($columns[$orderColumnIndex])) {
            $query->orderBy($columns[$orderColumnIndex], $orderDirection);
        }
        $rows = $query->get();
        $data = [];
        $i = $start + 1;

        foreach ($rows as $row) {
            $data[] = [
                'no'      => $i++,
                'trustmark_id' => $row->trustmark_id,
                'business_name' => $row->business_name,
                'process' => $row->process,
                'process_date' => date('Y-m-d', strtotime($row->process_date)),
            ];
        }

        return response()->json([
            'draw'            => intval($request->input('draw')),
            'recordsTotal'    => $totalFiltered,
            'recordsFiltered' => $totalFiltered,
            'data'            => $data,
        ]);
    }
    public function viewexportAll(Request $request)
    {   
        $id = $request->id;
        $query = DB::table('business_performance as bp')
            ->leftJoin('businesses as b', 'b.id', '=', 'bp.busn_id')
            ->select([
                'b.trustmark_id',
                'b.business_name',
                'bp.process',
                'bp.process_date',
            ])
            ->where('bp.user_id', $id);
        if (!empty($request->status)) {
            $query->where('bp.process', $request->status);
        }

        if (!empty($request->viewfromdate)) {
            $query->whereDate('bp.process_date', '>=', $request->viewfromdate);
        }

        if (!empty($request->viewtodate)) {
            $query->whereDate('bp.process_date', '<=', $request->viewtodate);
        }

        if (!empty($request->viewq)) {
            $search = strtolower($request->viewq);
            $query->where(DB::raw('LOWER(b.business_name)'), 'like', "%{$search}%")
            ->orWhere(DB::raw('LOWER(b.trustmark_id)'), 'like', "%{$search}%");
        }

        $data = $query->get();

        return response()->json([
            'data' => $data
        ]);
    }
}
