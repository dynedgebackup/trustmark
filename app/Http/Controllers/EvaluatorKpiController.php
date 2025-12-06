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
        return view('Evaluator-KPI.index');
    }
    
    public function exportAll(Request $request)
    {
        $startdate = $request->input('fromdate');
        $enddate = $request->input('todate');
        $query = DB::table('business_fees as a')
            
            ->join('businesses as b', 'a.busn_id', '=', 'b.id')
            ->join('payments as c', 'a.payment_id', '=', 'c.id')
            ->join('users as d', 'b.user_id', '=', 'd.id')
            ->where('a.payment_id', '>', 0);

            if ($request->filled('fee_id')) {
                $query->where('a.fee_id', $request->fee_id);
            }
            if (!empty($startdate)) {
                $sdate = explode('-', $startdate);
                $startdate = date('Y-m-d', strtotime("{$sdate[2]}-{$sdate[1]}-{$sdate[0]}"));
                $query->whereDate('a.create_date', '>=', trim($startdate));
            }

            if (!empty($enddate)) {
                $edate = explode('-', $enddate);
                $enddate = date('Y-m-d', strtotime("{$edate[2]}-{$edate[1]}-{$edate[0]}"));
                $query->whereDate('a.create_date', '<=', trim($enddate));
            }
            if (!empty($request->q)) {
                $search = $request->q;
                $query->where(function($q) use ($search) {
                    $q->where('b.business_name', 'like', "%{$search}%")
                    ->orWhere(DB::raw('LOWER(b.trustmark_id)'),'like',"%".strtolower($search)."%")
                    ->orWhere(DB::raw('LOWER(a.fee_name)'),'like',"%".strtolower($search)."%")
                    ->orWhere(DB::raw('LOWER(c.transaction_id)'),'like',"%".strtolower($search)."%")
                    ->orWhere(DB::raw('LOWER(a.amount)'),'like',"%".strtolower($search)."%")
                    ->orWhere(DB::raw('LOWER(a.create_date)'),'like',"%".strtolower($search)."%")
                    ->orWhere(DB::raw('LOWER(d.name)'),'like',"%".strtolower($search)."%");
                    });
            }

        $data = $query->select(
            'b.business_name as BusinessName',
            'b.trustmark_id as SecurityNo',
            'a.fee_name as PaymentDescription',
            'c.transaction_id as TransactionID',
            'a.amount as Amount',
            'a.create_date as Date',
            'd.name as PaymentBy',
            'a.fee_id'
        )->orderBy('b.trustmark_id', 'asc')->get();

        return response()->json(['data' => $data]);
    }

    public function getEvaluatorKpiList(Request $request)
    {
        $startdate = $request->input('fromdate');
        $enddate   = $request->input('todate');

        $query = DB::table('business_performance as bp')
        ->select([
            'u.id as Evaluator_ID',
            'u.name as Evaluator',
            'bp.busn_id',
            DB::raw("MAX(DATE(bp.process_date)) AS LastDate"),
            DB::raw("SUM(CASE WHEN bp.process='APPROVED' THEN 1 ELSE 0 END) AS Approved"),
            DB::raw("SUM(CASE WHEN bp.process='RETURNED' THEN 1 ELSE 0 END) AS Returned"),
            DB::raw("SUM(CASE WHEN bp.process='DISAPPROVED' THEN 1 ELSE 0 END) AS Disapproved"),
            DB::raw("SUM(CASE WHEN bp.process='ON-HOLD' THEN 1 ELSE 0 END) AS `On-Hold`"),
            DB::raw("SUM(CASE WHEN bp.process='RE-ACTIVATED' THEN 1 ELSE 0 END) AS `Re-Activated`"),
        ])
        ->leftJoin('users as u', 'u.id', '=', 'bp.user_id')
        ->whereNotNull('bp.user_id')
        ->groupBy('u.id', 'u.name', 'bp.busn_id');
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
            2 => null, 
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
                'Re-Activated'  => $row->{'Re-Activated'},
                'action' => '<a href="#" 
                    class="mx-3 btn btn-sm align-items-center viewEvaluatorBtn"
                    data-id="'.$row->busn_id.'"
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


public function getEvaluatorBusinessList($id)
{
    $rows = DB::table('business_performance as bp')
        ->leftJoin('businesses as b', 'b.id', '=', 'bp.busn_id')
        ->select([
            'b.trustmark_id',
            'b.business_name',
            'bp.process',
            'bp.process_date'])
        ->where('bp.busn_id', $id)
        ->orderBy('bp.process_date', 'DESC')
        ->get();

    $html = '';
    $i = 1;

    foreach ($rows as $row) {
        $html .= '<tr>
            <td>'.$i++.'</td>
            <td>'.$row->trustmark_id.'</td>
            <td>'.$row->business_name.'</td>
            <td>'.$row->process.'</td>
            <td>'.date("Y-m-d", strtotime($row->process_date)).'</td>
        </tr>';
    }

    return response()->json(['html' => $html]);
}

    
}
