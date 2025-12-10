<?php

namespace App\Http\Controllers;

use App\Models\Email;
use App\Models\CronJob;
use App\Models\Setting;
use App\Models\Business;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Http;
use App\Models\BusinessFollowupReturns;
use App\Models\BusinessFollowupUnpaids;
use App\Models\BusinessFollowups;
use Illuminate\Support\Facades\Storage;

use Carbon\Carbon;

class CronJobController extends Controller
{
    public $scheduleType = [];

    public $postdata = [];

    public $arrMonth = [];

    public $arrDay = [];
    public $arrName = [
        '0'=>'',
        '1'=>'First',
        '2'=>'Second',
        '3'=>'Third',
        '4'=>'Final'
    ];

    public $arrMenugroup = ['' => 'Please Select'];

    public $scheduleValue = ['' => 'Please Select'];

    protected $business;

    protected $followUpReturn;

    protected $followUps;

    protected $email;

    public function __construct(Business $business, BusinessFollowupReturns $followUpReturn, BusinessFollowups $followUps, Email $email)
    {
        $this->business = $business;
        $this->followUpReturn = $followUpReturn;
        $this->followUps = $followUps;
        $this->email = $email;

        $this->_CronJob = new CronJob;
        $this->data = ['id' => '', 'department' => '', 'description' => '', 'remarks' => '', 'schedule_type' => '', 'schedule_value' => '', 'url' => '', 'day' => '', 'hours' => '', 'status' => ''];
        $this->slugs = 'cron-job';
        $this->scheduleType = [
            '' => 'Please Select',
            '1' => 'Minute',
            '2' => 'Hour',
            '7' => 'Daily',
            '3' => 'Day of the month',
            '4' => 'Month',
            '5' => 'Day of the week',
            '6' => 'Yearly',
        ];
        $this->arrMonth = [
            1 => 'January',
            2 => 'February',
            3 => 'March',
            4 => 'April',
            5 => 'May',
            6 => 'June',
            7 => 'July',
            8 => 'August',
            9 => 'September',
            10 => 'October',
            11 => 'November',
            12 => 'December',
        ];
        $this->arrDay = [
            1 => 'Sunday',
            2 => 'Monday',
            3 => 'Tuesday',
            4 => 'Wednesday',
            5 => 'Thursday',
            6 => 'Friday',
            7 => 'Saturday',
        ];
    }

    public function index()
    {
        return view('CronJob.index');
    }

    public function store(Request $request)
    {
        $scheduleType = $this->scheduleType;
        $scheduleValue = $this->scheduleValue;

        $data = (object) $this->data;

        if ($request->input('id') > 0 && $request->input('submit') == '') {
            $data = $this->_CronJob->getEditDetails($request->input('id'));
        }

        if ($request->input('submit') != '') {
            foreach ((array) $this->data as $key => $val) {
                $this->data[$key] = $request->input($key);
            }
            $this->data['updated_by'] = Auth::id();
            $this->data['updated_at'] = date('Y-m-d H:i:s');
            if ($request->input('id') > 0) {
                $this->_CronJob->updateData($request->input('id'), $this->data);
                $success_msg = 'Updated successfully.';
            } else {
                $this->data['created_by'] = Auth::id();
                $this->data['created_at'] = date('Y-m-d H:i:s');
                $this->data['status'] = 1;
                $request->id = $this->_CronJob->addData($this->data);
                $success_msg = 'Added successfully.';
            }

            return redirect()->route('cron-job.index')->with('success', __($success_msg));
        }

        return view('CronJob.create', compact('data', 'scheduleType', 'scheduleValue'));
    }

