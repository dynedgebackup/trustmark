<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\ApplicationStatusCannedMessage;
use Illuminate\Validation\Rule;
use File;
use Carbon\Carbon;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Support\Facades\Auth;
use DB;

class ApplicationStatusCannedMessageController extends Controller
{
    public $arrapp_code = array("" => "");
    public $arrfee_id = array("" => "");
    public function __construct()
    {
        $this->ApplicationStatusCannedMessage = new ApplicationStatusCannedMessage();
        $this->data = array('id' => '', 'app_status_id' => '', 'description' => '', 'remarks' => '', 'status' => '');
    }
    public function index()
    {
        return view('ApplicationStatusCannedMessage.index');
    }

    public function applicationStatusAjaxList(Request $request)
    {
        $search = $request->input('search');
        $arrRes = $this->ApplicationStatusCannedMessage->applicationStatusAjaxList($search);
        $arr = array();
        foreach ($arrRes['data'] as $key => $val) {
            $arr['data'][$key]['id'] = $val->id;
            $arr['data'][$key]['text'] = $val->name;
        }
        $arr['data_cnt'] = $arrRes['data_cnt'];
        echo json_encode($arr);
    }

    public function ActiveInactive(Request $request)
    {
        $id = $request->input('id');
        $is_activeinactive = $request->input('is_activeinactive');
        $data = array('status' => $is_activeinactive);
        $this->ApplicationStatusCannedMessage->updateActiveInactive($id, $data);
    }
    public function store(Request $request)
    {
        $data = (object)$this->data;
        $arrapp_code = $this->arrapp_code;
        $arrfee_id = $this->arrfee_id;
        if ($request->input('id') > 0 && $request->input('submit') == "") {
            $data = $this->ApplicationStatusCannedMessage->getEditDetails($request->input('id'));
            $arrapp_codes = $this->ApplicationStatusCannedMessage->getAppStatusDetails($data->app_status_id);
            foreach ($arrapp_codes as $val) {
                $arrapp_code[$val->id] = $val->name;
            }
        }

        if ($request->input('submit') != "") {
            foreach ((array)$this->data as $key => $val) {
                $this->data[$key] = $request->input($key);
            }
            $prov_no = $request->input('prov_no');
            $this->data['modified_by'] = Auth::id();
            $this->data['modified_date'] = date('Y-m-d H:i:s');
            if ($request->input('id') > 0) {
                $this->ApplicationStatusCannedMessage->updateData($request->input('id'), $this->data);
                $lastInsertedId = $request->input('id');
                $success_msg = 'Updated successfully.';
            } else {
                $this->data['created_by'] = Auth::id();
                $this->data['created_date'] = date('Y-m-d H:i:s');
                $lastInsertedId = $this->ApplicationStatusCannedMessage->addData($this->data);
                $success_msg = 'Added successfully.';
            }
            return redirect()->route('ApplicationStatusCannedMessage.index')->with('success', __($success_msg));
        }



        return view('ApplicationStatusCannedMessage.create', compact('data', 'arrapp_code', 'arrfee_id'));
    }

    public function getList(Request $request)
    {
        $query = DB::table('application_canned_messages AS a')
            ->leftJoin('application_status AS b', 'b.id', '=', 'a.app_status_id')
            ->select('a.id', 'a.description', 'a.status', 'b.name');
        if ($request->filled('app_status_id')) {
            $query->where('a.app_status_id', $request->app_status_id);
        }
        if ($request->filled('status')) {
            $query->where('a.status', $request->status);
        }
        if (!empty($request->q)) {
            $search = $request->q;
            $query->where(function ($q) use ($search) {
                $q->where('b.name', 'like', "%{$search}%")
                    ->orWhere(DB::raw('LOWER(a.description)'), 'like', "%" . strtolower($search) . "%");
            });
        }
        $totalRecords = DB::table('application_canned_messages AS a')->count();
        $totalFiltered = $query->count();
        $limit = $request->input('length');
        $start = $request->input('start');
        $query->skip($start)->take($limit);
        $orderColumnIndex = $request->input('order.0.column');
        $orderDirection = $request->input('order.0.dir', 'asc');

        $columns = [
            0 => null,
            1 => 'b.name',
            2 => 'a.description',
            3 => 'a.status',
            4 => null
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
                'description' => $row->description,
                'status' => ($row->status == 1 ? '<span class="btn btn-success" style="padding: 0.1rem 0.5rem !important;background: none;font-size: 12px;border-radius: 11px;">Active</span>
                                ' : '<span class="btn btn-warning" style="padding: 0.1rem 0.5rem !important;background: none;font-size: 12px;border-radius: 11px;">Cancel</span>'),
                'action' => '<a href="#" 
                                class="mx-3 btn btn-sm align-items-center" 
                                data-url="' . url('/master-data/ApplicationStatusCannedMessage/store?id=' . $row->id) . '" 
                                data-ajax-popup="true" 
                                data-size="lg" 
                                data-bs-toggle="tooltip" 
                                title="Edit" 
                                data-title="Manage Application Status (Canned Message)" style="background: #09325d !important;color: #fff;">
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

    public function getReasons($status_id)
    {
        $reasons = DB::table('application_canned_messages')
            ->where('app_status_id', $status_id)
            ->where('status', 1)
            ->orderBy('description', 'ASC')
            ->pluck('description', 'id'); 

        return response()->json($reasons);
    }
}