    public function getList(Request $request)
    {
        $query = $sql = DB::table('cron_job')
            ->select('cron_job.*');
        $department_id = $request->input('department_id');
        if ($request->filled('department_id')) {
            $query->where('department', $department_id);
        }

        if (! empty($request->q)) {
            $search = $request->q;
            $query->where(function ($q) use ($search) {
                $q->where('department', 'like', "%{$search}%")
                    // ->orWhere(DB::raw('LOWER(url)'), 'like', "%" . strtolower($q) . "%")
                    ->orWhere(DB::raw('LOWER(url)'), 'like', '%'.strtolower($search).'%')
                    ->orWhere(DB::raw('LOWER(remarks)'), 'like', '%'.strtolower($q).'%')
                    ->orWhere(DB::raw('LOWER(description)'), 'like', '%'.strtolower($q).'%')
                    ->orWhere(DB::raw('LOWER(schedule_type)'), 'like', '%'.strtolower($q).'%')
                    ->orWhere(DB::raw('LOWER(schedule_value)'), 'like', '%'.strtolower($q).'%');
            });
        }
        $totalRecords = DB::table('cron_job')->count();
        $totalFiltered = $query->count();
        $limit = $request->input('length');
        $start = $request->input('start');
        $query->skip($start)->take($limit);
        $orderColumnIndex = $request->input('order.0.column');
        $orderDirection = $request->input('order.0.dir', 'asc');

        $columns = [
            0 => null,
            1 => 'department',
            2 => 'url',
            3 => 'description',
            4 => 'remarks',
            5 => 'schedule_type',
            6 => 'schedule_value',
            7 => 'status',
            8 => null,
        ];
        $orderColumn = $columns[$orderColumnIndex] ?? null;

        if (! empty($orderColumn)) {
            $query->orderBy($orderColumn, $orderDirection);
        }

        $fees = $query->get();
        $data = [];
        $i = $start + 1;

        foreach ($fees as $row) {
            $color = (strpos($row->response, '200') !== false) ? 'color:green' : 'color:red';

            $data[] = [
                'no' => $i++,
                'department' => $row->department ?? '-',
                'url' => $row->url,
                'description' => $row->description,
                'response' => "<div class='showLess' style='{$color}'>{$row->response}</div>",
                'quickRun' => '<span class="btn btn-success quickRun" style="padding: 0.1rem 0.5rem !important;font-size: 12px;color: #fff;" cid="'.$row->id.'">Quick Run</span>',
                'lastExecuted' => $row->last_run_datetime,
                'schedule_type' => $this->scheduleType[$row->schedule_type],
                'schedule_value' => $row->schedule_value,
                'status' => ($row->status == 1
                    ? '<span class="btn btn-success" style="padding: 0.1rem 0.5rem !important;background: none;font-size: 12px;border-radius: 11px;">Active</span>'
                    : '<span class="btn btn-warning" style="padding: 0.1rem 0.5rem !important;background: none;font-size: 12px;border-radius: 11px;">Cancel</span>'
                ),
                'action' => '<a href="#" 
                                        class="mx-3 btn btn-sm align-items-center" 
                                        data-url="'.url('/setting/cron-job/store?id='.$row->id).'" 
                                        data-ajax-popup="true" 
                                        data-size="lg" 
                                        data-bs-toggle="tooltip" 
                                        title="Edit" 
                                        data-title="Manage Cron-Job" 
                                        style="background: #09325d !important;color: #fff;">
                                            <i class="fas fa-pencil "></i>
                                    </a>',
            ];
        }

        return response()->json([
            'draw' => intval($request->input('draw')),
            'recordsTotal' => $totalRecords,
            'recordsFiltered' => $totalFiltered,
            'data' => $data,
        ]);
    }

    public function getScheduleVal(Request $request)
    {
        $schedule_type_id = $request->input('schedule_type_id');
        $h_hours = $request->input('h_hours');
        $h_day = $request->input('h_day');
        $schedule_val = $request->input('schedule_val');

        $data = [];
        switch ($schedule_type_id) {
            case '2':
                for ($i = 1; $i <= 24; $i++) {
                    $data[] = ['key' => $i, 'value' => $i];
                }
                break;

            case '3':
                for ($i = 1; $i <= 31; $i++) {
                    $data[] = ['key' => $i, 'value' => $i];
                }
                break;

            case '4':
                for ($i = 1; $i <= 12; $i++) {
                    $data[] = ['key' => $i, 'value' => $this->arrMonth[$i]];
                }
                break;

            case '5':
                for ($i = 1; $i <= 7; $i++) {
                    $data[] = ['key' => $i, 'value' => $this->arrDay[$i]];
                }
                break;

            case '6':
                for ($i = 1; $i <= 12; $i++) {
                    $data[] = ['key' => $i, 'value' => $this->arrMonth[$i]];
                }
                break;

            default:
                for ($i = 1; $i <= 60; $i++) {
                    $data[] = ['key' => $i, 'value' => $i];
                }
                break;
        }
        $comd = 12;
        if ($schedule_type_id == 3 || $schedule_type_id == 5) {
            $comd = 6;
        } elseif ($schedule_type_id == 4 || $schedule_type_id == 6) {
            $comd = 4;
        }
        $select = (! empty($schedule_type_id)) ? 'Select' : '';
        $displayName = $this->scheduleType[$schedule_type_id];
        if ($schedule_type_id == 6) {
            $displayName = 'Month';
        }
        if ($schedule_type_id != 7) {
            ?>
            <div class="col-md-<?= $comd ?>" id="divSchduleValue">
                <div class="form-group" id="schedule_value_parrent">
                    <label class="form-label"><?= $select ?> <?= $displayName ?></label><span class="text-danger">*</span>
                    <div class="form-icon-user">
                        <select id="schedule_value" name="schedule_value" class="form-control" required><?php
                        foreach ($data as $key => $val) {
                            $selected = $schedule_val == $val['key'] ? 'selected' : '';
                            ?><option <?= $selected ?> value="<?= $val['key'] ?>"><?= $val['value'] ?></option><?php
                        } ?>
                        </select>
                    </div>
                </div>
            </div><?php
        }

        if ($schedule_type_id == 4 || $schedule_type_id == 6) {
            for ($i = 1; $i <= 31; $i++) {
                $days[] = ['key' => $i, 'value' => $i];
            } ?>
            <div class="col-md-<?= $comd ?>" id="divSchduleValue">
                <div class="form-group" id="schedule_value_parrent">
                    <lable class="form-label">Select Day</lable><span class="text-danger">*</span>
                    <div class="form-icon-user">
                        <select id="day" name="day" class="form-control" required><?php
                        foreach ($days as $key => $val) {
                            $selected = $h_day == $val['key'] ? 'selected' : '';
                            ?><option <?= $selected ?> value="<?= $val['key'] ?>"><?= $val['value'] ?></option><?php
                        } ?>
                    </select>
                </div>
            </div>
            </div><?php
        }
        if ($schedule_type_id == 3 || $schedule_type_id == 4 || $schedule_type_id == 5 || $schedule_type_id == 6 || $schedule_type_id == 7) { ?>
            <div class="col-md-<?= $comd ?>">
                <div class="form-group">
                    <label class="form-label">Select Time</label><span class="text-danger">*</span>
                    <div class="form-icon-user">
                        <input type="text" name="hours" id="hours" class="form-control timepicker" required value="<?= $h_hours ?>">
                    </div>
                </div>
            </div> <?php
        }
    }

    public function allCronDepartmentAjaxList(Request $request)
    {
        $search = $request->input('search');
        $arrCron = $this->_CronJob->allCronDepartmentAjaxList($search);
        $arr = [];
        foreach ($arrCron['data'] as $key => $val) {
            $arr['data'][$key]['id'] = $val->department;
            $arr['data'][$key]['text'] = $val->department;
        }
        $arr['data_cnt'] = $arrCron['data_cnt'];
        echo json_encode($arr);
    }

    public function quickRunCron(Request $request)
    {
        $id = $request->input('id');
        $data = $this->_CronJob->getEditDetails($id);
        if (isset($data)) {
            try {
                $response = Http::timeout(300)->get($data->url);
                $data = [];
                $data['response'] = 'Status Code: '.$response->status();
                if ($response->status() == 200) {
                    $data['last_run_datetime'] = date('Y-m-d H:i:s');
                }
                DB::table('cron_job')->where('id', $id)->update($data);
            } catch (\Throwable $e) {
                Log::error("Cron job failed: {$data->url}", [
                    'error' => $e->getMessage(),
                    'trace' => $e->getTraceAsString(),
                ]);

                DB::table('cron_job')->where('id', $id)->update([
                    'response' => 'Error: '.$e->getMessage(),
                ]);
            }
        }
    }

    public function followUpReturnApplication()
    {
        $diffDay= Setting::where('name','follow_up_returned_app_every')->pluck('value')->first();
        $businesses = Business::where('app_status_id', 2)
            ->where('is_active', 1)
            ->whereNull('payment_id')
            ->where('status', 'RETURNED')
            ->where(function ($q) {
                $q->whereDate('last_returned_email_at', '!=', Carbon::today())
                ->orWhereNull('last_returned_email_at');
            })
            ->select('id','pic_name','trustmark_id','pic_email', 'tax_year','date_returned','last_returned_email_at','total_returned_sent_email','business_name')
            ->get();

        $totalCnt=0;
        foreach ($businesses as $item) {
            if($diffDay > (int)$item->total_returned_sent_email){
                $referenceDate = $item->last_returned_email_at 
                    ? Carbon::parse($item->last_returned_email_at)
                    : Carbon::parse($item->date_returned);
                $days = abs(Carbon::now()->diffInRealDays($referenceDate));
                if ($days>=$diffDay) {
                    $totalCnt++;
                    $json_data = json_encode([
                        'pic_name'=>$item->pic_name,
                        'trustmark_id'=>$item->trustmark_id,
                    ]);
                    BusinessFollowups::insert([
                        'busn_id' => $item->id,
                        'is_type' => '2',
                        'year' => $item->tax_year,
                        'followup_date' => now(),
                        'followup_message' => $json_data,
                    ]);

                    $totalSentEmail = (int)$item->total_returned_sent_email + 1;
                    DB::table('businesses')->where('id', $item->id)->update([
                        'last_returned_email_at' => now(),
                        'total_returned_sent_email' => $totalSentEmail,
                    ]);

                    if($totalSentEmail==4){
                        $subject = $this->arrName[(int)$totalSentEmail].' Notification: E-Commerce Philippine Trustmark Application – Reference No. '.$item->trustmark_id;
                    }else{
                        $subject = $this->arrName[(int)$totalSentEmail].' Follow-Up on E-Commerce Philippine Trustmark Application – Reference No. '.$item->trustmark_id;
                    }
                    
                    $sendEmail = $this->email->sendMail('followUpReturn', [
                        'business' => $item,
                        'subject'=> $subject,
                        'totalCount'=>$totalSentEmail
                    ]);
                    if (isset($sendEmail['error'])) {
                        return 'Email failed: '.$sendEmail['error'];
                    }
                }
            }
        }
        return response()->json([
            'status' => 'success',
            'inserted' => $totalCnt,
        ]);
    }

    public function followUpUnpaid()
    {
        $diffDay= Setting::where('name','follow_up_payment_app_every')->pluck('value')->first();
        $businesses = Business::where('app_status_id', 1)
            ->where('is_active', 1)
            ->whereNull('payment_id')
            ->where('status', 'APPROVED')
            ->select('id','pic_name','trustmark_id','pic_email', 'tax_year','date_approved','last_approved_email_at','total_approved_sent_email','business_name')
            ->get();
        $totalCnt=0;
        foreach ($businesses as $item) {
            if($diffDay > (int)$item->total_approved_sent_email){
                $referenceDate = $item->last_approved_email_at 
                    ? Carbon::parse($item->last_approved_email_at)
                    : Carbon::parse($item->date_approved);
                $days = abs(Carbon::now()->diffInRealDays($referenceDate));
                if($days>=$diffDay){
                    $totalCnt++;
                    $json_data = json_encode([
                        'pic_name'=>$item->pic_name,
                        'trustmark_id'=>$item->trustmark_id,
                    ]);
                    $data= [
                        'busn_id' => $item->id,
                        'is_type' => '1',
                        'year' => $item->tax_year,
                        'followup_date' => now(),
                        'followup_message' => $json_data,
                    ];
                    BusinessFollowups::insert($data);

                    $totalSentEmail = (int)$item->total_approved_sent_email + 1;
                    DB::table('businesses')->where('id', $item->id)->update([
                        'last_approved_email_at' => now(),
                        'total_approved_sent_email' => $totalSentEmail,
                    ]);

                    if($totalSentEmail==4){
                        $subject = $this->arrName[(int)$totalSentEmail].' Notification: E-Commerce Philippine Trustmark Application – Reference No. '.$item->trustmark_id;
                    }else{
                        $subject = $this->arrName[(int)$totalSentEmail].' Follow-Up of Payment on your Trustmark Application – Reference No. '.$item->trustmark_id;
                    }
                    
                    $sendEmail = $this->email->sendMail('followUpUnpaid', [
                        'business' => $item,
                        'subject'=> $subject,
                        'totalCount'=>$totalSentEmail
                    ]);
                    if (isset($sendEmail['error'])) {
                        return 'Email failed: '.$sendEmail['error'];
                    }
                }
            }
        }
        return response()->json([
            'status' => 'success',
            'inserted' => $totalCnt,
        ]);
    }

    public function archiveFollowUpReturn()
    {
         // get value from settings
        $setting = Setting::where('name', 'archive_returned_app_after_nth_followups')->first();
        $countFollowups = $setting ? (int) $setting->value : 0;

        $businesses = Business::where('app_status_id', 2)
            ->where('is_active', 1)
            ->whereNull('payment_id')
            ->where('status', 'RETURNED')
            ->select('id', 'tax_year','total_returned_sent_email','pic_name','trustmark_id','last_returned_email_at')
            ->get();

        foreach ($businesses as $item) {
            if($item->total_returned_sent_email >= $countFollowups){
                $days = Carbon::parse($item->last_returned_email_at)->diffInDays(now());
                if((int)$days>=2){
                    DB::table('businesses')
                    ->where('id', $item->id)
                    ->update(['is_active' => 3,'date_archived' => now()]);

                    $json_data = json_encode([
                        'pic_name'=>$item->pic_name,
                        'trustmark_id'=>$item->trustmark_id,
                    ]);
                    $data= [
                        'busn_id' => $item->id,
                        'is_type' => '3',
                        'year' => $item->tax_year,
                        'followup_date' => now(),
                        'followup_message' => $json_data,
                    ];
                    BusinessFollowups::insert($data);
                }
            }
        }
        return response()->json(['status' => 'success', 'message' => 'Follow-up return archived successfully.']);
    }

    public function archiveFollowUpUnpaid()
    { 
        // get value from settings
        $setting = Setting::where('name', 'archive_unpaid_app_after_nth_followups')->first();
        $countFollowups = $setting ? (int) $setting->value : 0;

        $businesses = Business::where('app_status_id', 1)
            ->where('is_active', 1)
            ->whereNull('payment_id')
            ->where('status', 'APPROVED')
            ->select('id', 'tax_year','total_approved_sent_email','pic_name','trustmark_id','last_approved_email_at')
            ->get();

        foreach ($businesses as $item) {
            if($item->total_approved_sent_email >= $countFollowups){
                $days = Carbon::parse($item->last_approved_email_at)->diffInDays(now());
                if((int)$days>=2){
                    $updateBusiness = DB::table('businesses')
                    ->where('id', $item->id)
                    ->update(['is_active' => 3,'date_archived' => now()]);

                    $json_data = json_encode([
                        'pic_name'=>$item->pic_name,
                        'trustmark_id'=>$item->trustmark_id,
                    ]);
                    $data= [
                        'busn_id' => $item->id,
                        'is_type' => '3',
                        'year' => $item->tax_year,
                        'followup_date' => now(),
                        'followup_message' => $json_data,
                    ];
                    BusinessFollowups::insert($data);
                }
            }
        }
        return response()->json(['status' => 'success', 'message' => 'Follow-up unpaids archived successfully.']);
    }

    public function deleteDraft()
    {
        $diffDay= Setting::where('name','delete_draft_app_every')->pluck('value')->first();

        $businesses = Business::where('is_active', 1)->where('status', 'DRAFT')
            ->select('id','created_at','docs_business_reg','docs_bir_2303','docs_internal_redress','tax_year')->get();
        $totalCnt=0;
        foreach ($businesses as $item) {
            $referenceDate = Carbon::parse($item->created_at);
            $days = abs(Carbon::now()->diffInRealDays($referenceDate));
            if($days>=$diffDay){
                $totalCnt++;
                Business::where('id', $item->id)->delete();
                if (!empty($item->docs_business_reg) && Storage::disk('public')->exists($item->docs_business_reg)) {
                    Storage::disk('public')->delete($item->docs_business_reg);
                }

                if (!empty($item->docs_bir_2303) && Storage::disk('public')->exists($item->docs_bir_2303)) {
                    Storage::disk('public')->delete($item->docs_bir_2303);
                }

                if (!empty($item->docs_internal_redress) && Storage::disk('public')->exists($item->docs_internal_redress)) {
                    Storage::disk('public')->delete($item->docs_internal_redress);
                }

                $arrAttachment = DB::table('business_documents')->where('busn_id', $item->id)->where('year', $item->tax_year)->select('id','attachment')
                    ->get();
                foreach ($arrAttachment as $val) {
                    if (Storage::disk('public')->exists($val->attachment)) {
                        Storage::disk('public')->delete($val->attachment);
                    }
                    DB::table('business_documents')->where('id', $val->id)->delete();
                }
            }
        }
        return response()->json([
            'status' => 'success',
            'message' => 'Draft applications deleted successfully.',
            'deleted_count' => $totalCnt,
        ]);
    }
}
