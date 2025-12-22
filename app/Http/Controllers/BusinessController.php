<?php

namespace App\Http\Controllers;

use App\Jobs\ApprovedMailJob;
use App\Jobs\ReceivedMailJob;
use App\Models\ApplicationFees;
use App\Models\Barangay;
use App\Models\Business;
use App\Models\BusinessFees;
use App\Models\Category;
use App\Models\Email;
use App\Models\Payment;
use App\Models\Region;
use App\Models\RequirementReps;
use App\Models\TypeCorporation;
use Carbon\Carbon;
use Illuminate\Contracts\Encryption\DecryptException;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;
use Spatie\Browsershot\Browsershot;
use TCPDF;
use TCPDF_FONTS;
use Hashids\Hashids;
class BusinessController extends Controller
{
    protected $business;

    protected $email;

    public function __construct(Business $business, Email $email)
    {
        $this->business = $business;
        $this->email = $email;
    }

    public function search(Request $request)
    {
        $validated = $request->validate([
            'trustmark_id' => 'required|string',
        ]);

        $business = Business::search('')                       // empty query
            ->where('trustmark_id', $validated['trustmark_id']) // exact equality
            ->take(1)                                          // safety – we only need one
            ->get()
            ->first();                                         // returns a model or null

        return $business
            ? view('index', compact('business'))
            : back()->with('error', 'No data found with that reference number.');
    }

    public function indexold(Request $request)
    {
        $startdate = $request->input('fromdate');
        $enddate = $request->input('todate');
        $businesses = DB::table('businesses as a')
            ->leftJoin('users as b', 'a.user_id', '=', 'b.id')
            ->select([
                DB::raw("NULLIF(a.id,'') as id"), // fixed here
                DB::raw("NULLIF(a.trustmark_id,'') as trustmark_id"),
                DB::raw("NULLIF(a.business_name,'') as business_name"),
                DB::raw("NULLIF(a.reg_num,'') as reg_num"),
                DB::raw("NULLIF(a.tin,'') as tin"),
                DB::raw("(CASE a.corporation_type
                        WHEN 1 THEN 'Sole Proprietorship'
                        WHEN 2 THEN 'Corporation/Partnership'
                        WHEN 4 THEN 'Cooperative'
                    END) as business_type"),
                DB::raw('b.name as representative'),
                DB::raw("DATE_FORMAT(a.submit_date, '%m/%d/%Y') as date_submitted"),
                DB::raw('DATEDIFF(CURRENT_DATE(), a.submit_date) as no_of_days'),
                DB::raw("NULLIF(a.admin_remarks,'') as remarks"),
                DB::raw("NULLIF(a.status,'') as status"),
                DB::raw("NULLIF(a.payment_id,'') as payment_id"),
                DB::raw("NULLIF(a.corporation_type,'') as corporation_type"),
            ])
            ->where('a.is_active', 1);
        if (Auth::check() && Auth::user()->role != 1) {
            $businesses->where('a.status', 'UNDER EVALUATION')
                ->whereRaw('IFNULL(a.evaluator_id,0)=0');
        }
        $businesses->orderByDesc('a.submit_date');
        $types = TypeCorporation::orderBy('name')->where('is_active', 1)->get();

        // Business Type filter (assuming you store type as a string in corporationType relation)
        if ($request->filled('type')) {
            $businesses = $businesses->where('corporation_type', $request->type);
        }

        // Details
        if ($request->filled('details')) {
            $search = $request->details;

            $businesses = $businesses->where(function ($query) use ($search) {
                $query->where('trustmark_id', 'like', '%'.$search.'%')
                    ->orWhere('business_name', 'like', '%'.$search.'%')
                    ->orWhere('reg_num', 'like', '%'.$search.'%')
                    ->orWhere('tin', 'like', '%'.$search.'%')
                    ->orWhere('b.name', 'like', '%'.$search.'%')
                    ->orWhere('admin_remarks', 'like', '%'.$search.'%');
            });
        }
        if (! empty($startdate)) {
            $sdate = explode('-', $startdate);
            $startdate = date('Y-m-d', strtotime("{$sdate[2]}-{$sdate[1]}-{$sdate[0]}"));
            $businesses->whereDate('submit_date', '>=', trim($startdate));
        }

        if (! empty($enddate)) {
            $edate = explode('-', $enddate);
            $enddate = date('Y-m-d', strtotime("{$edate[2]}-{$edate[1]}-{$edate[0]}"));
            $businesses->whereDate('submit_date', '<=', trim($enddate));
        }
        // // Payment filter
        // if ($request->filled('payment')) {
        //     if ($request->payment == 'Paid') {
        //         $businesses = $businesses->whereNotNull('payment_id');
        //     } elseif ($request->payment == 'Unpaid') {
        //         $businesses = $businesses->whereNull('payment_id');
        //     }
        // }

        // // Status filter
        // if ($request->filled('status')) {
        //     $businesses = $businesses->where('status', $request->status);
        // }

        if (Auth::check() && Auth::user()->role == 1) {
            $businesses = $businesses->where('user_id', Auth::id());
        }

        // $businesses = $businesses->whereNotNull('corporation_type')
        //     ->orderBy('id', 'DESC')
        //     ->get();

        $businesses = $businesses->orderBy('id', 'DESC')
            ->get();

        return view('business.index', compact('businesses', 'types'));
    }

    public function index(Request $request)
    {
        $businesses = [];
        $types = TypeCorporation::orderBy('name')->where('is_active', 1)->get();
        $Eveluator = DB::table('user_admins AS a')
            ->join('users AS b', 'b.id', '=', 'a.user_id')
            ->select('a.user_id', 'b.name')
            ->pluck('b.name', 'a.user_id');

        return view('business.index', compact('businesses', 'types', 'Eveluator'));
    }

    public function getList(Request $request)
    {

        $params = $_REQUEST;
        $q = $request->input('q');
        $fromdate = $request->input('fromdate');
        $todate = $request->input('todate');

        if (! isset($params['start']) || ! isset($params['length'])) {
            $params['start'] = '0';
            $params['length'] = '10';
        }

        $columns = [
            1 => 'trustmark_id',
            2 => 'business_name',
            3 => 'reg_num',
            4 => 'business_type',
            5 => 'tin',
            6 => 'representative',
            7 => 'date_submitted',
        ];

        $sql = DB::table('businesses as a')
            ->leftJoin('users as b', 'a.user_id', '=', 'b.id')
            ->select([
                DB::raw("NULLIF(a.id,'') as id"), // fixed here
                DB::raw("NULLIF(a.trustmark_id,'') as trustmark_id"),
                DB::raw("NULLIF(a.business_name,'') as business_name"),
                DB::raw("NULLIF(a.reg_num,'') as reg_num"),
                DB::raw("NULLIF(a.tin,'') as tin"),
                DB::raw("NULLIF(a.on_hold,'') as on_hold"),
                DB::raw("(CASE a.corporation_type
                            WHEN 1 THEN 'Sole Proprietorship'
                            WHEN 2 THEN 'Corporation/Partnership'
                            WHEN 4 THEN 'Cooperative'
                        END) as business_type"),
                DB::raw('b.name as representative'),
                DB::raw("DATE_FORMAT(a.submit_date, '%m/%d/%Y') as date_submitted"),
                DB::raw('DATEDIFF(CURRENT_DATE(), a.submit_date) as no_of_days'),
                DB::raw("NULLIF(a.admin_remarks,'') as remarks"),
                DB::raw("NULLIF(a.status,'') as status"),
                DB::raw("NULLIF(a.payment_id,'') as payment_id"),
                DB::raw("NULLIF(a.corporation_type,'') as corporation_type"),
                DB::raw("NULLIF(a.is_active,'') as is_active"),
            ])
            ->where('a.is_active', 1);
        if (Auth::check() && Auth::user()->role != 1) {
            $sql->where('a.status', 'UNDER EVALUATION')
                ->whereRaw('IFNULL(a.evaluator_id,0)=0');
        }
        if (! empty($q)) {
            $sql->where(function ($query) use ($q) {
                $query->where(DB::raw('LOWER(trustmark_id)'), 'like', '%'.strtolower($q).'%')
                    ->orWhere(DB::raw('LOWER(business_name)'), 'like', '%'.strtolower($q).'%')
                    ->orWhere(DB::raw('LOWER(tin)'), 'like', '%'.strtolower($q).'%')
                    ->orWhere(DB::raw('LOWER(b.name)'), 'like', '%'.strtolower($q).'%')
                    ->orWhere(DB::raw('LOWER(admin_remarks)'), 'like', '%'.strtolower($q).'%')
                    ->orWhere(DB::raw('LOWER(reg_num)'), 'like', '%'.strtolower($q).'%');
            });
        }
        if (! empty($fromdate) && isset($fromdate)) {
            $sql->whereDate('submit_date', '>=', trim($fromdate));
        }
        if (! empty($todate) && isset($todate)) {
            $sql->whereDate('submit_date', '<=', trim($todate));
        }

        if (Auth::check() && Auth::user()->role == 1) {
            $sql->where('user_id', Auth::id());
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

        // $data=$this->business->getList($request);
        // echo "<pre>"; print_r($data); exit;
        $arr = [];
        $i = '0';
        $sr_no = (int) $request->input('start') - 1;
        $sr_no = $sr_no > 0 ? $sr_no + 1 : 0;
        $role = Auth::user()->role;

        foreach ($data as $row) {
            $status = $row->status;
            $sr_no = $sr_no + 1;
            $hashids = new Hashids(env('APP_KEY'), 10);
            $ids = $hashids->encode($row->id);
            if ($role == 1) {
                if (in_array($status, ['UNDER EVALUATION', 'APPROVED', 'ON-HOLD'])) {
                    $actions = '<a href="'.route('business.view', $ids).'" 
                                    data-bs-toggle="tooltip" data-bs-placement="bottom" 
                                    title="View" data-busn-id="'.$row->id.'" 
                                    data-trustmark-id="'.$row->trustmark_id.'" 
                                    data-status="'.$row->status.'"   class="custom-eye-btn"><i class="custom-eye-icon fa fa-eye"></i></a>';
                } elseif ($status == 'RETURNED') {
                    $actions = '<a href="'.route('business.edit', $ids).'" 
                                    data-bs-toggle="tooltip" data-bs-placement="bottom" 
                                    title="Edit"><i class="custom-pencil-icon fa fa-pencil"></i></a>';
                } elseif ($status == 'DISAPPROVED') {
                    $actions = '<a href="'.route('business.disapproved_view', $ids).'" 
                                    data-bs-toggle="tooltip" data-bs-placement="bottom" 
                                    title="View" data-busn-id="'.$row->id.'" 
                                    data-trustmark-id="'.$row->trustmark_id.'" 
                                    data-status="'.$row->status.'"   class="custom-eye-btn"><i class="custom-eye-icon fa fa-eye"></i></a>';
                } else {
                    $actions = '<a href="'.route('business.create', $ids).'" 
                                    data-bs-toggle="tooltip" data-bs-placement="bottom" 
                                    title="Continue"><i class="custom-pencil-icon fa fa-arrow-right"></i></a><a href="javascript:void(0);" 
                                    class="btn-delete-business" 
                                    data-id="'.$row->id.'" 
                                    data-url="'.route('business.destroydraft', $row->id).'" 
                                    data-bs-toggle="tooltip" 
                                    data-bs-placement="bottom" 
                                    title="Delete">
                                    <i class="custom-trash-icon fa fa-trash" style="color: red;font-size: 15px;"></i>
                                </a>';
                }
            } else {
                if ($status == 'UNDER EVALUATION') {
                    $actions = '<a href="'.route('business.view', $ids).'" 
                                    data-bs-toggle="tooltip" data-bs-placement="bottom" 
                                    title="Update"><i class="custom-pencil-icon fa fa-pencil"></i></a>';
                }
                if ($status == 'ON-HOLD') {
                    $actions = '<a href="'.route('business.view', $ids).'" 
                                    data-bs-toggle="tooltip" data-bs-placement="bottom" 
                                    title="Update"><i class="custom-pencil-icon fa fa-pencil"></i></a>';
                } elseif ($status == 'DISAPPROVED') {
                    $actions = '<a href="'.route('business.disapproved_view', $ids).'" 
                                    data-bs-toggle="tooltip" data-bs-placement="bottom" 
                                    title="View"><i class="custom-eye-icon fa fa-eye"></i></a>';
                } else {
                    $actions = '<a href="'.route('business.view', $ids).'" 
                                    data-bs-toggle="tooltip" data-bs-placement="bottom" 
                                    title="View"><i class="custom-eye-icon fa fa-eye"></i></a>';
                }
                $adminUser = DB::table('user_admins')
                    ->where('user_id', Auth::id())
                    ->first(['is_admin']);
                if ($adminUser && $adminUser->is_admin) {
                    if ($row->is_active == 1 && $row->payment_id == 0) {
                        $actions .= '<a href="javascript:void(0);" 
                                        class="btn-delete" 
                                        data-id="'.$row->id.'" 
                                        data-url="'.route('business.mytasklistdestroy', $row->id).'" 
                                        data-bs-toggle="tooltip" 
                                        data-bs-placement="bottom" 
                                        title="Delete">
                                        <i class="custom-trash-icon fa fa-trash" style="color: red;font-size: 15px;"></i>
                                    </a>';
                    }
                }
            }
            $arr[$i]['checkbox'] = '<input type="checkbox" class="row-check" value="'.$row->id.'">';
            $arr[$i]['srno'] = $sr_no;
            $arr[$i]['trustmark_id'] = $row->trustmark_id ?? 'N/A';
            $arr[$i]['business_name'] = $row->business_name;
            $arr[$i]['reg_num'] = $row->reg_num ?? 'N/A';
            $arr[$i]['tin'] = $row->tin ?? 'N/A';
            $arr[$i]['business_type'] = $row->business_type ?? 'N/A';
            $arr[$i]['representative'] = $row->representative ?? 'N/A';
            $arr[$i]['date_submitted'] = $row->date_submitted ?? 'N/A';
            $arr[$i]['no_of_days'] = $row->no_of_days ?? 'N/A';
            $arr[$i]['remarks'] = $row->remarks;

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

            $arr[$i]['status'] = '<span class="'.$badgeClass.'">'.$displayStatus.'</span>';
            $arr[$i]['action'] = $actions;
            $i++;
        }

        $totalRecords = $data_cnt;
        $json_data = [
            'recordsTotal' => intval($totalRecords),
            'recordsFiltered' => intval($totalRecords),
            'data' => $arr,   // total data array
        ];
        echo json_encode($json_data);
    }
    public function submit_userlogs(Request $request)
    {
        $businessId   = $request->busn_id;
        $trustmarkId  = $request->trustmark_id;
        $status       = $request->status;
        DB::table('user_logs')->updateOrInsert(
            [
                'busn_id'   => $businessId,
                'action_id' => $request->action_id, 
            ],
            [
            'action_name'      => $request->action_name,
            'message'          => Auth::user()->name . ' view the application with Sec-No. ' 
                                  . $trustmarkId . ' dated ' . now()->format('Y-m-d H:i:s') . '.',
            'public_ip_address'=> $request->ip(),
            'status'           => $status,
            'remarks'          => '',
            'longitude'        => $request->input('longitude'),
            'latitude'         => $request->input('latitude'),
            'created_by'       => Auth::id(),
            'created_by_name'  => Auth::user()->name,
            'created_date'     => now(),
        ]);

        return response()->json(['success' => true]);
    }
    public function submit_downloadQR(Request $request)
    {
        $businessId   = $request->busn_id;
        $trustmarkId  = $request->trustmark_id;
        $status       = $request->status;
        DB::table('user_logs')->updateOrInsert(
            [
                'busn_id'   => $businessId,
                'action_id' => $request->action_id, 
            ],
            [
            'action_name'      => $request->action_name,
            'message'          => Auth::user()->name . ' download QR Code with Sec-No. ' 
                                  . $trustmarkId . ' dated ' . now()->format('Y-m-d H:i:s') . '.',
            'public_ip_address'=> $request->ip(),
            'status'           => $status,
            'remarks'          => '',
            'longitude'        => $request->input('longitude'),
            'latitude'         => $request->input('latitude'),
            'created_by'       => Auth::id(),
            'created_by_name'  => Auth::user()->name,
            'created_date'     => now(),
        ]);

        return response()->json(['success' => true]);
    }
    public function submit_downloadCert(Request $request)
    {
        $businessId   = $request->busn_id;
        $trustmarkId  = $request->trustmark_id;
        $status       = $request->status;
        DB::table('user_logs')->updateOrInsert(
            [
                'busn_id'   => $businessId,
                'action_id' => $request->action_id, 
            ],
            [
            'action_name'      => $request->action_name,
            'message'          => Auth::user()->name . ' downloaded PDF Certificate with Sec-No.  ' 
                                  . $trustmarkId . ' dated ' . now()->format('Y-m-d H:i:s') . '.',
            'public_ip_address'=> $request->ip(),
            'status'           => $status,
            'remarks'          => '',
            'longitude'        => $request->input('longitude'),
            'latitude'         => $request->input('latitude'),
            'created_by'       => Auth::id(),
            'created_by_name'  => Auth::user()->name,
            'created_date'     => now(),
        ]);

        return response()->json(['success' => true]);
    }

    public function submit_regenerateCert(Request $request)
    {
        $businessId   = $request->busn_id;
        $trustmarkId  = $request->trustmark_id;
        $status       = $request->status;
        DB::table('user_logs')->updateOrInsert(
            [
                'busn_id'   => $businessId,
                'action_id' => $request->action_id, 
            ],
            [
            'action_name'      => $request->action_name,
            'message'          => Auth::user()->name . ' re-generated PDF Certificate with Sec-No.   ' 
                                  . $trustmarkId . ' dated ' . now()->format('Y-m-d H:i:s') . '.',
            'public_ip_address'=> $request->ip(),
            'status'           => $status,
            'remarks'          => '',
            'longitude'        => $request->input('longitude'),
            'latitude'         => $request->input('latitude'),
            'created_by'       => Auth::id(),
            'created_by_name'  => Auth::user()->name,
            'created_date'     => now(),
        ]);

        return response()->json(['success' => true]);
    }
    public function mytasklist(Request $request)
    {
        // $businesses = Business::where('is_active', 1);

        // if ($request->filled('type')) {
        //     $businesses = $businesses->whereHas('corporationType', function ($q) use ($request) {
        //         $q->where('name', $request->type);
        //     });
        // }
        // if ($request->filled('details')) {
        //     $search = $request->details;

        //     $businesses = $businesses->where(function ($query) use ($search) {
        //         $query->where('trustmark_id', 'like', '%'.$search.'%')
        //             ->orWhere('business_name', 'like', '%'.$search.'%')
        //             ->orWhere('reg_num', 'like', '%'.$search.'%')
        //             ->orWhere('tin', 'like', '%'.$search.'%')
        //             ->orWhere('pic_name', 'like', '%'.$search.'%')
        //             ->orWhere('admin_remarks', 'like', '%'.$search.'%');
        //     });
        // }
        // if ($request->filled('payment')) {
        //     if ($request->payment == 'Paid') {
        //         $businesses = $businesses->whereNotNull('payment_id');
        //     } elseif ($request->payment == 'Unpaid') {
        //         $businesses = $businesses->whereNull('payment_id');
        //     }
        // }
        // if ($request->filled('status')) {
        //     $businesses = $businesses->where('status', $request->status);
        // }
        // if ($request->filled('evaluator_id')) {
        //     $businesses = $businesses->where('evaluator_id', $request->evaluator_id);
        // } else {
        //     //$businesses = $businesses->where('evaluator_id', Auth::id());
        // }

        // if (Auth::check() && Auth::user()->role == 1) {
        //     $businesses = $businesses->where('user_id', Auth::id());
        // }
        // // ->whereNotNull('corporation_type')
        // $businesses = $businesses->orderBy('id', 'DESC')
        //     ->get();
        //     dd($businesses);
        // echo "<pre>";print_r($businesses);  exit;
        $businesses = [];

        $Eveluator = DB::table('user_admins AS a')
            ->join('users AS b', 'b.id', '=', 'a.user_id')
            ->select('a.user_id', 'b.name')
            ->pluck('b.name', 'a.user_id');
        $types = TypeCorporation::orderBy('name')->where('is_active', 1)->get();
        $businessEveluator = Business::where('is_active', 1)
            ->where('evaluator_id', Auth::id())
            ->first();

        return view('business.mytasklist', compact('businesses', 'types', 'Eveluator', 'businessEveluator'));
    }

    public function getmytasklist(Request $request)
    {
        $params = $_REQUEST;
        $q = $request->input('q');

        if (! isset($params['start']) || ! isset($params['length'])) {
            $params['start'] = '0';
            $params['length'] = '10';
        }

        $columns = [
            1 => 'trustmark_id',
            2 => 'business_name',
            3 => 'reg_num',
            4 => 'business_type',
            5 => 'tin',
            6 => 'b.name',
            8 => 'admin_remarks',
        ];
        /*
        ->select([
            DB::raw("NULLIF(a.id,'') as id"), // fixed here
            DB::raw("NULLIF(a.trustmark_id,'') as trustmark_id"),
            DB::raw("NULLIF(a.business_name,'') as business_name"),
            DB::raw("NULLIF(a.reg_num,'') as reg_num"),
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
            DB::raw("NULLIF(a.admin_remarks,'') as remark"),
            DB::raw("NULLIF(a.payment_id,'') as payment_id"),
            DB::raw("NULLIF(a.corporation_type,'') as corporation_type"),
            DB::raw("NULLIF(a.is_active,'') as is_active"),
        ]) */
        $sql = DB::table('businesses as a')
            ->leftJoin('users as b', 'a.user_id', '=', 'b.id')
	    ->leftJoin('users as c', 'a.evaluator_id', '=', 'c.id')
            ->select([
                'a.id',
                'a.trustmark_id',
                'a.business_name',
                'a.reg_num',
                'a.tin',
                'a.on_hold',
                DB::raw("CASE a.corporation_type
                            WHEN 1 THEN 'Sole Proprietorship'
                            WHEN 2 THEN 'Corporation/Partnership'
                            WHEN 4 THEN 'Cooperative'
                         END AS business_type"),
                'b.name as representative',
		'c.name as Evaluator',
                DB::raw("DATE_FORMAT(a.submit_date, '%m/%d/%Y') as date_submitted"),
                DB::raw("DATE_FORMAT(a.date_returned, '%m/%d/%Y') as date_returned"),
                DB::raw("DATE_FORMAT(a.date_approved, '%m/%d/%Y') as date_approved"),
                DB::raw("DATE_FORMAT(a.date_issued, '%m/%d/%Y') as date_issued"),
                DB::raw("DATE_FORMAT(a.expired_date, '%m/%d/%Y') as expired_date"),
                DB::raw("DATE_FORMAT(a.created_at, '%m/%d/%Y') as date_generated"),
                'a.status',
                'a.admin_remarks as remark',
                'a.payment_id',
                'a.corporation_type',
                'a.is_active',
            ])->where('a.is_active', 1)->whereNotNull('a.evaluator_id');

        if (! empty($q)) {
            $sql->where(function ($query) use ($q) {
                $query->where(DB::raw('LOWER(trustmark_id)'), 'like', '%'.strtolower($q).'%')
                    ->orWhere(DB::raw('LOWER(business_name)'), 'like', '%'.strtolower($q).'%')
                    ->orWhere(DB::raw('LOWER(tin)'), 'like', '%'.strtolower($q).'%')
                    ->orWhere(DB::raw('LOWER(b.name)'), 'like', '%'.strtolower($q).'%')
                    ->orWhere(DB::raw('LOWER(admin_remarks)'), 'like', '%'.strtolower($q).'%')
                    ->orWhere(DB::raw('LOWER(reg_num)'), 'like', '%'.strtolower($q).'%');
            });
        }
        if ($request->filled('type')) {
            $sql->where('corporation_type', $request->type);
        }
        if ($request->month && $request->year) {
            $sql->whereMonth('a.submit_date', $request->month)
                  ->whereYear('a.submit_date', $request->year);
        }
        if ($request->filled('payment')) {
            if ($request->payment == 'Paid') {
                $sql->whereNotNull('payment_id');
            } elseif ($request->payment == 'Unpaid') {
                $sql->whereNull('payment_id');
            }
        }
        if ($request->filled('status')) {

            if ($request->status === 'ON-HOLD') {
                $sql->where('on_hold', 1);
            } else {
                $sql->where('status', $request->status);
            }
        
        }
        if ($request->filled('evaluator_id')) {
            $sql->where('evaluator_id', $request->evaluator_id);
        } else {
            // $sql->where('evaluator_id', Auth::id());
        }

        if (Auth::check() && Auth::user()->role == 1) {
            $sql->where('user_id', Auth::id());
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

        // echo "<pre>"; print_r($data); exit;
        $arr = [];
        $i = '0';
        $sr_no = (int) $request->input('start') - 1;
        $sr_no = $sr_no > 0 ? $sr_no + 1 : 0;
        $role = Auth::user()->role;

        foreach ($data as $row) {
            $status = $row->status;
            $sr_no = $sr_no + 1;
            $id = encrypt($row->id);
            $hashids = new Hashids(env('APP_KEY'), 10);
            $ids = $hashids->encode($row->id);
            // $id = substr(encrypt($row->id), 0, 10);
            $actions = '';
            if ($role == 1) {
                if (in_array($status, ['UNDER EVALUATION', 'APPROVED', 'ON-HOLD'])) {
                    $actions = '<a href="'.route('business.view', $ids).'" 
                                    data-bs-toggle="tooltip" data-bs-placement="bottom" 
                                    title="View"><i class="custom-eye-icon fa fa-eye"></i></a>';
                } elseif ($status == 'RETURNED') {
                    $actions = '<a href="'.route('business.edit', $ids).'" 
                                    data-bs-toggle="tooltip" data-bs-placement="bottom" 
                                    title="Edit"><i class="custom-pencil-icon fa fa-pencil"></i></a>';
                } elseif ($status == 'DISAPPROVED') {
                    $actions = '<a href="'.route('business.disapproved_view', $ids).'" 
                                    data-bs-toggle="tooltip" data-bs-placement="bottom" 
                                    title="View"><i class="custom-eye-icon fa fa-eye"></i></a>';
                } else {
                    $actions = '<a href="'.route('business.create', $ids).'" 
                                    data-bs-toggle="tooltip" data-bs-placement="bottom" 
                                    title="Continue"><i class="custom-pencil-icon fa fa-arrow-right"></i></a>';
                }
            } else {
                if ($status == 'UNDER EVALUATION') {
                    $actions = '<a href="'.route('business.view', $ids).'" 
                                    data-bs-toggle="tooltip" data-bs-placement="bottom" 
                                    title="Update"><i class="custom-pencil-icon fa fa-pencil"></i></a>';

                }elseif ($status == 'DISAPPROVED') {
                    $actions = '<a href="'.route('business.disapproved_view', $ids).'" 
                                    data-bs-toggle="tooltip" data-bs-placement="bottom" 
                                    title="View"><i class="custom-eye-icon fa fa-eye"></i></a>';
                } else {
                    $actions = '<a href="'.route('business.view', $ids).'" 
                                    data-bs-toggle="tooltip" data-bs-placement="bottom" 
                                    title="View"><i class="custom-eye-icon fa fa-eye"></i></a>';
                }
                $adminUser = DB::table('user_admins')
                    ->where('user_id', Auth::id())
                    ->first(['is_admin']);

                if ($adminUser && $adminUser->is_admin) {
                    if ($row->is_active == 1 && $row->payment_id == 0) {
                        $actions .= '<a href="javascript:void(0);" 
                                        class="btn-delete" 
                                        data-id="'.$row->id.'" 
                                        data-url="'.route('business.mytasklistdestroy', $row->id).'" 
                                        data-bs-toggle="tooltip" 
                                        data-bs-placement="bottom" 
                                        title="Delete">
                                        <i class="custom-trash-icon fa fa-trash" style="color: red;font-size: 15px;"></i>
                                    </a>';
                    }
                }

            }

            $arr[$i]['srno'] = $sr_no;
            $arr[$i]['id'] = $row->id;
            $arr[$i]['trustmark_id'] = $row->trustmark_id ?? '';
            $arr[$i]['business_name'] = $row->business_name;
            $arr[$i]['reg_num'] = $row->reg_num ?? '';
            $arr[$i]['tin'] = $row->tin ?? '';
            $arr[$i]['business_type'] = $row->business_type ?? '';
            $arr[$i]['representative'] = $row->representative ?? '';
            $arr[$i]['Evaluator'] = $row->Evaluator ?? '';
            $arr[$i]['remark'] = $row->remark ?? ' ';
            $arr[$i]['date_submitted'] = $row->date_submitted ?? ' ';
            $arr[$i]['date_returned'] = $row->date_returned ?? ' ';
            $arr[$i]['date_approved'] = $row->date_approved ?? ' ';
            $arr[$i]['date_issued'] = $row->date_issued ?? ' ';
            $arr[$i]['expired_date'] = $row->expired_date ?? ' ';
            $paymentStatus = $row->payment_id === null ? 'Unpaid' : 'Paid';
            $paymentBadgeClass = match ($paymentStatus) {
                'Paid' => 'badge-bg-approve', // green-like color
                'Unpaid' => 'badge-bg-returned', // red-like color
                'default' => 'badge-bg-draft', // fallback color
            };

            $arr[$i]['paymnetsttaus'] = '<span
                                                class="badge '.$paymentBadgeClass.' px-2 py-1 small text-center d-inline-block"
                                                style="min-width: 80px;">
                                                '.$paymentStatus.'
                                            </span>';

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
            $arr[$i]['action'] = $actions;
            $i++;
        }

        $totalRecords = $data_cnt;
        $json_data = [
            'recordsTotal' => intval($totalRecords),
            'recordsFiltered' => intval($totalRecords),
            'data' => $arr,   // total data array
        ];
        echo json_encode($json_data);

    }

    public function auto_store()
    {
        $user = Auth::user();
        $type_corporation = TypeCorporation::where('is_active', 1)->get();
        $business = new Business; // empty model
        $categories = Category::where('is_active', 1)->get();
        $requirements = RequirementReps::where('status', 'Active')->get();
        $settings = DB::table('settings')
            ->where('name', 'delete_draft_app_every')
            ->first();
        $regions = Region::where('is_active', 1)->pluck('reg_region', 'id');
        $AdditionalDocuments = array();
        /*$AdditionalDocuments = DB::table('business_documents')
            // ->where('busn_id', $business_id)
            ->where('year', now()->year)
            ->get(); */
        $business_category = DB::table('business_category')
        ->get();
        $settingsIrm = DB::table('settings')
        ->where('name', 'enabled_irm')
        ->first();
        $business_irm = DB::table('business_irm')
        ->where('busn_id', $business->id)
        ->first();
        $suffixs = DB::connection('project1')->table('name_suffixes')->get();
        return view('business.create', compact('type_corporation','business_irm','suffixs','settingsIrm','business_category','business', 'categories', 'requirements', 'settings', 'regions', 'AdditionalDocuments', 'user'));
    }

    public function create($business_id)
    {
        try {
            $hashids = new Hashids(env('APP_KEY'), 10);
            $business_id = $hashids->decode($business_id)[0];
            // $business_id = Crypt::decrypt($business_id);
        } catch (DecryptException $e) {
            abort(403, 'Invalid business ID.');
        }

        $business = Business::findOrFail($business_id);
        $type_corporation = TypeCorporation::where('is_active', 1)->get();
        $selectedTypeId = $business->corporation_type;
        $regions = Region::where('is_active', 1)->pluck('reg_region', 'id');
        // $categories = Category::where('is_active', 1)->pluck('name', 'id');
        $business_category = DB::table('application_fee_category')
        // ->orderByDesc('is_default') 
        ->orderBy('id')
        ->get();
        // $requirements = RequirementReps::where('status', 'Active')->pluck('description', 'id');
        $categories = Category::where('is_active', 1)->get();
        $settings = DB::table('settings')
            ->where('name', 'delete_draft_app_every')
            ->first();
        $requirements = RequirementReps::where('status', 'Active')->get();
        $AdditionalDocuments = DB::table('business_documents')
            ->where('busn_id', $business_id)
            ->where('year', now()->year)
            ->get();
        $user = Auth::user();
        $businessCatName  = DB::table('application_fee_category')->where('busn_category_id',$business->busn_category_id)->first();
        $suffixs = DB::connection('project1')->table('name_suffixes')->get();
        $settingsIrm = DB::table('settings')
        ->where('name', 'enabled_irm')
        ->first();
        $business_irm = DB::table('business_irm')
        ->where('busn_id', $business->id)
        ->first();
        return view('business.create', compact('business_id','suffixs','business_irm','settingsIrm','businessCatName' ,'settings', 'type_corporation', 'business', 'selectedTypeId', 'regions', 'categories', 'requirements', 'AdditionalDocuments', 'user','business_category'));
    }

    public function save_corporation(Request $request)
    {
        $requirements = RequirementReps::where('status', 'Active')->get();

        // validation rules as before...
        $rules = [
            'type_id' => 'required',
            'reg_num' => 'required|string',
            'tin_num' => ['required', 'regex:/^\d{3}-\d{3}-\d{3}-\d{5}$/'],
            'business_name' => 'required|string|max:130',
            'franchise' => 'nullable|string',
            'category' => 'nullable',
            'other_category' => 'nullable|string|max:150',
            'url_platform_json' => [
                'required',
                function ($attribute, $value, $fail) {
                    $urls = json_decode($value, true);
                    if (! is_array($urls) || count($urls) === 0 || empty($urls[0])) {
                        $fail('At least one business URL is required.');
                    }
                    foreach ($urls as $url) {
                        if (! empty($url) && ! filter_var($url, FILTER_VALIDATE_URL)) {
                            $fail("Invalid URL detected: $url");
                        }
                    }
                },
            ],
            'first_name' => 'required|string',
            'middle_name' => 'nullable|string',
            'last_name' => 'required|string',
            'suffix' => 'nullable|string',
            'ctc_no' => 'required|numeric',
            'email' => 'required|email',
            'issued_id' => 'required',
            'req_upload' => 'nullable|file|mimes:jpg,jpeg,png,pdf|max:10240',
            'expired_date' => ['nullable', 'date'],
        ];

        // extra rule if requirement requires expiration
        if ($request->issued_id) {
            $requirement = $requirements->firstWhere('id', $request->issued_id);
            if ($requirement && trim($requirement->with_expiration) === '1') {
                $rules['expired_date'] = ['required', 'date', 'after_or_equal:'.now()->format('Y-m-d')];
            }
        }

        $validator = Validator::make($request->all(), $rules);

        if ($validator->fails()) {
            return redirect()->back()
                ->withErrors($validator, 'errorBusiness')
                ->withInput();
        }

        $validated = $validator->validated();
        $urls = json_decode($request->input('url_platform_json', '[]'), true);
        
        // ✅ if business_id exists → update, else → create
        if ($request->filled('business_id')) {
            $business = Business::findOrFail($request->business_id);
        } else {
            $business = new Business;
            $business->user_id = Auth::id();
            $business->created_by = Auth::id();
            $business->status = 'DRAFT';
            $business->tax_year = date('Y');
            $business->app_code = 1;
        }
        $fullName = trim(
            $request->input('first_name').' '.
                ($request->input('middle_name') ? $request->input('middle_name').' ' : '').
                $request->input('last_name').
                ($request->input('suffix') ? ', '.$request->input('suffix') : '')
        );
        // update/save fields
        $business->pic_name = $fullName;
        $business->first_name = $validated['first_name'];
        $business->middle_name = $validated['middle_name'];
        $business->last_name = $validated['last_name'];
        $business->suffix = $validated['suffix'];
        $business->corporation_type = $validated['type_id'];
        $business->reg_num = $validated['reg_num'];
        $business->tin = $validated['tin_num'];
        $business->business_name = $validated['business_name'];
        $business->franchise = $validated['franchise'];
        $business->category_id = $validated['category'];
        $business->category_other_description = $validated['other_category'];
        $business->url_platform = $urls;
        $business->pic_ctc_no = $validated['ctc_no'];
        $business->pic_email = $validated['email'];
        $business->requirement_id = $validated['issued_id'];
        $business->requirement_expired = $validated['expired_date'];

        if ($request->hasFile('req_upload')) {
            if (!empty($business->requirement_upload) && Storage::disk('public')->exists($business->requirement_upload)) {
                Storage::disk('public')->delete($business->requirement_upload);
            }
        
            $file = $request->file('req_upload');
            $originalName = $file->getClientOriginalName();
            $fileNameWithoutExt = pathinfo($originalName, PATHINFO_FILENAME);
            $extension = $file->getClientOriginalExtension();
            $year  = Carbon::now()->format('Y');
            $month = Carbon::now()->format('M');
        
            $uploadDir = "document-upload/requirement_reps/{$year}/{$month}";
        
            if (!Storage::disk('public')->exists($uploadDir)) {
                Storage::disk('public')->makeDirectory($uploadDir);
            }
        
            $fileName = time() . '_' . $fileNameWithoutExt . '.' . $extension;
        
            $req_upload_path = $file->storeAs($uploadDir, $fileName, 'public');
        
            $business->requirement_upload = $req_upload_path;
        }

        $business->save();
        foreach ($urls as $url) {
            $url = trim($url);
            if (empty($url)) continue;
            try {
                $parsedUrl = parse_url($url);
                $baseHost = strtolower($parsedUrl['host'] ?? '');
            } catch (\Exception $e) {
                $baseHost = '';
            }
    
            //  Check platform_url table
            $platform = DB::table('platform_url as a')
                ->select('a.platform_name', 'a.with_irm')
                ->where('a.is_active', 1)
                ->where(function ($q) use ($baseHost) {
                    $q->where('a.base_url', 'LIKE', "%{$baseHost}%");
                })
                ->first();
    
            $platformName = $platform->platform_name ?? '';
            $withIrm = isset($platform->with_irm) ? (int) $platform->with_irm : 0;
    
            //business_url Insert or update business_url record
            DB::table('business_url')->updateOrInsert(
                [
                    'busn_id' => $business->id,
                    'url' => $url,
                ],
                [
                    'tax_year' => date('Y'),
                    'platform_name' => $platformName,
                    'with_irm' => $withIrm,
                    'created_by' => Auth::id(),
                    'created_date' => now(),
                    'modified_by' => Auth::id(),
                    'modified_date' => now(),
                ]
            );
        }
        DB::table('user_logs')->updateOrInsert(
            [
                'busn_id'   => $business->id,
                'action_id' => 1, 
            ],
            [
            'action_name'      => 'created',
            'message'          => Auth::user()->name . ' created the draft application with details Reg-No. ' 
                                  . $business->reg_num . ', TIN: ' . $business->tin 
                                  . ', Business Name: ' . $business->business_name 
                                  . ' dated ' . now()->format('Y-m-d H:i:s') . '.',
            'public_ip_address'=> $request->ip(),
            'status'           => 'DRAFT',
            'remarks'          => '',
            'longitude'        => $request->input('longitude'), 
            'latitude'         => $request->input('latitude'),  
            'created_by'       => Auth::id(),
            'created_by_name'  => Auth::user()->name,
            'created_date'     => now(),
        ]);
        $hashids = new Hashids(env('APP_KEY'), 10);
        $ids = $hashids->encode($business->id);
        return redirect()->route('business.create', ['business_id' => $ids])
            ->with('go_to_details', true);
    }

    public function save_detail(Request $request)
    {
        $business = Business::find($request->input('business_id'));

        $validated = $request->validate([
            'region' => 'required',
            'province' => 'required',
            'municipality' => 'required',
            'barangay' => 'required',
            'address' => 'required|string',
        ]);

        $business->region_id = $validated['region'];
        $business->province_id = $validated['province'];
        $business->municipality_id = $validated['municipality'];
        $business->barangay_id = $validated['barangay'];
        $business->complete_address = $validated['address'];

        $business->save();
        $hashids = new Hashids(env('APP_KEY'), 10);
        $busn_id = $hashids->encode($request->business_id);
        // $busn_id = encrypt($request->business_id);

        return redirect()->route('business.create', ['business_id' => $busn_id])
            ->with('go_to_documents', true)
            ->with('business_id', $request->business_id)
            ->with('business', $business);
    }

    public function save_document(Request $request)
    {
        $businessId = $request->input('business_id');
        $business = Business::find($businessId);

        if (! $business) {
            \Log::error('Business not found for ID: '.$businessId);

            return redirect()->back()->withErrors(['Business not found.']);
        }

        try {

            if (empty($business->docs_business_reg)) {
                $rules['business_reg'] = 'required|file|mimes:jpg,jpeg,png,pdf|max:10240';
            } else {
                $rules['business_reg'] = 'nullable|file|mimes:jpg,jpeg,png,pdf|max:10240';
            }

            if (empty($business->docs_bir_2303)) {
                $rules['bir_2303'] = 'required|file|mimes:jpg,jpeg,png,pdf|max:10240';
            } else {
                $rules['bir_2303'] = 'nullable|file|mimes:jpg,jpeg,png,pdf|max:10240';
            }

            if (empty($business->docs_internal_redress)) {
                $rules['internal_redress'] = 'required|file|mimes:jpg,jpeg,png,pdf|max:10240';
            } else {
                $rules['internal_redress'] = 'nullable|file|mimes:jpg,jpeg,png,pdf|max:10240';
            }

            $validated = $request->validate($rules);

            $data = [];

            $now = now();
            $year  = $now->format('Y');
            $month = $now->format('M');

            /* ================= BUSINESS REG ================= */
            if ($request->hasFile('business_reg')) {

                $file = $request->file('business_reg');
                $originalName = $file->getClientOriginalName();

                $fileNameWithoutExt = pathinfo($originalName, PATHINFO_FILENAME);
                $extension = $file->getClientOriginalExtension();

                $timestamp = $now->format('YmdHis');
                $fileName = $timestamp . '_' . $fileNameWithoutExt . '.' . $extension;

                $business_reg_path = $file->storeAs(
                    "document-upload/business_registration/{$year}/{$month}",
                    $fileName,
                    'public'
                );

                $data['docs_business_reg'] = $business_reg_path;

                if ($business->docs_business_reg) {
                    Storage::disk('public')->delete($business->docs_business_reg);
                }

            } else {
                $data['docs_business_reg'] = $business->docs_business_reg;
            }

            /* ================= BIR 2303 ================= */
            if ($request->hasFile('bir_2303')) {

                $file = $request->file('bir_2303');
                $originalName = $file->getClientOriginalName();

                $fileNameWithoutExt = pathinfo($originalName, PATHINFO_FILENAME);
                $extension = $file->getClientOriginalExtension();

                $timestamp = $now->format('YmdHis');
                $fileName = $timestamp . '_' . $fileNameWithoutExt . '.' . $extension;

                $bir_2303_path = $file->storeAs(
                    "document-upload/bir_2303/{$year}/{$month}",
                    $fileName,
                    'public'
                );

                $data['docs_bir_2303'] = $bir_2303_path;

                if ($business->docs_bir_2303) {
                    Storage::disk('public')->delete($business->docs_bir_2303);
                }

            } else {
                $data['docs_bir_2303'] = $business->docs_bir_2303;
            }

            /* ================= INTERNAL REDRESS ================= */
            if ($request->hasFile('internal_redress')) {

                $file = $request->file('internal_redress');
                $originalName = $file->getClientOriginalName();

                $fileNameWithoutExt = pathinfo($originalName, PATHINFO_FILENAME);
                $extension = $file->getClientOriginalExtension();

                $timestamp = $now->format('YmdHis');
                $fileName = $timestamp . '_' . $fileNameWithoutExt . '.' . $extension;

                $internal_redress_path = $file->storeAs(
                    "document-upload/internal_redress/{$year}/{$month}",
                    $fileName,
                    'public'
                );

                $data['docs_internal_redress'] = $internal_redress_path;

                if ($business->docs_internal_redress) {
                    Storage::disk('public')->delete($business->docs_internal_redress);
                }

            } else {
                $data['docs_internal_redress'] = $business->docs_internal_redress;
            }

            $data['is_bmbe'] = $request->input('is_bmbe');

            /* ================= BMBE DOC ================= */
            if ($request->hasFile('bmbe_doc')) {

                $file = $request->file('bmbe_doc');
                $originalName = $file->getClientOriginalName();

                $fileNameWithoutExt = pathinfo($originalName, PATHINFO_FILENAME);
                $extension = $file->getClientOriginalExtension();

                $timestamp = $now->format('YmdHis');
                $fileName = $timestamp . '_' . $fileNameWithoutExt . '.' . $extension;

                $bmbe_doc_path = $file->storeAs(
                    "document-upload/bmbe_doc/{$year}/{$month}",
                    $fileName,
                    'public'
                );

                $data['bmbe_doc'] = $bmbe_doc_path;

                if ($business->bmbe_doc) {
                    Storage::disk('public')->delete($business->bmbe_doc);
                }

            } else {
                $data['bmbe_doc'] = $business->bmbe_doc;
            }

            $data['busn_category_id'] = $request->input('busn_category_id');

            /* ================= BUSINESS VALUATION ================= */
            if ($request->hasFile('busn_valuation_doc')) {

                $file = $request->file('busn_valuation_doc');
                $originalName = $file->getClientOriginalName();

                $fileNameWithoutExt = pathinfo($originalName, PATHINFO_FILENAME);
                $extension = $file->getClientOriginalExtension();

                $timestamp = $now->format('YmdHis');
                $fileName = $timestamp . '_' . $fileNameWithoutExt . '.' . $extension;

                $busn_valuation_doc_path = $file->storeAs(
                    "document-upload/busn_valuation_doc/{$year}/{$month}",
                    $fileName,
                    'public'
                );

                $data['busn_valuation_doc'] = $busn_valuation_doc_path;

                if ($business->busn_valuation_doc) {
                    Storage::disk('public')->delete($business->busn_valuation_doc);
                }

            } else {
                $data['busn_valuation_doc'] = $business->busn_valuation_doc;
            }

            if ($request->input('is_bmbe') == 0) {
                $data['bmbe_doc'] = null;
                if ($business->bmbe_doc) {
                    Storage::disk('public')->delete($business->bmbe_doc);
                }
                
            }else{
                $data['busn_valuation_doc'] = null;
                if ($business->busn_valuation_doc) {
                    Storage::disk('public')->delete($business->busn_valuation_doc);
                }
                $data['busn_category_id'] = null;
            }
            $saved = $business->update($data);

            if ($saved) {
                \Log::info('Business documents saved successfully for business ID: '.$business->id);
            } else {
                \Log::error('Failed to save business documents for business ID: '.$business->id);
            }
            $this->AdditionalPermitsstore($request, $business->id);

            // return redirect()->back()
            //     // ->with('go_to_payments', true)
            //     ->with('go_to_confirmations', true)
            //     ->with('business_id', $request->business_id)
            //     ->with('business', $business);
            $hashids = new Hashids(env('APP_KEY'), 10);
            $busn_id = $hashids->encode($request->business_id);
            return redirect()->route('business.create', ['business_id' => $busn_id])
                ->with('go_to_confirmations', true)
                ->with('business', $business);

        } catch (\Exception $e) {
            \Log::error('Registration Error: '.$e->getMessage());

            return redirect()->back()->withErrors(['error' => 'Failed to save documents. Please try again.']);
        }
    }

    public function check_tin_num(Request $request)
    {
        $tin = $request->input('tin');
        $businessId = $request->input('business_id'); // for excluding current record

        $exists = \App\Models\Business::where('tin', $tin)
            ->when($businessId, fn ($query) => $query->where('id', '!=', $businessId))
            ->exists();

        return response()->json(['exists' => $exists]);
    }

    public function AdditionalPermitsstore(Request $request, $id)
    {
        $now   = now();
        $year  = $now->format('Y');
        $month = $now->format('M');
        $uploadDir = "document-upload/additional_permit/{$year}/{$month}";

        if (!Storage::disk('public')->exists($uploadDir)) {
            Storage::disk('public')->makeDirectory($uploadDir);
        }

        $request->validate([
            'attachment.*' => 'file|mimes:jpg,jpeg,png,pdf|max:10240',
        ], [
            'attachment.*.file'  => 'The uploaded file must be a valid file.',
            'attachment.*.mimes'=> 'Only jpg, jpeg, png, and pdf files are allowed.',
            'attachment.*.max'  => 'Each file must not be larger than 10MB.',
        ]);

        $names = $request->input('document_name', []);
        $files = $request->file('attachment', []);

        foreach ($names as $index => $name) {

            $file = $files[$index] ?? null;

            if ($file && $file->isValid()) {

                $extension = $file->getClientOriginalExtension();
                $filename  = $now->format('YmdHis') . '_' . Str::random(5) . '.' . $extension;
                $path = $file->storeAs($uploadDir, $filename, 'public');

                DB::table('business_documents')->insert([
                    'busn_id'      => $id,
                    'year'         => $now->year,
                    'name'         => $name,
                    'attachment'   => $path,
                    'created_by'   => Auth::id(),
                    'created_date' => $now,
                ]);
            }
        }

        // return back()->with('success', 'Documents uploaded successfully.');
    }

    public function AdditionalPermitsstoreView(Request $request, $id)
    {
        $business = Business::find($id);

        $now   = now();
        $year  = $now->format('Y');
        $month = $now->format('M');
        $uploadDir = "document-upload/additional_permit/{$year}/{$month}";

        if (!Storage::disk('public')->exists($uploadDir)) {
            Storage::disk('public')->makeDirectory($uploadDir);
        }

        $request->validate([
            'attachment.*' => 'file|mimes:jpg,jpeg,png,pdf|max:10240',
        ], [
            'attachment.*.file'  => 'The uploaded file must be a valid file.',
            'attachment.*.mimes' => 'Only jpg, jpeg, png, and pdf files are allowed.',
            'attachment.*.max'   => 'Each file must not be larger than 10MB.',
        ]);

        $names = $request->input('document_name', []);
        $files = $request->file('attachment', []);

        foreach ($names as $index => $name) {

            $file = $files[$index] ?? null;

            if ($file && $file->isValid()) {

                $extension = $file->getClientOriginalExtension();
                $filename  = $now->format('YmdHis') . '_' . Str::random(5) . '.' . $extension;
                $path = $file->storeAs($uploadDir, $filename, 'public');

                DB::table('business_documents')->insert([
                    'busn_id'      => $id,
                    'year'         => $now->year,
                    'name'         => $name,
                    'attachment'   => $path,
                    'created_by'   => Auth::id(),
                    'created_date' => $now,
                ]);
            }
        }
        DB::table('user_logs')->updateOrInsert(
            [
                'busn_id'   => $business->id,
                'action_id' => 3, 
                'created_date'     => now(),
            ],
            [
            'action_name'      => 'updated',
            'message'          => Auth::user()->name . ' manually update the additional permit section with Sec-No. ' 
                                  . $business->trustmark_id
                                  . ' dated ' . now()->format('Y-m-d H:i:s') . '.',
            'public_ip_address'=> $request->ip(),
            'status'           => $business->status,
            'remarks'          => $business->admin_remarks,
            'longitude'        => $request->input('longitude'), 
            'latitude'         => $request->input('latitude'),  
            'created_by'       => Auth::id(),
            'created_by_name'  => Auth::user()->name,
            
        ]);
        // return back()->with('success', 'Documents uploaded successfully.');
    }
    public function download_AdditionalDocuments($id)
    {
        $business = DB::table('business_documents')->where('id', $id)->first();

        if (! $business || ! $business->attachment) {
            abort(404, 'File not found');
        }

        $fileRelativePath = str_replace('storage/', '', $business->attachment);
        $filePath = storage_path('app/public/'.$fileRelativePath);

        if (! file_exists($filePath)) {
            $filePath = public_path('storage/'.$fileRelativePath);
            if (! file_exists($filePath)) {
                abort(404, 'File not found on server');
            }
        }

        $extension = strtolower(pathinfo($filePath, PATHINFO_EXTENSION));

        $mimeTypes = [
            'pdf' => 'application/pdf',
            'jpg' => 'image/jpeg',
            'jpeg' => 'image/jpeg',
            'png' => 'image/png',
        ];

        $contentType = $mimeTypes[$extension] ?? 'application/octet-stream';

        return response()->file($filePath, [
            'Content-Type' => $contentType,
        ]);
    }

    public function AdditionalPermitdestroy($id)
    {
        $document = DB::table('business_documents')->where('id', $id)->first();

        if (! $document) {
            return response()->json(['message' => 'Document not found'], 404);
        }
        if ($document->attachment && Storage::disk('public')->exists($document->attachment)) {
            Storage::disk('public')->delete($document->attachment);
        }
        DB::table('business_documents')->where('id', $id)->delete();

        return response()->json(['message' => 'Document and file deleted successfully']);
    }

    public function destroy($id)
    {
        $business = Business::findOrFail($id);
        $business->delete();

        return redirect()->route('business.index');
    }

    // submit form
    public function submit_form(Request $request)
    {
        // $business = Business::find($request->input('business_id'));

        // $now = Carbon::now();
        // $business->status = 'UNDER EVALUATION';
        // $business->trustmark_id = $now->format('ymd').'-'.$now->format('His').substr((string) $now->micro, 0, 2);
        // $business->date_issued = $now;
        // $business->submit_date = $now;
        // $business->save();
        
        $business = Business::findOrFail($request->input('business_id'));
        $now = Carbon::now();

        $trustmarkId = $now->format('ymd-His').substr((string) $now->micro, 0, 2);
        $business->update([
            'status' => 'UNDER EVALUATION',
            'trustmark_id' => $trustmarkId,
            // 'date_issued' => $now,
            'submit_date' => $now,
        ]);
        $businessType = TypeCorporation::where('id', $business->corporation_type)->first();
        DB::table('user_logs')->updateOrInsert(
            [
                'busn_id'   => $business->id,
                'action_id' => 16, // unique condition
            ],
            [
            'action_name'      => 'submitted',
            'message'          => Auth::user()->name . ' submitted application with Sec-No. ' 
                                  . $business->trustmark_id . ', Business Type: ' . $businessType->name 
                                  . ', Category: ' . $business->category_id 
                                  . ' dated ' . now()->format('Y-m-d H:i:s') . '.',
            'public_ip_address'=> $request->ip(),
            'status'           => $business->status,
            'remarks'          => $business->admin_remarks,
            'longitude'        => $request->input('longitude'), 
            'latitude'         => $request->input('latitude'),  
            'created_by'       => Auth::id(),
            'created_by_name'  => Auth::user()->name,
            'created_date'     => now(),
        ]);
        DB::table('business_compliance')->updateOrInsert(
            [
                'busn_id'   => $business->id,// unique condition
            ],
            [
            'year'                        => date('Y'),
            'evaluator_id'                => $business->evaluator_id,
            'created_by'       => Auth::id(),
            'created_date'     => now(),
            'modified_by'       => Auth::id(),
            'modified_date'     => now(),
        ]);
        Log::info('Business updated successfully', [
            'business_id' => $business->id,
            'status' => $business->status,
            'trustmark_id' => $business->trustmark_id,
            // 'date_issued' => $business->date_issued,
            'submit_date' => $business->submit_date,
        ]);

        // Log::info('Business updated successfully', [
        //     'business_id' => $business->id,
        //     'status' => $business->status,
        //     'trustmark_id' => $business->trustmark_id,
        //     'date_issued' => $business->date_issued,
        //     'submit_date' => $business->submit_date,
        // ]);

        // Dispatch queued email
        // local
        // dispatch(new ReceivedMailJob($business));

        // DTI Email
        // $sendEmail = $this->business->apiSendReceivedEmail($business);

        // if (! $sendEmail->successful()) {
        //     return 'Email failed: '.$sendEmail->json();
        // }

        // Mandrill
        $sendEmail = $this->email->sendMail('registration', [
            'business' => $business,
        ]);

        Log::info('Registration email attempt', [
            'business_id' => $business->id ?? null,
            'business_name' => $business->business_name ?? null,
            'email_status' => isset($sendEmail['error']) ? 'failed' : 'success',
            'error' => $sendEmail['error'] ?? null,
        ]);

        if (isset($sendEmail['error'])) {
            return 'Email failed: '.$sendEmail['error'];
        }

        return redirect()
        ->route('business.index')
        ->with('success', [
            'You have successfully registered.',
            'Confirmation sent to your email.',
            'Please wait for approval.'
        ]);

    }

    // regenerate and resend email for trustmark_id
    public function regenerateTrustmark($id)
    {
        $business = Business::findOrFail($id);
        $trustmarkId = $business->trustmark_id;

        if (empty($trustmarkId)) {
            // Use updated_at if trustmark_id not set
            $updatedAt = $business->updated_at ?? Carbon::now();
            $trustmarkId = Carbon::parse($updatedAt)->format('ymd-His').substr((string) Carbon::parse($updatedAt)->micro, 0, 2);
        } else {
            // If trustmark_id already exists, use now
            $trustmarkId = Carbon::now()->format('ymd-His').substr((string) Carbon::now()->micro, 0, 2);
        }

        $business->update([
            'trustmark_id' => $trustmarkId,
            // 'date_issued' => Carbon::now(),
            'submit_date' => Carbon::now(),
        ]);

        Log::info('Trustmark ID regenerated successfully', [
            'business_id' => $business->id,
            'new_trustmark_id' => $trustmarkId,
        ]);

        // Mandrill
        $sendEmail = $this->email->sendMail('regenerateTrustmarkId', [
            'business' => $business,
        ]);

        Log::info('Regenerate and resent trustmark id', [
            'business_id' => $business->id ?? null,
            'trustmark_id' => $trustmarkId,
            // 'date_issued' => Carbon::now(),
            'submit_date' => Carbon::now(),
        ]);

        if (isset($sendEmail['error'])) {
            return 'Email failed: '.$sendEmail['error'];
        }

        return redirect()->back()->with('success', 'Trustmark ID regenerated successfully.');
    }

    public function paymentMothod($business_id)
    {
        $hashids = new Hashids(env('APP_KEY'), 10);
        $busn_id = $hashids->decode($business_id)[0];
        // $busn_id = Crypt::decrypt($business_id);
        $total_amount = '0.00';
        $total = BusinessFees::where('busn_id', $busn_id)->sum('amount');
        if ($total > 0) {
            $total_amount = number_format($total, 2);
            $APP_ENV = app()->environment();
            if ($APP_ENV == 'prod') {
                $config = config('constants.tlpePaymentConfigProd');
            } else {
                $config = config('constants.tlpePaymentConfig');
            }

            $paymentOptions = [];
            $curl = curl_init();
            curl_setopt_array($curl, [
                CURLOPT_URL => $config['apiBase'].'/options',
                CURLOPT_RETURNTRANSFER => true,
                CURLOPT_ENCODING => '',
                CURLOPT_MAXREDIRS => 10,
                CURLOPT_TIMEOUT => 0,
                CURLOPT_FOLLOWLOCATION => true,
                CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
                CURLOPT_CUSTOMREQUEST => 'GET',
                CURLOPT_HTTPHEADER => [
                    'Authorization: '.$config['token'],
                    'Content-Type: application/json',
                ],
            ]);
            $response = curl_exec($curl);
            curl_close($curl);
            $paymentChannels = json_decode($response, true);
            /*$paymentChannels = [
                [
                    'value' => 'Visa',
                    'code' => 'eyJ0eXAiOiJKV1QiLCJhbGciOiJIUzI1NiJ9.eyJpc3MiOiJUTFBFIiwic3ViIjoiVExQRSBCYXNlIFJvdXRlciBBdXRoZW50aWNhdGlvbiIsImF1ZCI6IlRMUEUgQmFzZSBSb3V0ZXIiLCJleHAiOjE3NTIwMzkyNTQsImlhdCI6MTc1MTg2NjQ1NCwianRpIjoiNDZmZWZmZjktODgwNi00MTdiLWFlMjItMmRhMTIzMTc5MDEzIiwiZGF0YSI6ImFkMGU4OWNhLWYxYzAtNDFjNi1iNDBhLWJmYTAzMDMyYzRmZSJ9.V3azToqioFwrg6t20AnUs0vKGRz30iaUkdS2lIX1o9k',
                    'image' => 'https://dzv00tuelpzbp.cloudfront.net/static/img/logo-po/logo-po-visa-trans.png',
                ],
                [
                    'value' => 'Mastercard',
                    'code' => 'eyJ0eXAiOiJKV1QiLCJhbGciOiJIUzI1NiJ9.eyJpc3MiOiJUTFBFIiwic3ViIjoiVExQRSBCYXNlIFJvdXRlciBBdXRoZW50aWNhdGlvbiIsImF1ZCI6IlRMUEUgQmFzZSBSb3V0ZXIiLCJleHAiOjE3NTIwMzkyNTQsImlhdCI6MTc1MTg2NjQ1NCwianRpIjoiZmZjMzMzYzAtNTM0Zi00YjE1LWFhMmEtMzlmODRjNzFmOWIxIiwiZGF0YSI6IjNkN2Q4NDE5LWE0NjQtNGRhNi1hYmY0LTU5MzViMjcwMGNkMiJ9.PSDFSilZRx5uzba_iwFSE3NEInrYMsvwebxcdTloHGA',
                    'image' => 'https://dzv00tuelpzbp.cloudfront.net/static/img/logo-po/logo-po-master-trans.png',
                ],
                [
                    'value' => 'GCash',
                    'code' => 'eyJ0eXAiOiJKV1QiLCJhbGciOiJIUzI1NiJ9.eyJpc3MiOiJUTFBFIiwic3ViIjoiVExQRSBCYXNlIFJvdXRlciBBdXRoZW50aWNhdGlvbiIsImF1ZCI6IlRMUEUgQmFzZSBSb3V0ZXIiLCJleHAiOjE3NTIwMzkyNTQsImlhdCI6MTc1MTg2NjQ1NCwianRpIjoiZmY1MTk2MzUtNGU1MS00YTA5LWIwM2UtNjI2ZTc2NzM1MjQyIiwiZGF0YSI6IjVlMmYzOGM2LWM0Y2YtNGU2NC05NmRlLTFlZGMxZWE3Y2ZmOCJ9.6OgHrVKlBKyB96mQdVAfTwTT98ckDjDSzcqalDOOYRc',
                    'image' => 'https://dzv00tuelpzbp.cloudfront.net/static/img/logo-po/logo-po-gcash-trans.png',
                ],
                [
                    'value' => 'Instapay QR',
                    'code' => 'eyJ0eXAiOiJKV1QiLCJhbGciOiJIUzI1NiJ9.eyJpc3MiOiJUTFBFIiwic3ViIjoiVExQRSBCYXNlIFJvdXRlciBBdXRoZW50aWNhdGlvbiIsImF1ZCI6IlRMUEUgQmFzZSBSb3V0ZXIiLCJleHAiOjE3NTIwMzkyNTQsImlhdCI6MTc1MTg2NjQ1NCwianRpIjoiYjQxMzk4OTYtNGY4Ny00NDdhLWFlNTQtZTk5NDVhN2M5YWYyIiwiZGF0YSI6IjVhYzliNzUyLWEyYjktNGFkMi1hNGM2LTQxZDYwMjM0ZjdkZSJ9.U5ar2V44jRCOj8NPZQUwf5G1NlUjb9AYm1NL9YZsrkQ',
                    'image' => 'https://dzv00tuelpzbp.cloudfront.net/static/img/logo-po/logo-po-instapay-trans.png',
                ],
                [
                    'value' => '7-Eleven',
                    'code' => 'eyJ0eXAiOiJKV1QiLCJhbGciOiJIUzI1NiJ9.eyJpc3MiOiJUTFBFIiwic3ViIjoiVExQRSBCYXNlIFJvdXRlciBBdXRoZW50aWNhdGlvbiIsImF1ZCI6IlRMUEUgQmFzZSBSb3V0ZXIiLCJleHAiOjE3NTIwMzkyNTQsImlhdCI6MTc1MTg2NjQ1NCwianRpIjoiMDA3MmI2ZGItYjIzNi00ODMzLWI3MGEtOTYxZmM5ZmNhMGJlIiwiZGF0YSI6ImYyZThlODg4LWU5MmEtNGJmZi1iODM5LWNhNzFmZWQ2YjFiYyJ9.d8ddTyfRjiNn4cig02TQfR0qNej6cM_OIfUzWMyrCo4',
                    'image' => 'https://dzv00tuelpzbp.cloudfront.net/static/img/logo-po/1745220618056_f2290580-2f66-4201-a1df-dc775636145d',
                ],
                [
                    'value' => 'ECPay',
                    'code' => 'eyJ0eXAiOiJKV1QiLCJhbGciOiJIUzI1NiJ9.eyJpc3MiOiJUTFBFIiwic3ViIjoiVExQRSBCYXNlIFJvdXRlciBBdXRoZW50aWNhdGlvbiIsImF1ZCI6IlRMUEUgQmFzZSBSb3V0ZXIiLCJleHAiOjE3NTIwMzkyNTQsImlhdCI6MTc1MTg2NjQ1NCwianRpIjoiNGM2ZGYyMDktODMzNy00MzZkLWEyZWEtZTFmNjc0NTc4NWZlIiwiZGF0YSI6ImJhZTBhODNjLWNlZjEtNDI2Zi05NWRhLWJkMGU1NWZiZTQ5ZiJ9.D_kXyWcBtfwR54FAeq_H4AY_PflQNsFKGy2ntCaydD0',
                    'image' => 'https://dzv00tuelpzbp.cloudfront.net/static/img/logo-po/1745220570360_f2290580-2f66-4201-a1df-dc775636145d',
                ],
            ];*/

            return view('business.payment_method', compact('total_amount', 'business_id', 'paymentChannels'));
        } else {
            echo 'No fees found.';
        }
    }

    public function addpaymentayment(Request $request)
    {
        $channel_code = $request->input('channel_code');
        $business_id = $request->input('business_id');
        $hashids = new Hashids(env('APP_KEY'), 10);
        $business_id = $hashids->decode($business_id)[0];
        // $business_id = Crypt::decrypt($business_id);
        $amount = BusinessFees::where('busn_id', $business_id)->sum('amount');

        $validator = \Validator::make(
            $request->all(),
            [
                'channel_code' => 'required',
                'business_id' => 'required',
            ]
        );
        $arr = ['ESTATUS' => 0];
        if ($validator->fails()) {
            $messages = $validator->getMessageBag();
            $arr['field_name'] = $messages->keys()[0];
            $arr['error'] = $messages->all()[0];

            return $this->sendError($arr['error']);
        }
        $ids = $request->input('id');
        $transaction_id = 'TRUSTMARK'.str_replace('.', '', microtime(true));

        // add to payment table
        $payment = new Payment;
        $payment->business_id = $business_id;
        $payment->transaction_id = $transaction_id;
        $payment->sub_total = $amount;
        $payment->currency = 'Philippine peso';
        $payment->payment_method = 'Online';
        $payment->payment_in_process = '1';
        $payment->channel_code = $channel_code;
        $total_amount = $amount;
        $channel_tax_amount = 0;
        $payment->final_total_amount = $channel_tax_amount + $total_amount;
        $payment->channel_tax_amount = $channel_tax_amount;
        $payment->save();

        // update business table
        $business = Business::find($business_id);
        if (! $business) {
            return $this->sendError('Business not found');
        }
        /*
        // don't uncomment this one
        $business->amount = $amount;
        $business->status = 'APPROVED';
        $business->payment_id = $payment->id;
        $business->save();*/

        $data['transaction_id'] = $transaction_id;

        return $this->sendResponse($data, 'Success.');
    }
    /*public function addpaymentayment_fiuu(Request $request)
    {
        $channel_id = $request->input('channel_id');
        $amount = $request->input('amount');
        $business_id = $request->input('business_id');
        $business_id = Crypt::decrypt($business_id);

        $validator = \Validator::make(
            $request->all(),
            [
                'channel_id' => 'required',
                'amount' => 'required',
                'business_id' => 'required'
            ]
        );
        $arr = array('ESTATUS' => 0);
        if ($validator->fails()) {
            $messages = $validator->getMessageBag();
            $arr['field_name'] = $messages->keys()[0];
            $arr['error'] = $messages->all()[0];
            return $this->sendError($arr['error']);
        }
        $ids = $request->input('id');

        $transaction_id = "TRUSTMARK" . str_replace(".", "", microtime(true));


        // add to payment table
        $payment = new Payment();
        $payment->business_id = $business_id;
        $payment->transaction_id = $transaction_id;
        $payment->sub_total = $amount;
        $payment->currency = 'Philippine peso';
        $payment->payment_method = 'Online';
        $payment->payment_in_process = '1';
        $payment->channel_id = $channel_id;

        $arrChanel = DB::table('fiuu_payment_channels')->select('formula')->where('id', $channel_id)->first();

        $total_amount = $amount;
        $channel_tax_amount = 0;
        if (isset($arrChanel)) {
            $formula = strtolower($arrChanel->formula);
            $hasPercentage = preg_match('/(\d+(\.\d+)?)%/', $formula);
            $sanitizedFormula = preg_replace('/(\d+(\.\d+)?)%/', '($1/100)', $formula);
            $sanitizedFormula = str_replace('x', $total_amount, $sanitizedFormula);
            eval("\$channel_tax_amount = $sanitizedFormula;");
        }
        $payment->final_total_amount = $channel_tax_amount + $total_amount;
        $payment->channel_tax_amount = $channel_tax_amount;
        $payment->save();

        // update business table
        $business = Business::find($business_id);
        if (!$business) {
            return $this->sendError('Business not found');
        }


        // don't uncomment this one
        //$business->amount = $amount;
        //$business->status = 'APPROVED';
        //$business->payment_id = $payment->id;
        //$business->save();

        $data['transaction_id'] = $transaction_id;
        return $this->sendResponse($data, 'Success.');
    }*/

    public function displayPaymentPage(Request $request, $transaction_id)
    {
        $merchantCallbackUrl = url('/updatePaymentResponse');
        $merchantReturnUrl = url('/updatePaymentResponse');

        $arrDtls = DB::table('payments AS pmt')->join('businesses AS ap', 'ap.id', '=', 'pmt.business_id')->select('ap.user_id', 'final_total_amount', 'channel_code')->where('transaction_id', $transaction_id)->first();
        if (isset($arrDtls)) {
            $amount = $arrDtls->final_total_amount;
            $user_id = $arrDtls->user_id;
            $channel_code = $arrDtls->channel_code;

            if ($amount > 0 && $user_id > 0) {
                $arr['channel_code'] = $channel_code;
                $arr['user_id'] = $user_id;
                $arr['amount'] = $amount;
                $arr['merchantCallbackUrl'] = $merchantCallbackUrl;
                $arr['merchantReturnUrl'] = $merchantReturnUrl;
                $arr['transaction_id'] = $transaction_id;
                $this->loadPaymentHtmlPage($arr);
            }
        }
    }

    public function loadPaymentHtmlPage($arr)
    {
        echo '<p style="text-align:center; margin: 5% 0; font-size:2rem;" class="loading"><span id="paymentLoader">Please wait it redirecting to gateway...</span><span style="color:white;">.</span></p>';
        $channelFile = 'GCash.php';
        $formula = '';
        $amount = floatval($arr['amount']);
        // $amount = 1;
        $merchantReturnUrl = $arr['merchantReturnUrl'];

        $arrUser = $arrChannel = DB::table('users')->select('name', 'email', 'ctc_no', 'first_name', 'last_name')->where('id', $arr['user_id'])->first(); 
        if (isset($arrUser)) {
            $orderid = $arr['transaction_id'];
            $merchantCallbackUrl = $arr['merchantCallbackUrl'].'?orderid='.$orderid;
            $APP_ENV = app()->environment();
            if ($APP_ENV == 'prod') {
                $config = config('constants.tlpePaymentConfigProd');
            } else {
                $config = config('constants.tlpePaymentConfig');
            }
            $optionKey = $arr['channel_code'];
            $firstname = $arrUser->first_name ?? $arrUser->name;
            $lastname = $arrUser->last_name ?? '.';
            // Build JWT Payload
            $payload = [
                'data' => [
                    'customer' => [
                        'first_name' => $firstname,
                        'last_name' => $lastname,
                        'contact' => [
                            'email' => strtolower($arrUser->email),
                            'mobile' => $arrUser->ctc_no,
                        ],
                        'billing_address' => [
                            'line1' => '123 Main Street',
                            'city_municipality' => 'Metro City',
                            'zip' => '1000',
                            'state_province_region' => 'Metro',
                            'country_code' => 'PH',
                        ],
                    ],
                    'payment' => [
                        'description' => 'Checkout Payment',
                        'amount' => $amount,
                        'currency' => $config['currency'],
                        'option' => $optionKey,
                        'merchant_reference_id' => $orderid,
                    ],
                    'route' => [
                        'callback_url' => $merchantCallbackUrl,
                        'notify_user' => true,
                    ],
                    'customer_ip_address' => $_SERVER['REMOTE_ADDR'],
                    'time_offset' => '+08:00',
                ],
            ];

            $jwt = $this->jwtEncode($payload, $config['jwtSecret']);

            // ========== POST TO /checkout ==========
            $curl = curl_init();
            curl_setopt_array($curl, [
                CURLOPT_URL => $config['apiBase'].'/checkout',
                CURLOPT_RETURNTRANSFER => true,
                CURLOPT_ENCODING => '',
                CURLOPT_MAXREDIRS => 10,
                CURLOPT_TIMEOUT => 0,
                CURLOPT_FOLLOWLOCATION => true,
                CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
                CURLOPT_CUSTOMREQUEST => 'POST',
                CURLOPT_POSTFIELDS => json_encode(['payload' => $jwt]),
                CURLOPT_HTTPHEADER => [
                    'Authorization: '.$config['token'],
                    'Content-Type: application/json',
                ],
            ]);
            $checkoutResp = curl_exec($curl);
            curl_close($curl);

            $checkoutData = json_decode($checkoutResp, true);

            // Redirect to payment_url if available
            if (isset($checkoutData['data']['payment_url'])) {
                header('Location: '.$checkoutData['data']['payment_url']);
                exit;
            } else {
                /* ?><style> .loading{display: none;}</style><?php */
                echo '<h3>Checkout Failed</h3><pre>'.print_r($checkoutData, true).'</pre>';
            }
        }else{
             echo '<h3>Data Not Found.</h3>';
        }
    }

    public function loadPaymentHtmlPage_fiuu($arr)
    {
        echo '<p style="text-align:center; margin: 5% 0; font-size:2rem;"><span id="paymentLoader">Please wait it redirecting to gateway...</span><span style="color:white;">.</span></p>';

        $channelFile = 'GCash.php';
        $formula = '';
        if ($arr['channel_id'] > 0) {
            $arrChannel = DB::table('fiuu_payment_channels')->select('formula', 'file_name')->where('id', $arr['channel_id'])->first();
            if (isset($arrChannel)) {
                $channelFile = $arrChannel->file_name;
                $formula = $arrChannel->formula;
            }
        }
        $amount = number_format($arr['amount'], 2, '.', '');
        $amount = 1;
        $merchantReturnUrl = $arr['merchantReturnUrl'];
        $merchantCallbackUrl = $arr['merchantCallbackUrl'];

        $arrUser = $arrChannel = DB::table('users')->select('name', 'email')->where('id', $arr['user_id'])->first();
        if (isset($arrUser)) {
            $orderid = $arr['transaction_id'];
            $config = config('constants.paymentConfig');
            $merchantID = $config['merchant'];
            $verifykey = $config['verifykey'];
            $vcode = md5($amount.$merchantID.$orderid.$verifykey);
            $country = $config['country'];
            $currency = $config['currency'];
            $cancelUrl = $arr['merchantReturnUrl'].'?orderid='.$orderid.'&status=2';
            $merchantReturnUrl = $arr['merchantReturnUrl'].'?orderid='.$orderid;
            $config['channelUrl'] = $config['channelUrl'].$merchantID.'/'.$channelFile;

            ?>
            <form action="<?php echo $config['channelUrl']; ?>/" method="post" name="invoiceForm" id="invoiceForm">
                <input name="amount" value="<?php echo $amount; ?>" type="hidden">
                <input name="orderid" value="<?php echo $orderid; ?>" type="hidden">
                <input name="bill_name" value="<?php echo $arrUser->name; ?>" type="hidden">
                <input name="bill_email" value="<?php echo $arrUser->email; ?>" type="hidden">
                <input name="bill_mobile" value="+63 917 518 7320" type="hidden">
                <input name="bill_desc" value="test" type="hidden">

                <input name="country" value="<?php echo $country; ?>" type="hidden">
                <input name="vcode" value="<?php echo $vcode; ?>" type="hidden">
                <input name="currency" value="<?php echo $currency; ?>" type="hidden">
                <input name="returnurl" value="<?php echo $merchantReturnUrl; ?>" type="hidden">
                <input name="cancelurl" value="<?php echo $cancelUrl; ?>" type="hidden">
            </form>
            <script type="text/javascript">
                document.getElementById('invoiceForm').submit();
            </script>
        <?php
        }
    }

    public function updatePaymentResponse(Request $request)
    {
        $transactionId = trim($_REQUEST['orderid']);
        $arrPayment = DB::table('payments')->where('transaction_id', $transactionId)->select('payment_status', 'id','business_id')->orderBy('id', 'DESC')->first();
        $payment_status = 2;
        if (isset($arrPayment)) {
            $business_id = $arrPayment->business_id;
            if ($arrPayment->payment_status == 1) {
                $payment_status = $arrPayment->payment_status;
            }
        }
        $hashids = new Hashids(env('APP_KEY'), 10);
        $busn_id = $hashids->encode($business_id);
        // $busn_id = encrypt($business_id);
        $redirectUrl = url('/business/view/'.$busn_id);
        echo '<p style="text-align:center; margin: 12% 0; font-size:2rem;">Please wait, we are redirecting the page…</p>';
        ?>
        <script>
            var payment_status = <?php echo (int)$payment_status; ?>;
            var redirectUrl = "<?php echo $redirectUrl; ?>";

            if (payment_status === 1) {
                localStorage.setItem('paymentSuccess', 'true');
            } else {
                localStorage.setItem('paymentError', 'true');
            }
            window.location.href = redirectUrl;
        </script><?php
    }

    public function checkPaymentResponse(Request $request)
    {
        $business_id = $request->input('business_id');
        $hashids = new Hashids(env('APP_KEY'), 10);
        $business_id = $hashids->decode($business_id)[0];
        // $business_id = Crypt::decrypt($business_id);
        $arrPayment = DB::table('payments')->where('business_id', $business_id)->select('payment_status', 'id')->orderBy('id', 'DESC')->first();
        $message = 'Payment still pending';
        $payment_status = 2;
        if (isset($arrPayment)) {
            if ($arrPayment->payment_status == 1) {
                $message = 'Payment paid successfully';
                $payment_status = $arrPayment->payment_status;
            }
        }
        $data['payment_status'] = $payment_status;

        return $this->sendResponse($data, $message);
    }

    public function sendResponse($result, $message)
    {
        $response = [
            'status' => true,
            'data' => $result,
            'message' => $message,
        ];

        return json_encode($response);
    }

    public function sendError($error, $errorMessages = [], $code = 404)
    {
        $response = [
            'status' => false,
            'message' => $error,
        ];

        return json_encode($response);
    }

    // testing purpoes
    public function save_payment(Request $request)
    {
        $business = Business::find($request->input('business_id'));
        $amount = $request->input('amount');

        if (! $business) {
            return response()->json(['success' => false, 'message' => 'Business not found'], 404);
        }

        $payment = new Payment;
        $payment->business_id = $business->id;
        $payment->transaction_id = 'test';  // dummy value
        $payment->sub_total = '112';
        $payment->currency = 'Philippine peso';
        $payment->payment_method = 'Online';   // dummy value
        $payment->payment_status = 'Success';   // dummy value
        $payment->date = Carbon::now();
        $payment->payment_in_process = '1';  // dummy value;
        $payment->skey = 'Text';  // dummy value
        $payment->cardholder = 'Card Holder';  // dummy value
        $payment->tranID = '123445';  // dummy value
        $payment->txnstatus = 'Success';  // dummy value
        $payment->total_paid_amount = '112';
        $payment->channel_id = '1';  // dummy value
        $payment->save();

        $now = Carbon::now();
        $business->payment_id = $payment->id;
        // $business->trustmark_id = $now->format('ymdHis') . substr((string) $now->micro, 0, 2);

        // qr
        $fileName = $this->business->qr($business);
        $business->qr_code = $fileName;
        //$business->qr_code = 'storage/document-upload/qr_code/'.$fileName;

        // certificate
        $fileName2 = $this->business->generateCertificate($business);

        /*$year  = Carbon::now()->format('Y');
        $month = Carbon::now()->format('M');

        $uploadDir = "document-upload/certificate/{$year}/{$month}";
        if (!Storage::disk('public')->exists($uploadDir)) {
            Storage::disk('public')->makeDirectory($uploadDir);
        }
        $oldPath = "document-upload/certificate/{$fileName2}";
        $newPath = "{$uploadDir}/{$fileName2}";

        if (Storage::disk('public')->exists($oldPath)) {
            Storage::disk('public')->move($oldPath, $newPath);
        }
        if (!empty($business->certificate)) {
            $oldCert = str_replace('storage/', '', $business->certificate);
            if (Storage::disk('public')->exists($oldCert)) {
                Storage::disk('public')->delete($oldCert);
            }
        }*/

        $business->certificate = $fileName2;
        //$business->certificate = "storage/{$newPath}";
        $business->save();

        return response()->json([
            'success' => true,
            'business_id' => $business->id,
        ]);
    }

    // testing purpoes
    public function save_payment2(Request $request)
    {
        $id = 66;
        $business = Business::find($id);
        // qr
        $fileName = $this->business->qr($business);
        $business->qr_code = $fileName;
        //$business->qr_code = 'storage/document-upload/qr_code/'.$fileName;
        // certificate
        $fileName2 = $this->business->generateCertificate($business);

        /*$year  = Carbon::now()->format('Y');
        $month = Carbon::now()->format('M');

        $uploadDir = "document-upload/certificate/{$year}/{$month}";
        if (!Storage::disk('public')->exists($uploadDir)) {
            Storage::disk('public')->makeDirectory($uploadDir);
        }
        $oldPath = "document-upload/certificate/{$fileName2}";
        $newPath = "{$uploadDir}/{$fileName2}";

        if (Storage::disk('public')->exists($oldPath)) {
            Storage::disk('public')->move($oldPath, $newPath);
        }
        if (!empty($business->certificate)) {
            $oldCert = str_replace('storage/', '', $business->certificate);
            if (Storage::disk('public')->exists($oldCert)) {
                Storage::disk('public')->delete($oldCert);
            }
        }*/ 

        $business->certificate = $fileName2;
        //$business->certificate = "storage/{$newPath}";
        $business->save();

        return response()->json([
            'success' => true,
            'business_id' => $business->id,
        ]);
    }

    public function certReGenerate($id)
    {
        $business = Business::findOrFail($id);
        $fileName = $this->business->generateCertificate($business);

        /*$year  = Carbon::now()->format('Y');
        $month = Carbon::now()->format('M');
        
        $uploadDir = "document-upload/certificate/{$year}/{$month}";
        if (!Storage::disk('public')->exists($uploadDir)) {
            Storage::disk('public')->makeDirectory($uploadDir);
        }
        $oldPath = "document-upload/certificate/{$fileName}";
        $newPath = "{$uploadDir}/{$fileName}";
        
        if (Storage::disk('public')->exists($oldPath)) {
            Storage::disk('public')->move($oldPath, $newPath);
        }
        if (!empty($business->certificate)) {
            $oldCert = str_replace('storage/', '', $business->certificate);
            if (Storage::disk('public')->exists($oldCert)) {
                Storage::disk('public')->delete($oldCert);
            }
        }*/
        //$business->certificate = "storage/{$newPath}";

        $business->certificate = $fileName;
        echo $business->certificate;exit;
        $business->save();

        return back()->with('success', 'Certificate regenerated successfully.');
    }

    public function view($id)
    {
        // 
        $hashids = new Hashids(env('APP_KEY'), 10);
        $id = $hashids->decode($id)[0];
        // dd($id);exit;
        $business = Business::findOrFail($id);
        $payment = Payment::where('business_id', $id)->first();
        $busines_fee = BusinessFees::where('busn_id', $id)->get();
        $refund_fee=[];
        if(isset($payment)){
            if($payment->payment_status==3 || $payment->payment_status==4){
                $refund_fee = DB::table('payment_refund_history AS pr')->leftJoin('business_fees as bf', 'pr.busn_fee_id', '=', 'bf.id')->where('business_id', $id)->select('fee_name','refund_amount')->get();
            }
        }


        $type_corporation = TypeCorporation::where('is_active', 1)->get();
        $selectedTypeId = $business->corporation_type;
        $regions = Region::where('is_active', 1)->pluck('reg_region', 'id');
        $barangays = Barangay::where('is_active', 1)->pluck('brgy_description', 'id');
        $Eveluator = DB::table('user_admins AS a')
            ->join('users AS b', 'b.id', '=', 'a.user_id')
            ->select('a.user_id', 'b.name')
            ->pluck('b.name', 'a.user_id');
        $categories = Category::where('is_active', 1)->get();
        $requirements = RequirementReps::where('status', 'Active')->get();
        $status = DB::table('application_status')
            ->where('status', 1)
            ->where('during_application', 1)
            ->orderBy('name', 'ASC')
            ->pluck('name', 'id');
        $AdditionalDocuments = DB::table('business_documents')
            ->where('busn_id', $business->id)
            ->where('year', now()->year)
            ->get();
        $business_category = DB::table('application_fee_category')
        // ->orderByDesc('is_default') 
        ->orderBy('id')
        ->get();
        $PaymentChannel = null;
        if ($payment) {
            $PaymentChannel = DB::table('fiuu_payment_channels')
                ->where('id', $payment->channel_id)
                ->first();
        }
        $user = DB::table('users')
            ->where('id', $business->user_id)
            ->first();
        $currentUserId = Auth::id();
        $isAdmin = DB::table('user_admins')
            ->where('user_id', $currentUserId)
            ->value('is_admin');
        $app_status = DB::table('application_status')->where('id', $business->app_status_id)->first();
        $app_canned_messages = DB::table('application_canned_messages')->where('id', $business->app_canned_id)->first();
        $business_compliance = DB::table('business_compliance')->where('busn_id', $business->id)->first();
        $businessCatName  = DB::table('application_fee_category')->where('busn_category_id',$business->busn_category_id)->first();
        // dd($businessCatName);exit;
        // $certificate = str_replace('storage/', '', $business->certificate);
        // $filePathCertificate = storage_path('app/public/'.$certificate);
        $suffixs = DB::connection('project1')->table('name_suffixes')->get();
        $reasons = DB::table('application_canned_messages')->where('id', $business->app_canned_id)->get();
        return view('business.view', compact('business','business_category','isAdmin', 'suffixs','businessCatName','business_compliance','barangays', 'Eveluator', 'type_corporation', 'selectedTypeId', 'regions', 'categories', 'requirements', 'payment', 'id', 'busines_fee', 'status', 'AdditionalDocuments', 'app_status', 'app_canned_messages', 'PaymentChannel', 'user','refund_fee','reasons'));
    }

    public function disapproved_view($id)
    {
        $hashids = new Hashids(env('APP_KEY'), 10);
        $id = $hashids->decode($id)[0];
        // $id = Crypt::decrypt($id);
        $business = Business::findOrFail($id);
        $payment = Payment::where('business_id', $id)->first();
        $busines_fee = BusinessFees::where('busn_id', $id)->get();
        $status = DB::table('application_status')
            ->where('status', 1)
            ->where('during_application', 1)
            ->orderBy('name', 'ASC')
            ->pluck('name', 'id');
        $appStatus = DB::table('application_status')
            ->where('id', $business->app_status_id)
            ->first();
        $appCannedStatus = DB::table('application_canned_messages')
            ->where('id', $business->app_canned_id)
            ->first();
        $AdditionalDocuments = DB::table('business_documents')
            ->where('busn_id', $business->id)
            ->where('year', now()->year)
            ->get();
        $business_category = DB::table('application_fee_category')
        // ->orderByDesc('is_default') 
        ->orderBy('id')
        ->get();
        // dd($appStatus);
        $user = DB::table('users')
            ->where('id', $business->user_id)
            ->first();

        return view('business.disapproved_view', compact('business', 'business_category','payment', 'id', 'busines_fee', 'status', 'appStatus', 'appCannedStatus', 'AdditionalDocuments', 'user'));
    }

    public function confidential(Request $request)
    {
        $businessId = $request->input('id');
        $value = $request->input('value');
        $business = Business::find($businessId);
        if ($business) {
            $business->pic_ctc_no_is_confidential = $value;
            $business->save();

            return response()->json(['message' => 'Business marked as confidential successfully.']);
        }

        return response()->json(['message' => 'Business not found.'], 404);
    }

    public function edit($id)
    {
        $user = Auth::user();
        $hashids = new Hashids(env('APP_KEY'), 10);
        $id = $hashids->decode($id)[0];
        // $id = Crypt::decrypt($id);
        $business = Business::findOrFail($id);
        $type_corporation = TypeCorporation::where('is_active', 1)->get();
        $selectedTypeId = $business->corporation_type;
        $regions = Region::where('is_active', 1)->pluck('reg_region', 'id');
        // $categories = Category::where('is_active', 1)->pluck('name', 'id');
        $categories = Category::where('is_active', 1)->get();
        // $requirements = RequirementReps::where('status', 'Active')->pluck('description', 'id');
        $requirements = RequirementReps::where('status', 'Active')->get();
        $AdditionalDocuments = DB::table('business_documents')
            ->where('busn_id', $business->id)
            ->where('year', now()->year)
            ->get();
        $business_category = DB::table('application_fee_category')
        // ->orderByDesc('is_default') 
        ->orderBy('id')
        ->get();
        $business_compliance = DB::table('business_compliance')->where('busn_id', $business->id)->first();
        $suffixs = DB::connection('project1')->table('name_suffixes')->get();
        $settingsIrm = DB::table('settings')
        ->where('name', 'enabled_irm')
        ->first();
        $business_irm = DB::table('business_irm')
        ->where('busn_id', $business->id)
        ->first();
        return view('business.edit', compact('business', 'suffixs', 'settingsIrm', 'business_irm','business_category','business_compliance','type_corporation', 'selectedTypeId', 'regions', 'categories', 'requirements', 'AdditionalDocuments','user'));
    }

    public function update(Request $request, $id)
    {
        $business = Business::findOrFail($id);

        // Decode JSON URLs array from the request
        $urlsJson = $request->input('url_platform_json', '[]');
        $urls = json_decode($urlsJson, true);

        // Validate URLs array and format
        if (! is_array($urls)) {
            return redirect()->back()
                ->withErrors(['url_platform_json' => 'Invalid URL data format.'])
                ->withInput();
        }

        // Validate each URL format
        foreach ($urls as $url) {
            if (! filter_var($url, FILTER_VALIDATE_URL)) {
                return redirect()->back()
                    ->withErrors(['url_platform_json' => "Invalid URL detected: $url"])
                    ->withInput();
            }
        }

        $rules = [
            'admin_remarks' => 'required',
            'type_id' => 'required',
            'reg_num' => 'required|string',
            'tin_num' => ['required', 'regex:/^\d{3}-\d{3}-\d{3}-\d{5}$/'],
            'business_name' => 'required|string|max:130',
            'franchise' => 'nullable|string',
            'category' => 'required',
            'other_category' => 'nullable|string|max:150',
            'url_platform_json' => 'nullable|string',
            'name' => 'required|string',
            'ctc_no' => 'required|numeric',
            'email' => 'required|email',
            'issued_id' => 'required',
            'req_upload' => 'nullable|file|mimes:jpg,jpeg,png,pdf|max:10240',
            'expired_date' => ['nullable', 'date'], // default: nullable
            'region' => 'required',
            'province' => 'required',
            'municipality' => 'required',
            'barangay' => 'required',
            'address' => 'required|string',
            'business_reg' => 'nullable|file|mimes:jpg,jpeg,png,pdf|max:10240',
            'bir_2303' => 'nullable|file|mimes:jpg,jpeg,png,pdf|max:10240',
            'internal_redress' => 'nullable|file|mimes:jpg,jpeg,png,pdf|max:10240',
        ];

        // Fetch requirements to check expiration rule
        $requirements = RequirementReps::where('status', 'Active')->get();

        if ($request->issued_id) {
            $requirement = $requirements->firstWhere('id', $request->issued_id);

            if ($requirement && trim($requirement->with_expiration) === '1') {
                // Override expired_date validation to required + after or equal today
                $rules['expired_date'] = ['required', 'date', 'after_or_equal:'.now()->format('Y-m-d')];
            }
        }

        // Now validate with dynamically built rules
        $validated = $request->validate($rules);
        $business->admin_remarks = $validated['admin_remarks'];
        $business->corporation_type = $validated['type_id'];
        $business->reg_num = $validated['reg_num'];
        $business->tin = $validated['tin_num'];
        $business->business_name = $validated['business_name'];
        $business->franchise = $validated['franchise'];
        $business->category_id = $validated['category'];
        $business->category_other_description = $validated['other_category'];
        $business->url_platform = $urls;
        $business->pic_name = $validated['name'];
        $business->pic_ctc_no = $validated['ctc_no'];
        $business->pic_email = $validated['email'];
        $business->requirement_id = $validated['issued_id'];
        $business->requirement_expired = $validated['expired_date'];
        $business->region_id = $validated['region'];
        $business->province_id = $validated['province'];
        $business->municipality_id = $validated['municipality'];
        $business->barangay_id = $validated['barangay'];
        $business->complete_address = $validated['address'];

        $now   = now();
        $year  = $now->format('Y');
        $month = $now->format('M');

        /* ============ REQUIREMENT UPLOAD ============ */
        if ($request->hasFile('req_upload')) {

            $file = $request->file('req_upload');
            $originalName = $file->getClientOriginalName();
            $fileNameWithoutExt = pathinfo($originalName, PATHINFO_FILENAME);
            $extension = $file->getClientOriginalExtension();

            $fileName = time() . '_' . $fileNameWithoutExt . '.' . $extension;

            $req_upload_path = $file->storeAs(
                "document-upload/requirement_reps/{$year}/{$month}",
                $fileName,
                'public'
            );

            if ($business->requirement_upload) {
                Storage::disk('public')->delete($business->requirement_upload);
            }

            $business->requirement_upload = $req_upload_path;
        }

        /* ============ BUSINESS REGISTRATION ============ */
        if ($request->hasFile('business_reg')) {

            $file = $request->file('business_reg');
            $originalName = $file->getClientOriginalName();
            $fileNameWithoutExt = pathinfo($originalName, PATHINFO_FILENAME);
            $extension = $file->getClientOriginalExtension();

            $fileName = time() . '_' . $fileNameWithoutExt . '.' . $extension;

            $business_reg_path = $file->storeAs(
                "document-upload/business_registration/{$year}/{$month}",
                $fileName,
                'public'
            );

            if ($business->docs_business_reg) {
                Storage::disk('public')->delete($business->docs_business_reg);
            }

            $business->docs_business_reg = $business_reg_path;
        }

        /* ============ BIR 2303 ============ */
        if ($request->hasFile('bir_2303')) {

            $file = $request->file('bir_2303');
            $originalName = $file->getClientOriginalName();
            $fileNameWithoutExt = pathinfo($originalName, PATHINFO_FILENAME);
            $extension = $file->getClientOriginalExtension();

            $fileName = time() . '_' . $fileNameWithoutExt . '.' . $extension;

            $bir_2303_path = $file->storeAs(
                "document-upload/bir_2303/{$year}/{$month}",
                $fileName,
                'public'
            );

            if ($business->docs_bir_2303) {
                Storage::disk('public')->delete($business->docs_bir_2303);
            }

            $business->docs_bir_2303 = $bir_2303_path;
        }

        /* ============ INTERNAL REDRESS ============ */
        if ($request->hasFile('internal_redress')) {

            $file = $request->file('internal_redress');
            $originalName = $file->getClientOriginalName();
            $fileNameWithoutExt = pathinfo($originalName, PATHINFO_FILENAME);
            $extension = $file->getClientOriginalExtension();

            $fileName = time() . '_' . $fileNameWithoutExt . '.' . $extension;

            $internal_redress_path = $file->storeAs(
                "document-upload/docs_internal_redress/{$year}/{$month}",
                $fileName,
                'public'
            );

            if ($business->docs_internal_redress) {
                Storage::disk('public')->delete($business->docs_internal_redress);
            }

            $business->docs_internal_redress = $internal_redress_path;
        }

        $business->is_bmbe = $request->input('is_bmbe');

        /* ============ BMBE DOC ============ */
        if ($request->hasFile('bmbe_doc')) {

            $file = $request->file('bmbe_doc');
            $originalName = $file->getClientOriginalName();
            $fileNameWithoutExt = pathinfo($originalName, PATHINFO_FILENAME);
            $extension = $file->getClientOriginalExtension();

            $timestamp = $now->format('YmdHis');
            $fileName = $timestamp . '_' . $fileNameWithoutExt . '.' . $extension;

            $uploadDir = "document-upload/bmbe_doc/{$year}/{$month}";

            if (!Storage::disk('public')->exists($uploadDir)) {
                Storage::disk('public')->makeDirectory($uploadDir);
            }

            $bmbe_doc_path = $file->storeAs($uploadDir, $fileName, 'public');

            if ($business->bmbe_doc) {
                Storage::disk('public')->delete($business->bmbe_doc);
            }

            $business->bmbe_doc = $bmbe_doc_path;
        }

        if ($request->input('is_bmbe') == 0) {
            $business->bmbe_doc = null;
            if ($business->bmbe_doc) {
                Storage::disk('public')->delete($business->bmbe_doc);
            }
        }
        $business->updated_by = Auth::id();
        $business->updated_at = Carbon::now();
        $business->status = 'UNDER EVALUATION';
        $business->admin_status = null;

        $business->save();
        $this->AdditionalPermitsstore($request, $id);
        // local
        // dispatch(new ReceivedMailJob($business));

        // DTI Email
        // $sendEmail = $this->business->apiSendReceivedEmail($business);

        // if (! $sendEmail->successful()) {
        //     return 'Email failed: '.$sendEmail->json();
        // }

        // Mandrill
        $sendEmail = $this->email->sendMail('received', [
            'business' => $business,
        ]);

        Log::info('Received email attempt', [
            'business_id' => $business->id ?? null,
            'business_name' => $business->business_name ?? null,
            'email_status' => isset($sendEmail['error']) ? 'failed' : 'success',
            'error' => $sendEmail['error'] ?? null,
        ]);

        if (isset($sendEmail['error'])) {
            return 'Email failed: '.$sendEmail['error'];
        }

        return redirect()->route('business.index')
            ->with('success', 'Business updated successfully!');
    }
    public function updateEditMail(Request $request, $id)
    {
        $business = Business::findOrFail($id);

        

        $rules = [
            'business_reg' => 'nullable|file|mimes:jpg,jpeg,png,pdf|max:10240',
            'bir_2303' => 'nullable|file|mimes:jpg,jpeg,png,pdf|max:10240',
            'internal_redress' => 'nullable|file|mimes:jpg,jpeg,png,pdf|max:10240',
        ];


        $now   = now();
        $year  = $now->format('Y');
        $month = $now->format('M');

        /* ================= BUSINESS REG ================= */
        if ($request->hasFile('business_reg')) {

            $file = $request->file('business_reg');
            $originalName = $file->getClientOriginalName();
            $fileNameWithoutExt = pathinfo($originalName, PATHINFO_FILENAME);
            $extension = $file->getClientOriginalExtension();

            $fileName = time() . '_' . $fileNameWithoutExt . '.' . $extension;

            $business_reg_path = $file->storeAs(
                "document-upload/business_registration/{$year}/{$month}",
                $fileName,
                'public'
            );

            if ($business->docs_business_reg) {
                Storage::disk('public')->delete($business->docs_business_reg);
            }

            $business->docs_business_reg = $business_reg_path;
        }

        /* ================= BIR 2303 ================= */
        if ($request->hasFile('bir_2303')) {

            $file = $request->file('bir_2303');
            $originalName = $file->getClientOriginalName();
            $fileNameWithoutExt = pathinfo($originalName, PATHINFO_FILENAME);
            $extension = $file->getClientOriginalExtension();

            $fileName = time() . '_' . $fileNameWithoutExt . '.' . $extension;

            $bir_2303_path = $file->storeAs(
                "document-upload/bir_2303/{$year}/{$month}",
                $fileName,
                'public'
            );

            if ($business->docs_bir_2303) {
                Storage::disk('public')->delete($business->docs_bir_2303);
            }

            $business->docs_bir_2303 = $bir_2303_path;
        }

        /* ================= INTERNAL REDRESS ================= */
        if ($request->hasFile('internal_redress')) {

            $file = $request->file('internal_redress');
            $originalName = $file->getClientOriginalName();
            $fileNameWithoutExt = pathinfo($originalName, PATHINFO_FILENAME);
            $extension = $file->getClientOriginalExtension();

            $fileName = time() . '_' . $fileNameWithoutExt . '.' . $extension;

            $internal_redress_path = $file->storeAs(
                "document-upload/docs_internal_redress/{$year}/{$month}",
                $fileName,
                'public'
            );

            if ($business->docs_internal_redress) {
                Storage::disk('public')->delete($business->docs_internal_redress);
            }

            $business->docs_internal_redress = $internal_redress_path;
        }

        $business->is_bmbe = $request->input('is_bmbe');

        /* ================= BMBE DOC ================= */
        if ($request->hasFile('bmbe_doc')) {

            if (!empty($business->bmbe_doc) && Storage::disk('public')->exists($business->bmbe_doc)) {
                Storage::disk('public')->delete($business->bmbe_doc);
            }

            $file = $request->file('bmbe_doc');
            $originalName = $file->getClientOriginalName();

            $fileNameWithoutExt = pathinfo($originalName, PATHINFO_FILENAME);
            $extension = $file->getClientOriginalExtension();

            $timestamp = $now->format('YmdHis');
            $fileName = $timestamp . '_' . $fileNameWithoutExt . '.' . $extension;

            $uploadDir = "document-upload/bmbe_doc/{$year}/{$month}";

            if (!Storage::disk('public')->exists($uploadDir)) {
                Storage::disk('public')->makeDirectory($uploadDir);
            }

            $bmbe_doc_path = $file->storeAs($uploadDir, $fileName, 'public');
            $business->bmbe_doc = $bmbe_doc_path;
        }

        $business->busn_category_id = $request->input('busn_category_id');

        /* ================= BUSINESS VALUATION ================= */
        if ($request->hasFile('busn_valuation_doc')) {

            if (!empty($business->busn_valuation_doc) && Storage::disk('public')->exists($business->busn_valuation_doc)) {
                Storage::disk('public')->delete($business->busn_valuation_doc);
            }

            $file = $request->file('busn_valuation_doc');
            $originalName = $file->getClientOriginalName();

            $fileNameWithoutExt = pathinfo($originalName, PATHINFO_FILENAME);
            $extension = $file->getClientOriginalExtension();

            $timestamp = $now->format('YmdHis');
            $fileName = $timestamp . '_' . $fileNameWithoutExt . '.' . $extension;

            $uploadDir = "document-upload/busn_valuation_doc/{$year}/{$month}";

            if (!Storage::disk('public')->exists($uploadDir)) {
                Storage::disk('public')->makeDirectory($uploadDir);
            }

            $busn_valuation_doc_path = $file->storeAs($uploadDir, $fileName, 'public');
            $business->busn_valuation_doc = $busn_valuation_doc_path;
        }

        /* ================= CONDITIONAL DELETE ================= */
        if ($request->input('is_bmbe') == 0) {

            if (!empty($business->bmbe_doc) && Storage::disk('public')->exists($business->bmbe_doc)) {
                Storage::disk('public')->delete($business->bmbe_doc);
            }
            $business->bmbe_doc = null;

        } else {

            if (!empty($business->busn_valuation_doc) && Storage::disk('public')->exists($business->busn_valuation_doc)) {
                Storage::disk('public')->delete($business->busn_valuation_doc);
            }

            $business->busn_valuation_doc = null;
            $business->busn_category_id = null;
        }

        
        $business->updated_by = Auth::id();
        $business->updated_at = Carbon::now();
        $business->admin_status = null;
        $business->app_status_id = null;
        $business->save();
        $this->AdditionalPermitsstore($request, $id);
        DB::table('user_logs')->updateOrInsert(
            [
                'busn_id'   => $business->id,
                'action_id' => 3,
                'message'   => Auth::user()->name . ' updated the returned application with Sec-No. '
                                . $business->trustmark_id . ' on documents section dated '
                                . now()->format('Y-m-d H:i:s') . '.',
                'created_by'=> Auth::id(),
            ],
            [
                'action_name'      => 'updated',
                'public_ip_address'=> $request->ip(),
                'status'           => $business->status,
                'remarks'          => $business->admin_remarks,
                'longitude'        => $request->input('longitude'),
                'latitude'         => $request->input('latitude'),
                'created_by_name'  => Auth::user()->name,
                'created_date'     => now(),
            ]
        );
        $now = Carbon::now();

        //$trustmarkId = $now->format('ymd-His').substr((string) $now->micro, 0, 2);
        $business->update([
            'status' => 'UNDER EVALUATION',
            //'trustmark_id' => $trustmarkId,
            // 'date_issued' => $now,
            //'submit_date' => $now,
        ]);
        $businessType = TypeCorporation::where('id', $business->corporation_type)->first();
        DB::table('user_logs')->updateOrInsert(
            [
                'busn_id'   => $business->id,
                'action_id' => 16, // unique condition
                'message'          => Auth::user()->name . ' submitted the returned application with Sec-No ' 
                                  . $business->trustmark_id 
                                  . ' dated ' . now()->format('Y-m-d H:i:s') . '.',
                'created_by'       => Auth::id(),
            ],
            [
            'action_name'      => 'submitted',
            
            'public_ip_address'=> $request->ip(),
            'status'           => $business->status,
            'remarks'          => $business->admin_remarks,
            'longitude'        => $request->input('longitude'), 
            'latitude'         => $request->input('latitude'),  
            'created_by_name'  => Auth::user()->name,
            'created_date'     => now(),
        ]);
        Log::info('Business updated successfully', [
            'business_id' => $business->id,
            'status' => $business->status,
            'trustmark_id' => $business->trustmark_id,
            // 'date_issued' => $business->date_issued,
            'submit_date' => $business->submit_date,
        ]);

        // Mandrill
        /*$sendEmail = $this->email->sendMail('registration', [
            'business' => $business,
        ]);*/

        Log::info('Registration email attempt', [
            'business_id' => $business->id ?? null,
            'business_name' => $business->business_name ?? null,
            //'email_status' => isset($sendEmail['error']) ? 'failed' : 'success',
            //'error' => $sendEmail['error'] ?? null,
        ]);

        /*if (isset($sendEmail['error'])) {
            return 'Email failed: '.$sendEmail['error'];
        }*/

        return redirect()
        ->route('business.index')
        ->with('success', [
            'You have successfully registered.',
            //'Confirmation sent to your email.',
            'Please wait for approval.'
        ]);
    }

    public function updateEditOnly(Request $request, $id)
    {
        $business = Business::findOrFail($id);

        $validated = $request->validate([
            'admin_remarks' => 'nullable|string',
        ]);

        $business->admin_remarks = $request->input('admin_remarks'); 
        $business->updated_by    = Auth::id();
        $business->updated_at    = now();
        $business->admin_status  = null;

        $business->save();
        DB::table('user_logs')->updateOrInsert(
            [
                'busn_id'   => $business->id,
                'action_id' => 3, // unique condition
                'message'   => Auth::user()->name . ' updated the returned application with Sec-No.  ' 
                                . $business->trustmark_id .' on remarks section having description '
                                . ' dated ' . now()->format('Y-m-d H:i:s') . '.',
                'created_by'=> Auth::id(),
            ],
            [
            'action_name'      => 'updated',
            'public_ip_address'=> $request->ip(),
            'status'           => $business->status,
            'remarks'          => $business->admin_remarks,
            'longitude'        => $request->input('longitude'), 
            'latitude'         => $request->input('latitude'),  
            'created_by_name'  => Auth::user()->name,
            'created_date'     => now(),
        ]);
        return response()->json([
            'status'  => 'success',
            'message' => 'Business updated successfully!'
        ]);
    }

    public function updateEditOnly2(Request $request, $id)
    {
        $business = Business::findOrFail($id);

        // Decode JSON URLs array from the request
        $urlsJson = $request->input('url_platform_json', '[]');
        $urls = json_decode($urlsJson, true);

        // Validate URLs array and format
        if (! is_array($urls)) {
            return redirect()->back()
                ->withErrors(['url_platform_json' => 'Invalid URL data format.'])
                ->withInput();
        }

        // Validate each URL format
        foreach ($urls as $url) {
            if (! filter_var($url, FILTER_VALIDATE_URL)) {
                return redirect()->back()
                    ->withErrors(['url_platform_json' => "Invalid URL detected: $url"])
                    ->withInput();
            }
        }

        $rules = [
            'type_id' => 'required',
            'reg_num' => 'required|string',
            'tin_num' => ['required', 'regex:/^\d{3}-\d{3}-\d{3}-\d{5}$/'],
            'business_name' => 'required|string|max:130',
            'franchise' => 'nullable|string',
            'category' => 'required',
            'other_category' => 'nullable|string|max:150',
            'url_platform_json' => 'nullable|string',
            'first_name' => 'required|string',
            'middle_name' => 'nullable|string',
            'last_name' => 'required|string',
            'suffix' => 'nullable|string',
            'ctc_no' => 'required|numeric',
            'email' => 'required|email',
            'issued_id' => 'required',
            'expiration_date_visible' => ['nullable', 'date'],
            'req_upload' => 'nullable|file|mimes:jpg,jpeg,png,pdf|max:10240',
            'expired_date' => ['nullable', 'date'], // default: nullable
            
        ];

        // Fetch requirements to check expiration rule
        $requirements = RequirementReps::where('status', 'Active')->get();

        if ($request->issued_id) {
            $requirement = $requirements->firstWhere('id', $request->issued_id);

            if ($requirement && trim($requirement->with_expiration) === '1') {
                // Override expired_date validation to required + after or equal today
                $rules['expiration_date_visible'] = ['required', 'date', 'after_or_equal:'.now()->format('Y-m-d')];
            }
        }

        // Now validate with dynamically built rules
        $validated = $request->validate($rules);
        $fullName = trim(
            $request->input('first_name').' '.
                ($request->input('middle_name') ? $request->input('middle_name').' ' : '').
                $request->input('last_name').
                ($request->input('suffix') ? ', '.$request->input('suffix') : '')
        );
        // update/save fields
        $business->pic_name = $fullName;
        $business->first_name = $validated['first_name'];
        $business->middle_name = $validated['middle_name'];
        $business->last_name = $validated['last_name'];
        $business->suffix = $validated['suffix'];
        $business->corporation_type = $validated['type_id'];
        $business->reg_num = $validated['reg_num'];
        $business->tin = $validated['tin_num'];
        $business->business_name = $validated['business_name'];
        $business->franchise = $validated['franchise'];
        $business->category_id = $validated['category'];
        $business->category_other_description = $validated['other_category'];
        $business->url_platform = $urls;
        // $business->pic_ctc_no = $validated['ctc_no'];
        $business->pic_email = $validated['email'];
        $business->requirement_id = $validated['issued_id'];
        $business->requirement_expired = $validated['expiration_date_visible'];
        

        if ($request->hasFile('req_upload')) {
            if (!empty($business->requirement_upload) && Storage::disk('public')->exists($business->requirement_upload)) {
                Storage::disk('public')->delete($business->requirement_upload);
            }
        
            $file = $request->file('req_upload');
            $originalName = $file->getClientOriginalName();
            $fileNameWithoutExt = pathinfo($originalName, PATHINFO_FILENAME);
            $extension = $file->getClientOriginalExtension();
            $year  = Carbon::now()->format('Y');
            $month = Carbon::now()->format('M');
        
            $uploadDir = "document-upload/requirement_reps/{$year}/{$month}";
        
            if (!Storage::disk('public')->exists($uploadDir)) {
                Storage::disk('public')->makeDirectory($uploadDir);
            }
        
            $fileName = time() . '_' . $fileNameWithoutExt . '.' . $extension;
        
            $req_upload_path = $file->storeAs($uploadDir, $fileName, 'public');
        
            $business->requirement_upload = $req_upload_path;
        }

        
        $business->updated_by = Auth::id();
        $business->updated_at = Carbon::now();
        $business->admin_status = null;

        $business->save();
        foreach ($urls as $url) {
            $url = trim($url);
            if (empty($url)) continue;
            try {
                $parsedUrl = parse_url($url);
                $baseHost = strtolower($parsedUrl['host'] ?? '');
            } catch (\Exception $e) {
                $baseHost = '';
            }
    
            //  Check platform_url table
            $platform = DB::table('platform_url as a')
                ->select('a.platform_name', 'a.with_irm')
                ->where('a.is_active', 1)
                ->where(function ($q) use ($baseHost) {
                    $q->where('a.base_url', 'LIKE', "%{$baseHost}%");
                })
                ->first();
    
            $platformName = $platform->platform_name ?? '';
            $withIrm = isset($platform->with_irm) ? (int) $platform->with_irm : 0;
    
            //business_url Insert or update business_url record
            DB::table('business_url')->updateOrInsert(
                [
                    'busn_id' => $business->id,
                    'url' => $url,
                ],
                [
                    'tax_year' => date('Y'),
                    'platform_name' => $platformName,
                    'with_irm' => $withIrm,
                    'created_by' => Auth::id(),
                    'created_date' => now(),
                    'modified_by' => Auth::id(),
                    'modified_date' => now(),
                ]
            );
        }
        DB::table('user_logs')->updateOrInsert(
            [
                'busn_id'   => $business->id,
                'action_id' => 3,
                'message'   => Auth::user()->name . ' updated the returned application with Sec-No. '
                                . $business->trustmark_id . ' on business registration section dated '
                                . now()->format('Y-m-d H:i:s') . '.',
                'created_by'=> Auth::id(),
            ],
            [
                'action_name'      => 'updated',
                'public_ip_address'=> $request->ip(),
                'status'           => $business->status,
                'remarks'          => $business->admin_remarks,
                'longitude'        => $request->input('longitude'),
                'latitude'         => $request->input('latitude'),
                'created_by_name'  => Auth::user()->name,
                'created_date'     => now(),
            ]
        );
        return response()->json(['status' => 'success']);
    }
    public function updateEditOnly3(Request $request, $id)
    {
        $business = Business::findOrFail($id);

        

        $rules = [
            'region' => 'required',
            'province' => 'required',
            'municipality' => 'required',
            'barangay' => 'required',
            'address' => 'required|string',
           
        ];
        $validated = $request->validate($rules);
        $business->region_id = $validated['region'];
        $business->province_id = $validated['province'];
        $business->municipality_id = $validated['municipality'];
        $business->barangay_id = $validated['barangay'];
        $business->complete_address = $validated['address'];

       
        $business->updated_by = Auth::id();
        $business->updated_at = Carbon::now();
        $business->admin_status = null;

        $business->save();
        DB::table('user_logs')->updateOrInsert(
            [
                'busn_id'   => $business->id,
                'action_id' => 3,
                'message'   => Auth::user()->name . ' updated the returned application with Sec-No. '
                                . $business->trustmark_id . ' on business address section dated '
                                . now()->format('Y-m-d H:i:s') . '.',
                'created_by'=> Auth::id(),
            ],
            [
                'action_name'      => 'updated',
                'public_ip_address'=> $request->ip(),
                'status'           => $business->status,
                'remarks'          => $business->admin_remarks,
                'longitude'        => $request->input('longitude'),
                'latitude'         => $request->input('latitude'),
                'created_by_name'  => Auth::user()->name,
                'created_date'     => now(),
            ]
        );
        return response()->json(['status' => 'success']);
    }
    public function updateEditOnly4(Request $request, $id)
    {
        $business = Business::findOrFail($id);

        

        $rules = [
            'business_reg' => 'nullable|file|mimes:jpg,jpeg,png,pdf|max:10240',
            'bir_2303' => 'nullable|file|mimes:jpg,jpeg,png,pdf|max:10240',
            'internal_redress' => 'nullable|file|mimes:jpg,jpeg,png,pdf|max:10240',
        ];

        /* ================= BUSINESS REG ================= */
        if ($request->hasFile('business_reg')) {

            $file = $request->file('business_reg');
            $originalName = $file->getClientOriginalName();
            $fileNameWithoutExt = pathinfo($originalName, PATHINFO_FILENAME);
            $extension = $file->getClientOriginalExtension();
        
            $year  = Carbon::now()->format('Y');
            $month = Carbon::now()->format('M');
            $directory = "document-upload/business_registration/{$year}/{$month}";
        
            $fileName = time() . '_' . $fileNameWithoutExt . '.' . $extension;
            $business_reg_path = $file->storeAs($directory, $fileName, 'public');
        
            if (!empty($business->docs_business_reg)) {
                Storage::disk('public')->delete($business->docs_business_reg);
            }
        
            $business->docs_business_reg = $business_reg_path;
        }
        
        /* ================= BIR 2303 (already correct) ================= */
        if ($request->hasFile('bir_2303')) {
        
            $file = $request->file('bir_2303');
            $originalName = $file->getClientOriginalName();
            $fileNameWithoutExt = pathinfo($originalName, PATHINFO_FILENAME);
            $extension = $file->getClientOriginalExtension();
        
            $year  = Carbon::now()->format('Y');
            $month = Carbon::now()->format('M');
            $directory = "document-upload/bir_2303/{$year}/{$month}";
        
            $fileName = time() . '_' . $fileNameWithoutExt . '.' . $extension;
            $bir_2303_path = $file->storeAs($directory, $fileName, 'public');
        
            if (!empty($business->docs_bir_2303)) {
                Storage::disk('public')->delete($business->docs_bir_2303);
            }
        
            $business->docs_bir_2303 = $bir_2303_path;
        }
        
        /* ================= INTERNAL REDRESS ================= */
        if ($request->hasFile('internal_redress')) {
        
            $file = $request->file('internal_redress');
            $originalName = $file->getClientOriginalName();
            $fileNameWithoutExt = pathinfo($originalName, PATHINFO_FILENAME);
            $extension = $file->getClientOriginalExtension();
        
            $year  = Carbon::now()->format('Y');
            $month = Carbon::now()->format('M');
            $directory = "document-upload/docs_internal_redress/{$year}/{$month}";
        
            $fileName = time() . '_' . $fileNameWithoutExt . '.' . $extension;
            $internal_redress_path = $file->storeAs($directory, $fileName, 'public');
        
            if (!empty($business->docs_internal_redress)) {
                Storage::disk('public')->delete($business->docs_internal_redress);
            }
        
            $business->docs_internal_redress = $internal_redress_path;
        }
        
        $business->is_bmbe = $request->input('is_bmbe');
        
        /* ================= BMBE DOC ================= */
        if ($request->hasFile('bmbe_doc')) {
        
            if (!empty($business->bmbe_doc) && Storage::disk('public')->exists($business->bmbe_doc)) {
                Storage::disk('public')->delete($business->bmbe_doc);
            }
        
            $file = $request->file('bmbe_doc');
            $originalName = $file->getClientOriginalName();
            $fileNameWithoutExt = pathinfo($originalName, PATHINFO_FILENAME);
            $extension = $file->getClientOriginalExtension();
        
            $year  = Carbon::now()->format('Y');
            $month = Carbon::now()->format('M');
            $uploadDir = "document-upload/bmbe_doc/{$year}/{$month}";
        
            $timestamp = now()->format('YmdHis');
            $fileName = $timestamp . '_' . $fileNameWithoutExt . '.' . $extension;
        
            if (!Storage::disk('public')->exists($uploadDir)) {
                Storage::disk('public')->makeDirectory($uploadDir);
            }
        
            $bmbe_doc_path = $file->storeAs($uploadDir, $fileName, 'public');
            $business->bmbe_doc = $bmbe_doc_path;
        } else {
            $business->bmbe_doc = $business->bmbe_doc;
        }
        if ($request->input('is_bmbe') == 0) {
            if (!empty($business->bmbe_doc) && Storage::disk('public')->exists($business->bmbe_doc)) {
                Storage::disk('public')->delete($business->bmbe_doc);
            }
            $business->bmbe_doc = null;
        }
        
        $business->updated_by = Auth::id();
        $business->updated_at = Carbon::now();
        $business->admin_status = null;

        $business->save();
        $this->AdditionalPermitsstore($request, $id);
        DB::table('user_logs')->updateOrInsert(
            [
                'busn_id'   => $business->id,
                'action_id' => 3,
                'message'   => Auth::user()->name . ' updated the returned application with Sec-No. '
                                . $business->trustmark_id . ' on documents section dated '
                                . now()->format('Y-m-d H:i:s') . '.',
                'created_by'=> Auth::id(),
            ],
            [
                'action_name'      => 'updated',
                'public_ip_address'=> $request->ip(),
                'status'           => $business->status,
                'remarks'          => $business->admin_remarks,
                'longitude'        => $request->input('longitude'),
                'latitude'         => $request->input('latitude'),
                'created_by_name'  => Auth::user()->name,
                'created_date'     => now(),
            ]
        );

        return response()->json(['status' => 'success']);
    }

    
    public function internal_redress_template()
    {
        $document = DB::table('setting_documents')->first();
        // $filePath = storage_path('app/public/document-upload/internal_redress/SAMPLE.TRUSTMARK.INTERNAL REDRESS MECHANISM.250709.BID.docx');
        $fileRelativePath = str_replace('storage/', '', $document->path_url);
        $filePath = storage_path('app/public/'.$document->path_url);
        if (! file_exists($filePath)) {
            $filePath = public_path('storage/'.$fileRelativePath);
            if (! file_exists($filePath)) {
                abort(404, 'File not found on server');
            }
        }

        return response()->download($filePath);
    }

    // ----- ADMIN -----
    public function admin_update(Request $request, $id)
    {
        set_time_limit(240);

        $business = Business::findOrFail($id);
        $status = DB::table('application_status')->where('id', $request->input('status_id'))->first();
        $currentUserId = Auth::id();
        $isAdmin = DB::table('user_admins')
            ->where('user_id', $currentUserId)
            ->value('is_admin');
        $assignedEvaluator = DB::table('businesses')
            ->where('id', $id)
            ->value('evaluator_id');
        if (! $isAdmin && $assignedEvaluator != $currentUserId) {
            return back()->with('status_error', 'Only authorized evaluator were allowed to modify the details.');
        }

        $reason_data = DB::table('application_canned_messages')->where('id', $request->input('reason_id'))->first();
        $lat = $request->latitude;
        $long = $request->longitude;
        $remark = '';
        if (! empty($request->input('remark'))) {
            $remark = $request->input('remark');
        }
        if (! empty($request->input('reason_id'))) {
            $remark.' Reason: '.$reason_data->description;
        }

        if ($request->input('status_id') == '1') {
            $business->admin_remarks = $request->input('remark');
            $business->status = $status->name;
            $business->app_status_id = $request->input('status_id');
            $business->app_canned_id = $request->input('reason_id');
            $business->admin_updated_by = Auth::id();
            $business->admin_updated_at = Carbon::now();
            $business->admin_status = $status->name;
            $business->date_approved = date('Y-m-d H:i:s');
            // $business->date_returned = null;
            // $business->date_disapproved = null;

            $now = Carbon::now();

            // $business->date_issued = $now;
            // $business->expired_date = $now->copy()->addYear(); // Avoid modifying original $now

            // insert in table business_fee

            // Get all ApplicationFees with same app_code
            $applicationFees = ApplicationFees::where('app_code', $business->app_code)->get();
            foreach ($applicationFees as $app_fee) {
                if($app_fee->is_application_fee==1){
                    if ((int)$business->is_bmbe == 0) {
                        // For application bmbe fee
                        $arrCatFee = DB::table('application_fee_category')->select('amount')->where('application_fee_id', $app_fee->id)->where('busn_category_id', $business->busn_category_id)->get();
                        foreach ($arrCatFee as $catFee) {
                            if($catFee->amount > 0){
                                $business_fee = new BusinessFees;
                                $business_fee->tax_year = $business->tax_year;
                                $business_fee->busn_id = $id;
                                $business_fee->app_code = $business->app_code;
                                $business_fee->app_name = $app_fee->app_name;
                                $business_fee->fee_id = $app_fee->fee_id;
                                $business_fee->fee_name = $app_fee->fee_name;
                                $business_fee->amount = $catFee->amount;
                                $business_fee->category_id = $business->category_id;
                                $business_fee->created_by = Auth::id();
                                $business_fee->create_date = $now;
                                $business_fee->save();
                            }
                        }
                    }
                } else {
                    $business_fee = new BusinessFees;
                    $business_fee->tax_year = $business->tax_year;
                    $business_fee->busn_id = $id;
                    $business_fee->app_code = $business->app_code;
                    $business_fee->app_name = $app_fee->app_name;
                    $business_fee->fee_id = $app_fee->fee_id;
                    $business_fee->fee_name = $app_fee->fee_name;
                    $business_fee->amount = $app_fee->amount;
                    $business_fee->category_id = $business->category_id;
                    $business_fee->created_by = Auth::id();
                    $business_fee->create_date = $now;
                    $business_fee->save();
                }
            }

            // local
            // dispatch(new ApprovedMailJob($business));

            // DTI Email
            // $sendEmail = $this->business->apiSendApprovedEmail($business);
            // if (! $sendEmail->successful()) {
            //     return 'Email failed: '.$sendEmail->json();
            // }

            // Mandrill
            $sendEmail = $this->email->sendMail('adminApproved', [
                'business' => $business,
            ]);

            Log::info('Admin Approval email attempt', [
                'business_id' => $business->id ?? null,
                'business_name' => $business->business_name ?? null,
                'email_status' => isset($sendEmail['error']) ? 'failed' : 'success',
                'error' => $sendEmail['error'] ?? null,
            ]);

            $message = Auth::user()->name.' approved the application with Sec-No.'.$business->trustmark_id.' dated '.date('Y-m-d H:i:s');
            saveUserLogs($lat, $long, $business->id, 7, 'approved', $message, $status->name, $remark);
            if (isset($sendEmail['error'])) {
                return 'Email failed: '.$sendEmail['error'];
            }

        } else {
            $business->admin_remarks = $request->input('remark');
            $business->status = $status->name;
            $business->app_status_id = $request->input('status_id');
            $business->app_canned_id = $request->input('reason_id');
            $business->admin_updated_by = Auth::id();
            $business->admin_updated_at = Carbon::now();
            $business->admin_status = $status->name;

            $reason = DB::table('application_canned_messages')->where('id', $request->input('reason_id'))->first();
            $exists = DB::table('business_onhold')
            ->where('busn_id', $business->id)
            ->exists();

            if ($exists) {
                DB::table('business_onhold')
                    ->where('busn_id', $business->id)
                    ->update([
                        'is_active'     => 0,
                        'modified_by'   => Auth::id(),
                        'modified_date' => now(),
                    ]);
            }
            if ($request->input('status_id') == 2) {
                $business->date_returned = date('Y-m-d H:i:s');
                // $business->date_approved = null;
                // $business->date_disapproved = null;

                $paragraph = DB::table('application_canned_messages')->where('id', $request->input('reason_id'))->value('remarks');

                // DTI Email
                // $sendEmail = $this->business->apiSendReturnedEmail($business, $reason->description, $request->input('remark'), $paragraph);
                // if (! $sendEmail->successful()) {
                //     return 'Email failed: '.$sendEmail->json();
                // }

                // Mandrill
                $sendEmail = $this->email->sendMail('adminReturned', [
                    'business' => $business,
                    'reason' => $reason->description,
                    // 'remark' => $request->input('remark'),
                    'paragraph' => $paragraph,
                ]);

                Log::info('Registration email attempt', [
                    'business_id' => $business->id ?? null,
                    'business_name' => $business->business_name ?? null,
                    'email_status' => isset($sendEmail['error']) ? 'failed' : 'success',
                    'error' => $sendEmail['error'] ?? null,
                ]);
                $message = Auth::user()->name.' returned the application with Sec-No.'.$business->trustmark_id.' dated '.date('Y-m-d H:i:s');
                saveUserLogs($lat, $long, $business->id, 15, 'Returned', $message, $status->name, $remark);
                if (isset($sendEmail['error'])) {
                    return 'Email failed: '.$sendEmail['error'];
                }

            } elseif ($request->input('status_id') == 4) {
                $business->date_disapproved = date('Y-m-d H:i:s');
                // $business->date_approved = null;
                // $business->date_returned = null;

                // DTI Email
                // $sendEmail = $this->business->apiSendDisapprovedEmail($business, $reason->description);
                // if (! $sendEmail->successful()) {
                //     return 'Email failed: '.$sendEmail->json();
                // }

                // Mandrill
                $subject = 'Disapproved E-Commerce Philippine Trustmark Application – Reference No. '.$business->trustmark_id;

                $sendEmail = $this->email->sendMail('adminDisapproved', [
                    'business' => $business,
                    'subject' => $subject
                    // 'reason' => $reason->description,
                ]);
                $message = Auth::user()->name.' disapproved the application with Sec-No.'.$business->trustmark_id.' dated '.date('Y-m-d H:i:s');
                saveUserLogs($lat, $long, $business->id, 8, 'Disapproved', $message, $status->name, $remark);

                if (isset($sendEmail['error'])) {
                    return 'Email failed: '.$sendEmail['error'];
                }
            }
            elseif ($request->input('status_id') == 7) {
                DB::table('business_onhold')->updateOrInsert(
                    ['busn_id' => $business->id],       
                    [
                    'tax_year' => date('Y'),
                    'reason' => $request->input('reason_id'),
                    'remarks' => $request->input('remark'),
                    'is_active' => 1,
                    'created_by' => Auth::id(),
                    'created_date' => now(),
                    'modified_by' => Auth::id(),
                    'modified_date' => now()
                ]);
            }
        }
        $business->on_hold = 0;
        $business->save();
        if($request->input('status_id') == 7){
            DB::table('businesses')
            ->where('id', $business->id)
            ->update([
                'status'       => 'UNDER EVALUATION',
                'admin_status' => '',
                'on_hold'      => 1
            ]);
        }

       
        return redirect()->route('business.index')
            ->with('success', 'Business updated successfully!');
    }

    public function showQr($id)
    {
        $business = Business::findOrFail($id);

        return view('business.qr_content', compact('business'));
    }

    public function list_under_evaluation(Request $request)
    {
        // $businesses = Business::where('status', 'UNDER EVALUATION');
        // if (Auth::check() && Auth::user()->role == 1) {
        //     $businesses = $businesses->where('user_id', Auth::id());
        // }
        // // ✅ Apply submit_date filter before ->get()
        // if ($request->filled('submit_date')) {
        //     $businesses = $businesses->whereDate('submit_date', $request->submit_date);
        // }
        // $businesses = $businesses->orderBy('id', 'DESC')->get();
        $businesses = [];
        $allcounts = $this->getallsttausescout();

        $under_evaluations = $allcounts['under_evaluations'];
        $approves = $allcounts['approves'];
        $paid = $allcounts['paid'];
        $returns = $allcounts['returns'];
        $drafts = $allcounts['drafts'];
        $disapproves = $allcounts['disapproves'];
        $allApplicationCount = $allcounts['all'];
        $displayStartDate = date('Y-m-d');
        $displayEndDate = date('Y-m-d');
        $onhold = Business::where('status', 'UNDER EVALUATION')->where('on_hold', 1)->where('is_active', 1)->select('id');
        if (Auth::check() && Auth::user()->role == 1) {
            $onhold = $onhold->where('user_id', Auth::id())
                ->count();
        } else {
            $onhold = $onhold->count();
        }
        if (Auth::check() && Auth::user()->role == 1) {
            return view('business.list_under_evaluation', compact('businesses', 'under_evaluations',
                'approves',
                'paid',
                'returns',
                'drafts',
                'allApplicationCount',
                'disapproves',
                'displayStartDate',
                'displayEndDate','onhold'));
        } else {
            return view('business.list_under_evaluation_admin', compact(
                'businesses',
                'under_evaluations',
                'approves',
                'paid',
                'returns',
                'drafts',
                'allApplicationCount',
                'disapproves',
                'displayStartDate',
                'displayEndDate','onhold'
            ));
        }
    }
    public function list_on_hold(Request $request)
    {
        $businesses = [];
        $allcounts = $this->getallsttausescout();

        $under_evaluations = $allcounts['under_evaluations'];
        $approves = $allcounts['approves'];
        $paid = $allcounts['paid'];
        $returns = $allcounts['returns'];
        $drafts = $allcounts['drafts'];
        $disapproves = $allcounts['disapproves'];
        $allApplicationCount = $allcounts['all'];
        $displayStartDate = date('Y-m-d');
        $displayEndDate = date('Y-m-d');
        $onhold = Business::where('status', 'UNDER EVALUATION')->where('on_hold', 1)->where('is_active', 1)->select('id');
        if (Auth::check() && Auth::user()->role == 1) {
            $onhold = $onhold->where('user_id', Auth::id())
                ->count();
        } else {
            $onhold = $onhold->count();
        }
        if (Auth::check() && Auth::user()->role == 1) {
            return view('business.list_on_hold', compact('businesses', 'under_evaluations',
                'approves',
                'paid',
                'returns',
                'drafts',
                'allApplicationCount',
                'disapproves',
                'displayStartDate',
                'displayEndDate','onhold'));
        } else {
            return view('business.list_on_hold', compact(
                'businesses',
                'under_evaluations',
                'approves',
                'paid',
                'returns',
                'drafts',
                'allApplicationCount',
                'disapproves',
                'displayStartDate',
                'displayEndDate','onhold'
            ));
        }
    }
    public function businessapp(Request $request)
    {
        // $businesses = Business::where('status', 'UNDER EVALUATION');

        // if(Auth::check() && Auth::user()->role == 1) {
        //     $businesses = $businesses->where('user_id', Auth::id());
        // }

        // // ✅ Apply submit_date filter before ->get()
        // if ($request->filled('submit_date')) {
        //     $businesses = $businesses->whereDate('submit_date', $request->submit_date);
        // }

        // $businesses = $businesses->orderBy('id', 'DESC')->get();
        $businesses = [];
        $allcounts = $this->getallsttausescout();

        $under_evaluations = $allcounts['under_evaluations'];
        $approves = $allcounts['approves'];
        $paid = $allcounts['paid'];
        $returns = $allcounts['returns'];
        $drafts = $allcounts['drafts'];
        $disapproves = $allcounts['disapproves'];
        $allApplicationCount = $allcounts['all'];
        $displayStartDate = date('Y-m-d');
        $displayEndDate = date('Y-m-d');
        $onhold = Business::where('status', 'UNDER EVALUATION')->where('on_hold', 1)->where('is_active', 1)->select('id');
        if (Auth::check() && Auth::user()->role == 1) {
            $onhold = $onhold->where('user_id', Auth::id())
                ->count();
        } else {
            $onhold = $onhold->count();
        }
        if (Auth::check() && Auth::user()->role == 1) {
            return view('business.list_business_app', compact('businesses', 'under_evaluations',
                'approves',
                'paid',
                'returns',
                'drafts',
                'allApplicationCount',
                'disapproves',
                'displayStartDate',
                'displayEndDate',
                'onhold'));
        } else {
            return view('business.list_business_app_admin', compact(
                'businesses',
                'under_evaluations',
                'approves',
                'paid',
                'returns',
                'drafts',
                'allApplicationCount',
                'disapproves',
                'displayStartDate',
                'displayEndDate',
                'onhold'
            ));
        }
    }

    public function getlistunderevalution(Request $request)
    {
        $params = $_REQUEST;
        $q = $request->input('q');
        $fromdate = $request->input('fromdate');
        $todate = $request->input('todate');

        if (! isset($params['start']) || ! isset($params['length'])) {
            $params['start'] = '0';
            $params['length'] = '10';
        }

        $columns = [
            1 => 'trustmark_id',
            2 => 'business_name',
            3 => 'reg_num',
            4 => 'business_type',
            5 => 'tin',
            6 => 'representative',
            7 => 'date_submitted',
        ];

        $sql = DB::table('businesses as a')
            ->leftJoin('users as b', 'a.user_id', '=', 'b.id')
            ->select([
                DB::raw("NULLIF(a.id,'') as id"), // fixed here
                DB::raw("NULLIF(a.trustmark_id,'') as trustmark_id"),
                DB::raw("NULLIF(a.business_name,'') as business_name"),
                DB::raw("NULLIF(a.reg_num,'') as reg_num"),
                DB::raw("NULLIF(a.tin,'') as tin"),
                DB::raw("NULLIF(a.on_hold,'') as on_hold"),
                DB::raw("(CASE a.corporation_type
                            WHEN 1 THEN 'Sole Proprietorship'
                            WHEN 2 THEN 'Corporation/Partnership'
                            WHEN 4 THEN 'Cooperative'
                        END) as business_type"),
                DB::raw('b.name as representative'),
                DB::raw("DATE_FORMAT(a.submit_date, '%m/%d/%Y') as date_submitted"),
                DB::raw('DATEDIFF(CURRENT_DATE(), a.submit_date) as no_of_days'),
                DB::raw("NULLIF(a.admin_remarks,'') as remarks"),
                DB::raw("NULLIF(a.status,'') as status"),
                DB::raw("NULLIF(a.payment_id,'') as payment_id"),
                DB::raw("NULLIF(a.corporation_type,'') as corporation_type"),
            ])
            ->where('a.is_active', 1);
        $sql->where('a.status', 'UNDER EVALUATION');
            // ->whereRaw('IFNULL(a.evaluator_id,0)=0');
        if (! empty($q)) {
            $sql->where(function ($query) use ($q) {
                $query->where(DB::raw('LOWER(trustmark_id)'), 'like', '%'.strtolower($q).'%')
                    ->orWhere(DB::raw('LOWER(business_name)'), 'like', '%'.strtolower($q).'%')
                    ->orWhere(DB::raw('LOWER(tin)'), 'like', '%'.strtolower($q).'%')
                    ->orWhere(DB::raw('LOWER(b.name)'), 'like', '%'.strtolower($q).'%')
                    ->orWhere(DB::raw('LOWER(admin_remarks)'), 'like', '%'.strtolower($q).'%')
                    ->orWhere(DB::raw('LOWER(reg_num)'), 'like', '%'.strtolower($q).'%');
            });
        }
        if (! empty($fromdate) && isset($fromdate)) {
            $sql->whereDate('submit_date', '>=', trim($fromdate));
        }
        if (! empty($todate) && isset($todate)) {
            $sql->whereDate('submit_date', '<=', trim($todate));
        }

        if (Auth::check() && Auth::user()->role == 1) {
            $sql->where('user_id', Auth::id());
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

        // $data=$this->business->getList($request);
        // echo "<pre>"; print_r($data); exit;
        $arr = [];
        $i = '0';
        $sr_no = (int) $request->input('start') - 1;
        $sr_no = $sr_no > 0 ? $sr_no + 1 : 0;
        $role = Auth::user()->role;

        foreach ($data as $row) {
            $hashids = new Hashids(env('APP_KEY'), 10);
            $ids = $hashids->encode($row->id);
            $status = $row->status;
            $sr_no = $sr_no + 1;

            $actions = '<a href="'.route('business.view', $ids).'" 
                            data-bs-toggle="tooltip" data-bs-placement="bottom" 
                            title="View"><i class="custom-eye-icon fa fa-eye"></i></a>';

            $arr[$i]['srno'] = $sr_no;
            $arr[$i]['trustmark_id'] = $row->trustmark_id ?? 'N/A';
            $arr[$i]['business_name'] = $row->business_name;
            $arr[$i]['reg_num'] = $row->reg_num ?? 'N/A';
            $arr[$i]['tin'] = $row->tin ?? 'N/A';
            $arr[$i]['business_type'] = $row->business_type ?? 'N/A';
            $arr[$i]['representative'] = $row->representative ?? 'N/A';
            $arr[$i]['date_submitted'] = $row->date_submitted ?? 'N/A';
            $arr[$i]['no_of_days'] = $row->no_of_days ?? 'N/A';
            // $badgeClass = match ($status) {
            //     'UNDER EVALUATION' => 'badge badge-bg-evaluation p-2 px-3',
            //     '' => 'badge badge-bg-draft',
            // };
            // // $arr[$i]['status'] = '<button class=" '.$badgeClass.' ">'.$status.'<button>';
            // $arr[$i]['status'] = '<span class="'.$badgeClass.'">'.$status.'</span>';
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
            $arr[$i]['action'] = $actions;
            $i++;
        }

        $totalRecords = $data_cnt;
        $json_data = [
            'recordsTotal' => intval($totalRecords),
            'recordsFiltered' => intval($totalRecords),
            'data' => $arr,   // total data array
        ];
        echo json_encode($json_data);
    }
    public function getlistOnhold(Request $request)
    {
        $params = $_REQUEST;
        $q = $request->input('q');
        $fromdate = $request->input('fromdate');
        $todate = $request->input('todate');

        if (! isset($params['start']) || ! isset($params['length'])) {
            $params['start'] = '0';
            $params['length'] = '10';
        }

        $columns = [
            1 => 'trustmark_id',
            2 => 'business_name',
            3 => 'reg_num',
            4 => 'business_type',
            5 => 'tin',
            6 => 'representative',
            7 => 'date_submitted',
        ];

        $sql = DB::table('businesses as a')
            ->leftJoin('users as b', 'a.user_id', '=', 'b.id')
            ->select([
                DB::raw("NULLIF(a.id,'') as id"), // fixed here
                DB::raw("NULLIF(a.trustmark_id,'') as trustmark_id"),
                DB::raw("NULLIF(a.business_name,'') as business_name"),
                DB::raw("NULLIF(a.reg_num,'') as reg_num"),
                DB::raw("NULLIF(a.tin,'') as tin"),
                DB::raw("NULLIF(a.on_hold,'') as on_hold"),
                DB::raw("(CASE a.corporation_type
                            WHEN 1 THEN 'Sole Proprietorship'
                            WHEN 2 THEN 'Corporation/Partnership'
                            WHEN 4 THEN 'Cooperative'
                        END) as business_type"),
                DB::raw('b.name as representative'),
                DB::raw("DATE_FORMAT(a.submit_date, '%m/%d/%Y') as date_submitted"),
                DB::raw('DATEDIFF(CURRENT_DATE(), a.submit_date) as no_of_days'),
                DB::raw("NULLIF(a.admin_remarks,'') as remarks"),
                DB::raw("NULLIF(a.status,'') as status"),
                DB::raw("NULLIF(a.payment_id,'') as payment_id"),
                DB::raw("NULLIF(a.corporation_type,'') as corporation_type"),
            ])
            ->where('a.is_active', 1)->where('a.on_hold', 1);
        $sql->where('a.status', 'UNDER EVALUATION');
            // ->whereRaw('IFNULL(a.evaluator_id,0)=0');
        if (! empty($q)) {
            $sql->where(function ($query) use ($q) {
                $query->where(DB::raw('LOWER(trustmark_id)'), 'like', '%'.strtolower($q).'%')
                    ->orWhere(DB::raw('LOWER(business_name)'), 'like', '%'.strtolower($q).'%')
                    ->orWhere(DB::raw('LOWER(tin)'), 'like', '%'.strtolower($q).'%')
                    ->orWhere(DB::raw('LOWER(b.name)'), 'like', '%'.strtolower($q).'%')
                    ->orWhere(DB::raw('LOWER(admin_remarks)'), 'like', '%'.strtolower($q).'%')
                    ->orWhere(DB::raw('LOWER(reg_num)'), 'like', '%'.strtolower($q).'%');
            });
        }
        if (! empty($fromdate) && isset($fromdate)) {
            $sql->whereDate('submit_date', '>=', trim($fromdate));
        }
        if (! empty($todate) && isset($todate)) {
            $sql->whereDate('submit_date', '<=', trim($todate));
        }

        if (Auth::check() && Auth::user()->role == 1) {
            $sql->where('user_id', Auth::id());
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

        // $data=$this->business->getList($request);
        // echo "<pre>"; print_r($data); exit;
        $arr = [];
        $i = '0';
        $sr_no = (int) $request->input('start') - 1;
        $sr_no = $sr_no > 0 ? $sr_no + 1 : 0;
        $role = Auth::user()->role;

        foreach ($data as $row) {
            $hashids = new Hashids(env('APP_KEY'), 10);
            $ids = $hashids->encode($row->id);
            $status = $row->status;
            $sr_no = $sr_no + 1;

            $actions = '<a href="'.route('business.view', $ids).'" 
                            data-bs-toggle="tooltip" data-bs-placement="bottom" 
                            title="View"><i class="custom-eye-icon fa fa-eye"></i></a>';

            $arr[$i]['srno'] = $sr_no;
            $arr[$i]['trustmark_id'] = $row->trustmark_id ?? 'N/A';
            $arr[$i]['business_name'] = $row->business_name;
            $arr[$i]['reg_num'] = $row->reg_num ?? 'N/A';
            $arr[$i]['tin'] = $row->tin ?? 'N/A';
            $arr[$i]['business_type'] = $row->business_type ?? 'N/A';
            $arr[$i]['representative'] = $row->representative ?? 'N/A';
            $arr[$i]['date_submitted'] = $row->date_submitted ?? 'N/A';
            $arr[$i]['no_of_days'] = $row->no_of_days ?? 'N/A';
            // $badgeClass = match ($status) {
            //     'UNDER EVALUATION' => 'badge badge-bg-evaluation p-2 px-3',
            //     '' => 'badge badge-bg-draft',
            // };
            // // $arr[$i]['status'] = '<button class=" '.$badgeClass.' ">'.$status.'<button>';
            // $arr[$i]['status'] = '<span class="'.$badgeClass.'">'.$status.'</span>';
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
            $arr[$i]['action'] = $actions;
            $i++;
        }

        $totalRecords = $data_cnt;
        $json_data = [
            'recordsTotal' => intval($totalRecords),
            'recordsFiltered' => intval($totalRecords),
            'data' => $arr,   // total data array
        ];
        echo json_encode($json_data);
    }
    public function list_draft(Request $request)
    {
        // $businesses = Business::where('status', 'DRAFT');

        // if (Auth::check() && Auth::user()->role == 1) {
        //     $businesses = $businesses->where('user_id', Auth::id());
        // }

        // // ✅ Apply submit_date filter before ->get()
        // if ($request->filled('submit_date')) {
        //     $businesses = $businesses->whereDate('submit_date', $request->submit_date);
        // }

        // $businesses = $businesses->orderBy('id', 'DESC')->get();
        $businesses = [];
        $allcounts = $this->getallsttausescout();

        $under_evaluations = $allcounts['under_evaluations'];
        $approves = $allcounts['approves'];
        $paid = $allcounts['paid'];
        $returns = $allcounts['returns'];
        $drafts = $allcounts['drafts'];
        $disapproves = $allcounts['disapproves'];
        $allApplicationCount = $allcounts['all'];
        $displayStartDate = date('Y-m-d');
        $displayEndDate = date('Y-m-d');
        $onhold = Business::where('status', 'UNDER EVALUATION')->where('on_hold', 1)->where('is_active', 1)->select('id');
        if (Auth::check() && Auth::user()->role == 1) {
            $onhold = $onhold->where('user_id', Auth::id())
                ->count();
        } else {
            $onhold = $onhold->count();
        }
        if (Auth::check() && Auth::user()->role == 1) {
            return view('business.list_draft', compact('businesses', 'under_evaluations',
                'approves',
                'paid',
                'returns',
                'drafts',
                'allApplicationCount',
                'disapproves',
                'displayStartDate',
                'displayEndDate',
                'onhold'));
        } else {
            return view('business.list_draft_admin', compact(
                'businesses',
                'under_evaluations',
                'approves',
                'paid',
                'returns',
                'drafts',
                'allApplicationCount',
                'disapproves',
                'displayStartDate',
                'displayEndDate',
                'onhold'
            ));
        }
    }

    public function getlistdraft(Request $request)
    {
        $params = $_REQUEST;
        $q = $request->input('q');
        $fromdate = $request->input('fromdate');
        $todate = $request->input('todate');

        if (! isset($params['start']) || ! isset($params['length'])) {
            $params['start'] = '0';
            $params['length'] = '10';
        }

        $columns = [
            1 => 'trustmark_id',
            2 => 'business_name',
            3 => 'reg_num',
            4 => 'business_type',
            5 => 'tin',
            6 => 'representative',
            7 => 'date_submitted',
        ];

        $sql = DB::table('businesses as a')
        ->leftJoin('users as b', 'a.user_id', '=', 'b.id')
        ->select([
            DB::raw("NULLIF(a.id,'') as id"), // fixed here
            DB::raw("NULLIF(a.trustmark_id,'') as trustmark_id"),
            DB::raw("NULLIF(a.business_name,'') as business_name"),
            DB::raw("NULLIF(a.reg_num,'') as reg_num"),
            DB::raw("NULLIF(a.tin,'') as tin"),
            DB::raw("(CASE a.corporation_type
                        WHEN 1 THEN 'Sole Proprietorship'
                        WHEN 2 THEN 'Corporation/Partnership'
                        WHEN 4 THEN 'Cooperative'
                    END) as business_type"),
            DB::raw('b.name as representative'),
                DB::raw("DATE_FORMAT(a.created_at, '%m/%d/%Y') as date_generated"),
                DB::raw("NULLIF(a.status,'') as status"),
                DB::raw("NULLIF(a.payment_id,'') as payment_id"),
                DB::raw("NULLIF(a.corporation_type,'') as corporation_type"),
            ])
            ->where('a.is_active', 1);

        $sql->where('a.status', 'DRAFT')
            ->whereRaw('IFNULL(a.evaluator_id,0)=0');

        if (! empty($q)) {
            $sql->where(function ($query) use ($q) {
                $query->where(DB::raw('LOWER(trustmark_id)'), 'like', '%'.strtolower($q).'%')
                    ->orWhere(DB::raw('LOWER(business_name)'), 'like', '%'.strtolower($q).'%')
                    ->orWhere(DB::raw('LOWER(tin)'), 'like', '%'.strtolower($q).'%')
                    ->orWhere(DB::raw('LOWER(admin_remarks)'), 'like', '%'.strtolower($q).'%')
                    ->orWhere(DB::raw('LOWER(reg_num)'), 'like', '%'.strtolower($q).'%');
            });
        }
        if (! empty($fromdate) && isset($fromdate)) {
            $sql->whereDate('submit_date', '>=', trim($fromdate));
        }
        if (! empty($todate) && isset($todate)) {
            $sql->whereDate('submit_date', '<=', trim($todate));
        }

        if (Auth::check() && Auth::user()->role == 1) {
            $sql->where('user_id', Auth::id());
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

        // $data=$this->business->getList($request);
        // echo "<pre>"; print_r($data); exit;
        $arr = [];
        $i = '0';
        $sr_no = (int) $request->input('start') - 1;
        $sr_no = $sr_no > 0 ? $sr_no + 1 : 0;
        $role = Auth::user()->role;

        foreach ($data as $row) {
            $status = $row->status;
            $sr_no = $sr_no + 1;
            $hashids = new Hashids(env('APP_KEY'), 10);
            $ids = $hashids->encode($row->id);
            $actions = '<a href="'.route('business.view', $ids).'" 
                            data-bs-toggle="tooltip" data-bs-placement="bottom" 
                            title="View"><i class="custom-eye-icon fa fa-eye"></i></a>';

            $arr[$i]['srno'] = $sr_no;
            $arr[$i]['trustmark_id'] = $row->trustmark_id ?? 'N/A';
            $arr[$i]['business_name'] = $row->business_name;
            $arr[$i]['reg_num'] = $row->reg_num ?? 'N/A';
            $arr[$i]['tin'] = $row->tin ?? 'N/A';
            $arr[$i]['representative'] = $row->representative ?? '';
            $arr[$i]['business_type'] = $row->business_type ?? 'N/A';
            $arr[$i]['date_generated'] = $row->date_generated ?? 'N/A';
            $badgeClass = match ($status) {
                'DRAFT' => 'badge badge-bg-draft p-2 px-3',
                '' => 'badge badge-bg-draft',
            };
            // $arr[$i]['status'] = '<button class=" '.$badgeClass.' ">'.$status.'<button>';
            $arr[$i]['status'] = '<span class="'.$badgeClass.'">'.$status.'</span>';
            $arr[$i]['action'] = $actions;
            $i++;
        }

        $totalRecords = $data_cnt;
        $json_data = [
            'recordsTotal' => intval($totalRecords),
            'recordsFiltered' => intval($totalRecords),
            'data' => $arr,   // total data array
        ];
        echo json_encode($json_data);
    }

    public function getallsttausescout()
    {
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

        return [
            'under_evaluations' => $under_evaluations,
            'approves' => $approves,
            'paid' => $paid,
            'returns' => $returns,
            'drafts' => $drafts,
            'disapproves' => $disapproves,
            'all' => $allApplicationCount,
        ];

    }

    public function downloadQrPng($id)
    {
        $business = \App\Models\Business::findOrFail($id);

        return view('business.qr_template', compact('business'));
    }

    public function downloadQrPngOLd($id)
    {
        $business = Business::findOrFail($id);

        // Render the Blade view as HTML
        $html = view('business.qr_template', compact('business'))->render();

        $now = now();
        $timestamp = $now->format('ymdHis').substr((string) $now->micro, 0, 2);
        $fileName = 'TMKQR_'.$timestamp.'.png';
        /*$tempPath = storage_path('app/public/tmp/'.$fileName);

        // Ensure the tmp directory exists
        if (! file_exists(dirname($tempPath))) {
            mkdir(dirname($tempPath), 0755, true);
        }*/

        $tempPath = public_path('storage/tmp/'.$fileName);

        // Ensure the tmp directory exists
        if (! file_exists(dirname($tempPath))) {
            mkdir(dirname($tempPath), 0755, true);
        }



        // Generate PNG using Browsershot
        Browsershot::html($html)
            ->windowSize(500, 600)
            ->setChromePath('/usr/bin/google-chrome')
            ->setOption('args', ['--no-sandbox'])
            ->save($tempPath);

        // Return the PNG as a download and delete after response
        return response()->download($tempPath, $fileName)->deleteFileAfterSend(true);
    }

    public function downloadCertificate($id)
    {
        $business = Business::findOrFail($id);

        if (! $business->certificate) {
            return abort(404, 'Certificate not found');
        }

        $fileRelativePath = str_replace('storage/', '', $business->certificate);
        $filePath = storage_path('app/public/'.$fileRelativePath);

        if (! file_exists($filePath)) {
            $filePath = public_path('storage/'.$fileRelativePath);
            if (! file_exists($filePath)) {
                abort(404, 'File not found on server');
            }
        }

        // Force refresh
        clearstatcache();

        $now = now();
        $timestamp = $now->format('ymdHis').substr((string) $now->micro, 0, 2);
        $fileName = 'Trustmark_'.$business->trustmark_id.'.pdf';

        return response()->download($filePath, $fileName, [
            'Content-Type' => 'application/pdf',
            'Cache-Control' => 'no-store, no-cache, must-revalidate',
            'Pragma' => 'no-cache',
            'Expires' => '0',
        ]);
    }

    public function list_approved(Request $request)
    {
        // $businesses = Business::where('status', 'APPROVED');

        // if (Auth::check() && Auth::user()->role == 1) {
        //     $businesses = $businesses->where('user_id', Auth::id());
        // }

        // if ($request->filled('submit_date')) {
        //     $businesses = $businesses->whereDate('submit_date', $request->submit_date);
        // }
        // $businesses = $businesses->orderBy('id', 'DESC')->get();
        $businesses = [];
        $allcounts = $this->getallsttausescout();
        $under_evaluations = $allcounts['under_evaluations'];
        $approves = $allcounts['approves'];
        $paid = $allcounts['paid'];
        $returns = $allcounts['returns'];
        $drafts = $allcounts['drafts'];
        $disapproves = $allcounts['disapproves'];
        $allApplicationCount = $allcounts['all'];
        $displayStartDate = date('Y-m-d');
        $displayEndDate = date('Y-m-d');
        $onhold = Business::where('status', 'UNDER EVALUATION')->where('on_hold', 1)->where('is_active', 1)->select('id');
        if (Auth::check() && Auth::user()->role == 1) {
            $onhold = $onhold->where('user_id', Auth::id())
                ->count();
        } else {
            $onhold = $onhold->count();
        }
        if (Auth::check() && Auth::user()->role == 1) {
            return view('business.list_approved', compact('businesses', 'under_evaluations',
                'approves',
                'paid',
                'returns',
                'drafts',
                'allApplicationCount',
                'disapproves',
                'displayStartDate',
                'displayEndDate',
                'onhold'));
        } else {
            return view('business.list_approved_admin', compact(
                'businesses',
                'under_evaluations',
                'approves',
                'paid',
                'returns',
                'drafts',
                'allApplicationCount',
                'disapproves',
                'displayStartDate',
                'displayEndDate',
                'onhold'
            ));
        }
    }

    public function getlistapproved(Request $request)
    {
        $params = $_REQUEST;
        $q = $request->input('q');
        $fromdate = $request->input('fromdate');
        $todate = $request->input('todate');

        if (! isset($params['start']) || ! isset($params['length'])) {
            $params['start'] = '0';
            $params['length'] = '10';
        }

        $columns = [
            1 => 'trustmark_id',
            2 => 'business_name',
            3 => 'reg_num',
            4 => 'business_type',
            5 => 'tin',
            6 => 'representative',
            7 => 'date_submitted',
        ];

        $sql = DB::table('businesses as a')
            ->leftJoin('users as b', 'a.user_id', '=', 'b.id')
            ->select([
                DB::raw("NULLIF(a.id,'') as id"), // fixed here
                DB::raw("NULLIF(a.trustmark_id,'') as trustmark_id"),
                DB::raw("NULLIF(a.business_name,'') as business_name"),
                DB::raw("NULLIF(a.reg_num,'') as reg_num"),
                DB::raw("NULLIF(a.tin,'') as tin"),
                DB::raw("(CASE a.corporation_type
                            WHEN 1 THEN 'Sole Proprietorship'
                            WHEN 2 THEN 'Corporation/Partnership'
                            WHEN 4 THEN 'Cooperative'
                        END) as business_type"),
                DB::raw('b.name as representative'),
                DB::raw("DATE_FORMAT(a.date_approved, '%m/%d/%Y') as date_approved"),
                DB::raw('DATEDIFF(CURRENT_DATE(), a.date_approved) as no_of_days'),
                DB::raw("NULLIF(a.admin_remarks,'') as remarks"),
                DB::raw("NULLIF(a.status,'') as status"),
                DB::raw("NULLIF(a.payment_id,'') as payment_id"),
                DB::raw("NULLIF(a.corporation_type,'') as corporation_type"),
            ])
            ->where('a.is_active', 1);
        $sql->where('a.status', 'APPROVED')->where('payment_id', '=', null)->where('evaluator_id', '>', '0');

        if (! empty($q)) {
            $sql->where(function ($query) use ($q) {
                $query->where(DB::raw('LOWER(trustmark_id)'), 'like', '%'.strtolower($q).'%')
                    ->orWhere(DB::raw('LOWER(business_name)'), 'like', '%'.strtolower($q).'%')
                    ->orWhere(DB::raw('LOWER(tin)'), 'like', '%'.strtolower($q).'%')
                    ->orWhere(DB::raw('LOWER(b.name)'), 'like', '%'.strtolower($q).'%')
                    ->orWhere(DB::raw('LOWER(admin_remarks)'), 'like', '%'.strtolower($q).'%')
                    ->orWhere(DB::raw('LOWER(reg_num)'), 'like', '%'.strtolower($q).'%');
            });
        }
        if (! empty($fromdate) && isset($fromdate)) {
            $sql->whereDate('date_approved', '>=', trim($fromdate));
        }
        if (! empty($todate) && isset($todate)) {
            $sql->whereDate('date_approved', '<=', trim($todate));
        }

        if (Auth::check() && Auth::user()->role == 1) {
            $sql->where('user_id', Auth::id());
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

        // $data=$this->business->getList($request);
        // echo "<pre>"; print_r($data); exit;
        $arr = [];
        $i = '0';
        $sr_no = (int) $request->input('start') - 1;
        $sr_no = $sr_no > 0 ? $sr_no + 1 : 0;
        $role = Auth::user()->role;

        foreach ($data as $row) {
            $status = $row->status;
            $sr_no = $sr_no + 1;
            $hashids = new Hashids(env('APP_KEY'), 10);
            $ids = $hashids->encode($row->id);
            $actions = '<a href="'.route('business.view', $ids).'" 
                            data-bs-toggle="tooltip" data-bs-placement="bottom" 
                            title="View"><i class="custom-eye-icon fa fa-eye"></i></a>';

            $arr[$i]['srno'] = $sr_no;
            $arr[$i]['trustmark_id'] = $row->trustmark_id ?? '';
            $arr[$i]['business_name'] = $row->business_name;
            $arr[$i]['reg_num'] = $row->reg_num ?? '';
            $arr[$i]['tin'] = $row->tin ?? '';
            $arr[$i]['business_type'] = $row->business_type ?? '';
            $arr[$i]['representative'] = $row->representative ?? '';
            $arr[$i]['date_approved'] = $row->date_approved ?? '';
            $arr[$i]['no_of_days'] = $row->no_of_days ?? '';
            $paymentStatus = $row->payment_id === null ? 'Unpaid' : 'Paid';
            $paymentBadgeClass = match ($paymentStatus) {
                'Paid' => 'badge-bg-approve', // green-like color
                'Unpaid' => 'badge-bg-returned', // red-like color
                'default' => 'badge-bg-draft', // fallback color
            };

            $arr[$i]['paymnetsttaus'] = '<span
                                                class="badge '.$paymentBadgeClass.' px-2 py-1 small text-center d-inline-block"
                                                style="min-width: 80px;">
                                                '.$paymentStatus.'
                                            </span>';

            $badgeClass = match ($status) {
                'APPROVED' => 'badge badge-bg-approve p-2 px-3',
                '' => 'badge badge-bg-draft',
            };
            // $arr[$i]['status'] = '<button class=" '.$badgeClass.' ">'.$status.'<button>';
            $arr[$i]['status'] = '<span class="'.$badgeClass.'">'.$status.'</span>';
            $arr[$i]['action'] = $actions;
            $i++;
        }

        $totalRecords = $data_cnt;
        $json_data = [
            'recordsTotal' => intval($totalRecords),
            'recordsFiltered' => intval($totalRecords),
            'data' => $arr,   // total data array
        ];
        echo json_encode($json_data);
    }

    public function list_paid(Request $request)
    {
        // $businesses = Business::where('status', 'APPROVED')->where('payment_id','>','0')->where('evaluator_id','>','0');

        // if (Auth::check() && Auth::user()->role == 1) {
        //     $businesses = $businesses->where('user_id', Auth::id());
        // }

        // if ($request->filled('submit_date')) {
        //     $businesses = $businesses->whereDate('submit_date', $request->submit_date);
        // }
        // $businesses = $businesses->orderBy('id', 'DESC')->get();
        $businesses = [];
        $allcounts = $this->getallsttausescout();

        $under_evaluations = $allcounts['under_evaluations'];
        $approves = $allcounts['approves'];
        $paid = $allcounts['paid'];
        $returns = $allcounts['returns'];
        $drafts = $allcounts['drafts'];
        $disapproves = $allcounts['disapproves'];
        $allApplicationCount = $allcounts['all'];
        $displayStartDate = date('Y-m-d');
        $displayEndDate = date('Y-m-d');
        $onhold = Business::where('status', 'UNDER EVALUATION')->where('on_hold', 1)->where('is_active', 1)->select('id');
        if (Auth::check() && Auth::user()->role == 1) {
            $onhold = $onhold->where('user_id', Auth::id())
                ->count();
        } else {
            $onhold = $onhold->count();
        }
        if (Auth::check() && Auth::user()->role == 1) {
            return view('business.list_paid', compact('businesses', 'under_evaluations',
                'approves',
                'paid',
                'returns',
                'drafts',
                'allApplicationCount',
                'disapproves',
                'displayStartDate',
                'displayEndDate',
                'onhold'));
        } else {
            return view('business.list_paid_admin', compact(
                'businesses',
                'under_evaluations',
                'approves',
                'paid',
                'returns',
                'drafts',
                'allApplicationCount',
                'disapproves',
                'displayStartDate',
                'displayEndDate',
                'onhold'
            ));
        }
    }

    public function getlistpaid(Request $request)
    {
        $params = $_REQUEST;
        $q = $request->input('q');
        $fromdate = $request->input('fromdate');
        $todate = $request->input('todate');

        if (! isset($params['start']) || ! isset($params['length'])) {
            $params['start'] = '0';
            $params['length'] = '10';
        }

        $columns = [
            1 => 'trustmark_id',
            2 => 'business_name',
            3 => 'reg_num',
            4 => 'business_type',
            5 => 'tin',
            6 => 'representative',
            7 => 'date_submitted',
        ];

        $sql = DB::table('businesses as a')
            ->leftJoin('users as b', 'a.user_id', '=', 'b.id')
            ->select([
                DB::raw("NULLIF(a.id,'') as id"), // fixed here
                DB::raw("NULLIF(a.trustmark_id,'') as trustmark_id"),
                DB::raw("NULLIF(a.business_name,'') as business_name"),
                DB::raw("NULLIF(a.reg_num,'') as reg_num"),
                DB::raw("NULLIF(a.tin,'') as tin"),
                DB::raw("(CASE a.corporation_type
                            WHEN 1 THEN 'Sole Proprietorship'
                            WHEN 2 THEN 'Corporation/Partnership'
                            WHEN 4 THEN 'Cooperative'
                        END) as business_type"),
                DB::raw('b.name as representative'),
                DB::raw("DATE_FORMAT(a.date_issued, '%m/%d/%Y') as date_issued"),
                DB::raw("NULLIF(a.status,'') as status"),
                DB::raw("NULLIF(a.payment_id,'') as payment_id"),
                DB::raw("NULLIF(a.corporation_type,'') as corporation_type"),
            ])
            ->where('a.is_active', 1);
        $sql->where('a.status', 'APPROVED')->where('payment_id', '>', 0)->where('evaluator_id', '>', 0);

        if (! empty($q)) {
            $sql->where(function ($query) use ($q) {
                $query->where(DB::raw('LOWER(trustmark_id)'), 'like', '%'.strtolower($q).'%')
                    ->orWhere(DB::raw('LOWER(business_name)'), 'like', '%'.strtolower($q).'%')
                    ->orWhere(DB::raw('LOWER(tin)'), 'like', '%'.strtolower($q).'%')
                    ->orWhere(DB::raw('LOWER(b.name)'), 'like', '%'.strtolower($q).'%')
                    ->orWhere(DB::raw('LOWER(admin_remarks)'), 'like', '%'.strtolower($q).'%')
                    ->orWhere(DB::raw('LOWER(reg_num)'), 'like', '%'.strtolower($q).'%');
            });
        }
        if (! empty($fromdate) && isset($fromdate)) {
            $sql->whereDate('date_issued', '>=', trim($fromdate));
        }
        if (! empty($todate) && isset($todate)) {
            $sql->whereDate('date_issued', '<=', trim($todate));
        }

        if (Auth::check() && Auth::user()->role == 1) {
            $sql->where('user_id', Auth::id());
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

        // $data=$this->business->getList($request);
        // echo "<pre>"; print_r($data); exit;
        $arr = [];
        $i = '0';
        $sr_no = (int) $request->input('start') - 1;
        $sr_no = $sr_no > 0 ? $sr_no + 1 : 0;
        $role = Auth::user()->role;

        foreach ($data as $row) {
            $status = 'PAID';
            $sr_no = $sr_no + 1;
            $hashids = new Hashids(env('APP_KEY'), 10);
            $ids = $hashids->encode($row->id);
            $actions = '<a href="'.route('business.view', $ids).'" 
                            data-bs-toggle="tooltip" data-bs-placement="bottom" 
                            title="View"><i class="custom-eye-icon fa fa-eye"></i></a>';

            $arr[$i]['srno'] = $sr_no;
            $arr[$i]['trustmark_id'] = $row->trustmark_id ?? '';
            $arr[$i]['business_name'] = $row->business_name;
            $arr[$i]['reg_num'] = $row->reg_num ?? '';
            $arr[$i]['tin'] = $row->tin ?? '';
            $arr[$i]['business_type'] = $row->business_type ?? '';
            $arr[$i]['representative'] = $row->representative ?? '';
            $arr[$i]['date_issued'] = $row->date_issued ?? '';
            $paymentStatus = $row->payment_id === null ? 'Unpaid' : 'Paid';
            $paymentBadgeClass = match ($paymentStatus) {
                'Paid' => 'badge-bg-approve', // green-like color
                'Unpaid' => 'badge-bg-returned', // red-like color
                'default' => 'badge-bg-draft', // fallback color
            };

            $arr[$i]['paymnetsttaus'] = '<span
                                                class="badge '.$paymentBadgeClass.' px-2 py-1 small text-center d-inline-block"
                                                style="min-width: 80px;">
                                                '.$paymentStatus.'
                                            </span>';

            $badgeClass = match ($status) {
                'PAID' => 'badge badge-bg-approve p-2 px-3',
                '' => 'badge badge-bg-draft',
            };
            // $arr[$i]['status'] = '<button class=" '.$badgeClass.' ">'.$status.'<button>';
            $arr[$i]['status'] = '<span class="'.$badgeClass.'">'.$status.'</span>';
            $arr[$i]['action'] = $actions;
            $i++;
        }

        $totalRecords = $data_cnt;
        $json_data = [
            'recordsTotal' => intval($totalRecords),
            'recordsFiltered' => intval($totalRecords),
            'data' => $arr,   // total data array
        ];
        echo json_encode($json_data);
    }

    public function list_returned(Request $request)
    {
        // $businesses = Business::where('status', 'RETURNED');
        // if (Auth::check() && Auth::user()->role == 1) {
        //     $businesses = $businesses->where('user_id', Auth::id());
        // }

        // // ✅ Apply submit_date filter before ->get()
        // if ($request->filled('submit_date')) {
        //     $businesses = $businesses->whereDate('submit_date', $request->submit_date);
        // }

        // $businesses = $businesses->orderBy('id', 'DESC')->get();
        $businesses = [];
        $allcounts = $this->getallsttausescout();

        $under_evaluations = $allcounts['under_evaluations'];
        $approves = $allcounts['approves'];
        $paid = $allcounts['paid'];
        $returns = $allcounts['returns'];
        $drafts = $allcounts['drafts'];
        $disapproves = $allcounts['disapproves'];
        $allApplicationCount = $allcounts['all'];
        $displayStartDate = date('Y-m-d');
        $displayEndDate = date('Y-m-d');
        $onhold = Business::where('status', 'UNDER EVALUATION')->where('on_hold', 1)->where('is_active', 1)->select('id');
        if (Auth::check() && Auth::user()->role == 1) {
            $onhold = $onhold->where('user_id', Auth::id())
                ->count();
        } else {
            $onhold = $onhold->count();
        }
        if (Auth::check() && Auth::user()->role == 1) {
            return view('business.list_returned', compact('businesses', 'under_evaluations',
                'approves',
                'paid',
                'returns',
                'drafts',
                'allApplicationCount',
                'disapproves',
                'displayStartDate',
                'displayEndDate','onhold'));
        } else {
            return view('business.list_returned_admin', compact(
                'businesses',
                'under_evaluations',
                'approves',
                'paid',
                'returns',
                'drafts',
                'allApplicationCount',
                'disapproves',
                'displayStartDate',
                'displayEndDate','onhold'
            ));
        }
    }

    public function getlistreturned(Request $request)
    {
        $params = $_REQUEST;
        $q = $request->input('q');
        $fromdate = $request->input('fromdate');
        $todate = $request->input('todate');

        if (! isset($params['start']) || ! isset($params['length'])) {
            $params['start'] = '0';
            $params['length'] = '10';
        }

        $columns = [
            1 => 'trustmark_id',
            2 => 'business_name',
            3 => 'reg_num',
            4 => 'business_type',
            5 => 'tin',
            6 => 'representative',
            7 => 'date_submitted',
        ];

        $sql = DB::table('businesses as a')
            ->leftJoin('users as b', 'a.evaluator_id', '=', 'b.id')
            ->select([
                DB::raw("NULLIF(a.id,'') as id"), // fixed here
                DB::raw("NULLIF(a.trustmark_id,'') as trustmark_id"),
                DB::raw("NULLIF(a.business_name,'') as business_name"),
                DB::raw("NULLIF(a.reg_num,'') as reg_num"),
                DB::raw("NULLIF(a.tin,'') as tin"),
                DB::raw("(CASE a.corporation_type
                            WHEN 1 THEN 'Sole Proprietorship'
                            WHEN 2 THEN 'Corporation/Partnership'
                            WHEN 4 THEN 'Cooperative'
                        END) as business_type"),
                DB::raw('b.name as evaluator'),
                DB::raw("DATE_FORMAT(a.date_returned, '%m/%d/%Y') as date_returned"),
                DB::raw("NULLIF(a.status,'') as status"),
                DB::raw("NULLIF(a.admin_remarks,'') as remarks"),
                DB::raw("NULLIF(a.payment_id,'') as payment_id"),
                DB::raw("NULLIF(a.corporation_type,'') as corporation_type"),
            ])
            ->where('a.is_active', 1);
        $sql->where('a.status', 'RETURNED')->where('evaluator_id', '>', 0);

        if (! empty($q)) {
            $sql->where(function ($query) use ($q) {
                $query->where(DB::raw('LOWER(trustmark_id)'), 'like', '%'.strtolower($q).'%')
                    ->orWhere(DB::raw('LOWER(business_name)'), 'like', '%'.strtolower($q).'%')
                    ->orWhere(DB::raw('LOWER(tin)'), 'like', '%'.strtolower($q).'%')
                    ->orWhere(DB::raw('LOWER(b.name)'), 'like', '%'.strtolower($q).'%')
                    ->orWhere(DB::raw('LOWER(admin_remarks)'), 'like', '%'.strtolower($q).'%')
                    ->orWhere(DB::raw('LOWER(reg_num)'), 'like', '%'.strtolower($q).'%');
            });
        }
        if (! empty($fromdate) && isset($fromdate)) {
            $sql->whereDate('date_returned', '>=', trim($fromdate));
        }
        if (! empty($todate) && isset($todate)) {
            $sql->whereDate('date_returned', '<=', trim($todate));
        }

        if (Auth::check() && Auth::user()->role == 1) {
            $sql->where('user_id', Auth::id());
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

        // $data=$this->business->getList($request);
        // echo "<pre>"; print_r($data); exit;
        $arr = [];
        $i = '0';
        $sr_no = (int) $request->input('start') - 1;
        $sr_no = $sr_no > 0 ? $sr_no + 1 : 0;
        $role = Auth::user()->role;

        foreach ($data as $row) {
            $status = $row->status;
            $sr_no = $sr_no + 1;
            $hashids = new Hashids(env('APP_KEY'), 10);
            $busn_id = $hashids->encode($row->id);
            $actions = '<a href="' . route('business.edit', $busn_id) . '" 
                                    data-bs-toggle="tooltip" data-bs-placement="bottom" 
                                    title="Edit"><i class="custom-pencil-icon fa fa-pencil"></i></a>';

            $arr[$i]['srno'] = $sr_no;
            $arr[$i]['trustmark_id'] = $row->trustmark_id ?? ' ';
            $arr[$i]['business_name'] = $row->business_name;
            $arr[$i]['reg_num'] = $row->reg_num ?? ' ';
            $arr[$i]['remarks'] = $row->remarks ?? ' ';
            $arr[$i]['business_type'] = $row->business_type ?? ' ';
            $arr[$i]['evaluator'] = $row->evaluator ?? ' ';
            $arr[$i]['date_returned'] = $row->date_returned ?? ' ';

            $badgeClass = match ($status) {
                'RETURNED' => 'badge badge-bg-returned p-2 px-3',
                '' => 'badge badge-bg-draft',
            };
            // $arr[$i]['status'] = '<button class=" '.$badgeClass.' ">'.$status.'<button>';
            $arr[$i]['status'] = '<span class="'.$badgeClass.'">'.$status.'</span>';
            $arr[$i]['action'] = $actions;
            $i++;
        }

        $totalRecords = $data_cnt;
        $json_data = [
            'recordsTotal' => intval($totalRecords),
            'recordsFiltered' => intval($totalRecords),
            'data' => $arr,   // total data array
        ];
        echo json_encode($json_data);
    }

    public function list_disapproved(Request $request)
    {
        // $businesses = Business::where('status', 'RETURNED');
        // if (Auth::check() && Auth::user()->role == 1) {
        //     $businesses = $businesses->where('user_id', Auth::id());
        // }

        // // ✅ Apply submit_date filter before ->get()
        // if ($request->filled('submit_date')) {
        //     $businesses = $businesses->whereDate('submit_date', $request->submit_date);
        // }

        // $businesses = $businesses->orderBy('id', 'DESC')->get();
        $businesses = [];
        $allcounts = $this->getallsttausescout();
        $under_evaluations = $allcounts['under_evaluations'];
        $approves = $allcounts['approves'];
        $paid = $allcounts['paid'];
        $returns = $allcounts['returns'];
        $drafts = $allcounts['drafts'];
        $disapproves = $allcounts['disapproves'];
        $allApplicationCount = $allcounts['all'];
        $displayStartDate = date('Y-m-d');
        $displayEndDate = date('Y-m-d');
        $onhold = Business::where('status', 'UNDER EVALUATION')->where('on_hold', 1)->where('is_active', 1)->select('id');
        if (Auth::check() && Auth::user()->role == 1) {
            $onhold = $onhold->where('user_id', Auth::id())
                ->count();
        } else {
            $onhold = $onhold->count();
        }
        if (Auth::check() && Auth::user()->role == 1) {
            return view('business.list_disapproved', compact('businesses', 'under_evaluations',
                'approves',
                'paid',
                'returns',
                'drafts',
                'allApplicationCount',
                'disapproves',
                'displayStartDate',
                'displayEndDate','onhold'));
        } else {
            return view('business.list_disapproved_admin', compact(
                'businesses',
                'under_evaluations',
                'approves',
                'paid',
                'returns',
                'drafts',
                'allApplicationCount',
                'disapproves',
                'displayStartDate',
                'displayEndDate','onhold'
            ));
        }
    }

    public function getlistdisapproved(Request $request)
    {
        $params = $_REQUEST;
        $q = $request->input('q');
        $fromdate = $request->input('fromdate');
        $todate = $request->input('todate');

        if (! isset($params['start']) || ! isset($params['length'])) {
            $params['start'] = '0';
            $params['length'] = '10';
        }

        $columns = [
            1 => 'trustmark_id',
            2 => 'business_name',
            3 => 'reg_num',
            4 => 'business_type',
            5 => 'tin',
            6 => 'representative',
            7 => 'date_submitted',
        ];

        $sql = DB::table('businesses as a')
            ->leftJoin('users as b', 'a.evaluator_id', '=', 'b.id')
            ->select([
                DB::raw("NULLIF(a.id,'') as id"), // fixed here
                DB::raw("NULLIF(a.trustmark_id,'') as trustmark_id"),
                DB::raw("NULLIF(a.business_name,'') as business_name"),
                DB::raw("NULLIF(a.reg_num,'') as reg_num"),
                DB::raw("NULLIF(a.tin,'') as tin"),
                DB::raw("(CASE a.corporation_type
                            WHEN 1 THEN 'Sole Proprietorship'
                            WHEN 2 THEN 'Corporation/Partnership'
                            WHEN 4 THEN 'Cooperative'
                        END) as business_type"),
                DB::raw('b.name as evaluator'),
                DB::raw("DATE_FORMAT(a.date_disapproved, '%m/%d/%Y') as date_disapproved"),
                DB::raw("NULLIF(a.status,'') as status"),
                DB::raw("NULLIF(a.admin_remarks,'') as remarks"),
                DB::raw("NULLIF(a.payment_id,'') as payment_id"),
                DB::raw("NULLIF(a.corporation_type,'') as corporation_type"),
            ])
            ->where('a.is_active', 1);
        // if (Auth::check() && Auth::user()->role != 1) {
        $sql->where('a.status', 'DISAPPROVED')->where('evaluator_id', '>', 0);
        // }
        if (! empty($q)) {
            $sql->where(function ($query) use ($q) {
                $query->where(DB::raw('LOWER(trustmark_id)'), 'like', '%'.strtolower($q).'%')
                    ->orWhere(DB::raw('LOWER(business_name)'), 'like', '%'.strtolower($q).'%')
                    ->orWhere(DB::raw('LOWER(tin)'), 'like', '%'.strtolower($q).'%')
                    ->orWhere(DB::raw('LOWER(b.name)'), 'like', '%'.strtolower($q).'%')
                    ->orWhere(DB::raw('LOWER(admin_remarks)'), 'like', '%'.strtolower($q).'%')
                    ->orWhere(DB::raw('LOWER(reg_num)'), 'like', '%'.strtolower($q).'%');
            });
        }
        if (! empty($fromdate) && isset($fromdate)) {
            $sql->whereDate('date_disapproved', '>=', trim($fromdate));
        }
        if (! empty($todate) && isset($todate)) {
            $sql->whereDate('date_disapproved', '<=', trim($todate));
        }

        if (Auth::check() && Auth::user()->role == 1) {
            $sql->where('user_id', Auth::id());
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

        // $data=$this->business->getList($request);
        // echo "<pre>"; print_r($data); exit;
        $arr = [];
        $i = '0';
        $sr_no = (int) $request->input('start') - 1;
        $sr_no = $sr_no > 0 ? $sr_no + 1 : 0;
        $role = Auth::user()->role;

        foreach ($data as $row) {
            $status = $row->status;
            $sr_no = $sr_no + 1;
            $hashids = new Hashids(env('APP_KEY'), 10);
            $ids = $hashids->encode($row->id);
            $actions = '<a href="'.route('business.view', $ids).'" 
                            data-bs-toggle="tooltip" data-bs-placement="bottom" 
                            title="View"><i class="custom-eye-icon fa fa-eye"></i></a>';

            $arr[$i]['srno'] = $sr_no;
            $arr[$i]['trustmark_id'] = $row->trustmark_id ?? '';
            $arr[$i]['business_name'] = $row->business_name;
            $arr[$i]['reg_num'] = $row->reg_num ?? '';
            $arr[$i]['remarks'] = $row->remarks ?? '';
            $arr[$i]['business_type'] = $row->business_type ?? '';
            $arr[$i]['evaluator'] = $row->evaluator ?? '';
            $arr[$i]['date_disapproved'] = $row->date_disapproved ?? '';

            $badgeClass = match ($status) {
                'DISAPPROVED' => 'badge badge-bg-disapproved p-2 px-3',
                '' => 'badge badge-bg-draft',
            };
            // $arr[$i]['status'] = '<button class=" '.$badgeClass.' ">'.$status.'<button>';
            $arr[$i]['status'] = '<span class="'.$badgeClass.'">'.$status.'</span>';
            $arr[$i]['action'] = $actions;
            $i++;
        }

        $totalRecords = $data_cnt;
        $json_data = [
            'recordsTotal' => intval($totalRecords),
            'recordsFiltered' => intval($totalRecords),
            'data' => $arr,   // total data array
        ];
        echo json_encode($json_data);
    }

    public function download_authorized($id)
    {
        // $hashids = new Hashids(env('APP_KEY'), 10);
        // $id = $hashids->decode($id)[0];
        $id = Crypt::decrypt($id);
        $business = Business::findOrFail($id);

        if (! $business->requirement_upload) {
            abort(404, 'File not found');
        }

        $fileRelativePath = str_replace('storage/', '', $business->requirement_upload);
        $filePath = storage_path('app/public/'.$fileRelativePath);

        if (! file_exists($filePath)) {
            $filePath = public_path('storage/'.$fileRelativePath);
            if (! file_exists($filePath)) {
                abort(404, 'File not found on server');
            }
        }

        $extension = pathinfo($filePath, PATHINFO_EXTENSION);
        $originalFilename = pathinfo($filePath, PATHINFO_FILENAME);

        $fileName = $originalFilename.'.'.$extension;

        $mimeTypes = [
            'pdf' => 'application/pdf',
            'jpg' => 'image/jpeg',
            'jpeg' => 'image/jpeg',
            'png' => 'image/png',
        ];
        $contentType = $mimeTypes[strtolower($extension)] ?? 'application/octet-stream';
        $fileName = $originalFilename.'.'.$extension;

        // Serve the file inline so it opens in browser
        return response()->file($filePath, [
            'Content-Type' => $contentType,
            'Content-Disposition' => 'inline; filename="'.$fileName.'"',
        ]);
    }

    public function download_business_registration($id)
    {
        // $id = Crypt::decrypt($id);
        $business = Business::findOrFail($id);

        if (! $business->docs_business_reg) {
            abort(404, 'File not found');
        }

        $fileRelativePath = str_replace('storage/', '', $business->docs_business_reg);
        $filePath = storage_path('app/public/'.$fileRelativePath);

        if (! file_exists($filePath)) {
            $filePath = public_path('storage/'.$fileRelativePath);
            if (! file_exists($filePath)) {
                abort(404, 'File not found on server');
            }
        }

        $extension = pathinfo($filePath, PATHINFO_EXTENSION);
        $originalFilename = pathinfo($filePath, PATHINFO_FILENAME);

        $fileName = $originalFilename.'.'.$extension;

        $mimeTypes = [
            'pdf' => 'application/pdf',
            'jpg' => 'image/jpeg',
            'jpeg' => 'image/jpeg',
            'png' => 'image/png',
        ];
        $contentType = $mimeTypes[strtolower($extension)] ?? 'application/octet-stream';
        $fileName = $originalFilename.'.'.$extension;

        // Serve the file inline so it opens in browser
        return response()->file($filePath, [
            'Content-Type' => $contentType,
            'Content-Disposition' => 'inline; filename="'.$fileName.'"',
        ]);
    }

    public function download_bir_2303($id)
    {
        // $id = Crypt::decrypt($id);
        $business = Business::findOrFail($id);

        if (! $business->docs_bir_2303) {
            abort(404, 'File not found');
        }

        $fileRelativePath = str_replace('storage/', '', $business->docs_bir_2303);
        $filePath = storage_path('app/public/'.$fileRelativePath);

        if (! file_exists($filePath)) {
            $filePath = public_path('storage/'.$fileRelativePath);
            if (! file_exists($filePath)) {
                abort(404, 'File not found on server');
            }
        }

        $extension = pathinfo($filePath, PATHINFO_EXTENSION);
        $originalFilename = pathinfo($filePath, PATHINFO_FILENAME);

        $fileName = $originalFilename.'.'.$extension;

        $mimeTypes = [
            'pdf' => 'application/pdf',
            'jpg' => 'image/jpeg',
            'jpeg' => 'image/jpeg',
            'png' => 'image/png',
        ];
        $contentType = $mimeTypes[strtolower($extension)] ?? 'application/octet-stream';
        $fileName = $originalFilename.'.'.$extension;

        // Serve the file inline so it opens in browser
        return response()->file($filePath, [
            'Content-Type' => $contentType,
            'Content-Disposition' => 'inline; filename="'.$fileName.'"',
        ]);
    }

    public function download_internal_redress($id)
    {
        // $id = Crypt::decrypt($id);
        $business = Business::findOrFail($id);

        if (! $business->docs_internal_redress) {
            abort(404, 'File not found');
        }

        $fileRelativePath = str_replace('storage/', '', $business->docs_internal_redress);
        $filePath = storage_path('app/public/'.$fileRelativePath);
        if (! file_exists($filePath)) {
            $filePath = public_path('storage/'.$fileRelativePath);
            if (! file_exists($filePath)) {
                abort(404, 'File not found on server');
            }
        }

        $extension = pathinfo($filePath, PATHINFO_EXTENSION);
        $originalFilename = pathinfo($filePath, PATHINFO_FILENAME);

        $fileName = $originalFilename.'.'.$extension;

        $mimeTypes = [
            'pdf' => 'application/pdf',
            'jpg' => 'image/jpeg',
            'jpeg' => 'image/jpeg',
            'png' => 'image/png',
        ];
        $contentType = $mimeTypes[strtolower($extension)] ?? 'application/octet-stream';
        $fileName = $originalFilename.'.'.$extension;

        // Serve the file inline so it opens in browser
        return response()->file($filePath, [
            'Content-Type' => $contentType,
            'Content-Disposition' => 'inline; filename="'.$fileName.'"',
        ]);
    }

    public function download_bmbe_doc($id)
    {
        $business = Business::findOrFail($id);

        if (!$business->bmbe_doc) {
            abort(404, 'File not found');
        }

        $fileRelativePath = str_replace('storage/', '', $business->bmbe_doc);
        $filePath = storage_path('app/public/' . $fileRelativePath);
        if (! file_exists($filePath)) {
            $filePath = public_path('storage/'.$fileRelativePath);
            if (! file_exists($filePath)) {
                abort(404, 'File not found on server');
            }
        }

        $extension = strtolower(pathinfo($filePath, PATHINFO_EXTENSION));
        $originalFilename = pathinfo($filePath, PATHINFO_FILENAME);
        $fileName = $originalFilename . '.' . $extension;

        $mimeTypes = [
            'pdf' => 'application/pdf',
            'jpg' => 'image/jpeg',
            'jpeg' => 'image/jpeg',
            'png' => 'image/png',
        ];
        $contentType = $mimeTypes[$extension] ?? 'application/octet-stream';
        return response()->file($filePath, [
            'Content-Type' => $contentType,
            'Content-Disposition' => 'inline; filename="' . $fileName . '"',
        ]);
    }
    public function download_busn_valuation_doc($id)
    {
        $business = Business::findOrFail($id);

        if (!$business->busn_valuation_doc) {
            abort(404, 'File not found');
        }

        $fileRelativePath = str_replace('storage/', '', $business->busn_valuation_doc);
        $filePath = storage_path('app/public/' . $fileRelativePath);
        if (! file_exists($filePath)) {
            $filePath = public_path('storage/'.$fileRelativePath);
            if (! file_exists($filePath)) {
                abort(404, 'File not found on server');
            }
        }

        $extension = strtolower(pathinfo($filePath, PATHINFO_EXTENSION));
        $originalFilename = pathinfo($filePath, PATHINFO_FILENAME);
        $fileName = $originalFilename . '.' . $extension;

        $mimeTypes = [
            'pdf' => 'application/pdf',
            'jpg' => 'image/jpeg',
            'jpeg' => 'image/jpeg',
            'png' => 'image/png',
        ];
        $contentType = $mimeTypes[$extension] ?? 'application/octet-stream';
        return response()->file($filePath, [
            'Content-Type' => $contentType,
            'Content-Disposition' => 'inline; filename="' . $fileName . '"',
        ]);
    }

    // auto download business documents
    // public function download_business_document($id, $type)
    // {
    //     $business = Business::findOrFail($id);

    //     $fields = [
    //         'registration' => 'docs_business_reg',
    //         'bir' => 'docs_bir_2303',
    //         'redress' => 'docs_internal_redress',
    //     ];

    //     if (!array_key_exists($type, $fields)) {
    //         abort(400, 'Invalid document type');
    //     }

    //     $field = $fields[$type];
    //     $file = $business->$field;

    //     if (!$file) {
    //         abort(404, 'File not found');
    //     }

    //     $fileRelativePath = str_replace('storage/', '', $file);
    //     $filePath = storage_path('app/public/' . $fileRelativePath);

    //     if (!file_exists($filePath)) {
    //         abort(404, 'File not found on server');
    //     }

    //     $extension = pathinfo($filePath, PATHINFO_EXTENSION);
    //     $originalFilename = basename($file);

    //     $mimeTypes = [
    //         'pdf' => 'application/pdf',
    //         'jpg' => 'image/jpeg',
    //         'jpeg' => 'image/jpeg',
    //         'png' => 'image/png',
    //     ];

    //     $contentType = $mimeTypes[strtolower($extension)] ?? 'application/octet-stream';

    //     return response()->download($filePath, $originalFilename, [
    //         'Content-Type' => $contentType,
    //     ]);
    // }

    // open business documents in browser
    public function download_business_document($id, $type)
    {
        $business = Business::findOrFail($id);

        $fields = [
            'registration' => 'docs_business_reg',
            'bir' => 'docs_bir_2303',
            'redress' => 'docs_internal_redress',
        ];

        if (! array_key_exists($type, $fields)) {
            abort(400, 'Invalid document type');
        }

        $field = $fields[$type];
        $file = $business->$field;

        if (! $file) {
            abort(404, 'File not found');
        }


        $fileRelativePath = str_replace('storage/', '', $file);
        $filePath = storage_path('app/public/'.$fileRelativePath);
        if (! file_exists($filePath)) {
            $filePath = public_path('storage/'.$fileRelativePath);
            if (! file_exists($filePath)) {
                abort(404, 'File not found on server');
            }
        }

        $extension = pathinfo($filePath, PATHINFO_EXTENSION);
        $originalFilename = basename($file);

        $mimeTypes = [
            'pdf' => 'application/pdf',
            'jpg' => 'image/jpeg',
            'jpeg' => 'image/jpeg',
            'png' => 'image/png',
        ];

        $contentType = $mimeTypes[strtolower($extension)] ?? 'application/octet-stream';

        // 🔁 Return file inline for browser viewing
        return response()->file($filePath, [
            'Content-Type' => $contentType,
            'Content-Disposition' => 'inline; filename="'.$originalFilename.'"',
        ]);
    }

    public function jwtEncode($data, $secret)
    {
        $header = ['alg' => 'HS256', 'typ' => 'JWT'];
        $b64 = fn ($input) => rtrim(strtr(base64_encode(json_encode($input)), '+/', '-_'), '=');

        $h = $b64($header);
        $p = $b64($data);
        $sb = "$h.$p";

        $s = hash_hmac('sha256', $sb, $secret, true);
        $se = rtrim(strtr(base64_encode($s), '+/', '-_'), '=');

        return "$sb.$se";
    }

    public function getUrls($id)
    {
        $business = Business::findOrFail($id);
        $urls = $business->url_platform ?? [];

        return response()->json($urls);
    }

    public function updateUrls(Request $request, $id)
    {
        $business = Business::findOrFail($id);
        $urls = $request->input('urls', []);

        $business->url_platform = $urls;
        $business->save();
        DB::table('user_logs')->updateOrInsert(
            [
                'busn_id'   => $business->id,
                'action_id' => 3, 
                'created_date'     => now(),
            ],
            [
            'action_name'      => 'updated',
            'message'          => Auth::user()->name . ' manually update the business platform URL section with Sec-No.   ' 
                                  . $business->trustmark_id
                                  . ' dated ' . now()->format('Y-m-d H:i:s') . '.',
            'public_ip_address'=> $request->ip(),
            'status'           => $business->status,
            'remarks'          => $business->admin_remarks,
            'longitude'        => $request->input('longitude'), 
            'latitude'         => $request->input('latitude'),  
            'created_by'       => Auth::id(),
            'created_by_name'  => Auth::user()->name,
            
        ]);
        return response()->json([
            'success' => true,
            'urls' => $urls,
        ]);
    }

    public function updateBusinessInformation(Request $request, $id)
    {
        $business = Business::findOrFail($id);

        $business->corporation_type = $request->input('type_id');
        $business->reg_num = $request->input('reg_num');
        $business->tin = $request->input('tin_num');
        $business->business_name = $request->input('business_name');
        $business->franchise = $request->input('franchise');
        $business->category_id = $request->input('category');
        $business->category_other_description = $request->input('other_category');
        $business->save();
        $businessType = TypeCorporation::where('id', $business->corporation_type)->first();
        DB::table('user_logs')->updateOrInsert(
            [
                'busn_id'   => $business->id,
                'action_id' => 3, 
                'created_date'     => now(),
            ],
            [
            'action_name'      => 'updated',
            'message'          => Auth::user()->name . ' manually update the business information section with Sec-No.  ' 
                                  . $business->trustmark_id . ', with the following changes Bus-Type : ' . $businessType->name 
                                  . ', Reg-No : ' . $business->reg_num.', TIN: ' . $business->tin_num.', Bus-Name: ' . $business->business_name.',
                                  Trade Name: ' . $business->franchise .', Bus-Category: ' . $business->category_id.', Bus-Description: ' . $business->other_category
                                  . ' dated ' . now()->format('Y-m-d H:i:s') . '.',
            'public_ip_address'=> $request->ip(),
            'status'           => $business->status,
            'remarks'          => $business->admin_remarks,
            'longitude'        => $request->input('longitude'), 
            'latitude'         => $request->input('latitude'),  
            'created_by'       => Auth::id(),
            'created_by_name'  => Auth::user()->name,
            
        ]);
        return response()->json(['status' => 'success']);
    }

    public function updateAuthorizedRepresentative(Request $request, $id)
    {
        $business = Business::findOrFail($id);
        $fullName = trim(
            $request->input('first_name').' '.
                ($request->input('middle_name') ? $request->input('middle_name').' ' : '').
                $request->input('last_name').
                ($request->input('suffix') ? ', '.$request->input('suffix') : '')
        );
        $business->pic_name = $fullName;
        $business->first_name = $request->input('first_name');
        $business->middle_name = $request->input('middle_name');
        $business->last_name = $request->input('last_name');
        $business->suffix = $request->input('suffix');
        $business->pic_ctc_no = $request->input('ctc_no');
        $business->pic_email = $request->input('email');
        $business->requirement_id = $request->input('issued_id');
        $business->requirement_expired = $request->input('expired_date');

        if ($request->hasFile('req_upload')) {
            $file = $request->file('req_upload');
            $originalName = $file->getClientOriginalName();
            $fileNameWithoutExt = pathinfo($originalName, PATHINFO_FILENAME);
            $extension = $file->getClientOriginalExtension();

            $fileName = time().'_'.$fileNameWithoutExt.'.'.$extension;
            $req_upload_path = $file->storeAs('document-upload/requirement_reps', $fileName, 'public');

            if ($business->requirement_upload) {
                Storage::disk('public')->delete($business->requirement_upload);
            }

            $business->requirement_upload = $req_upload_path;
        }
        $business->save();
        DB::table('user_logs')->updateOrInsert(
            [
                'busn_id'   => $business->id,
                'action_id' => 3, 
                'created_date'     => now(),
            ],
            [
            'action_name'      => 'updated',
            'message'          => Auth::user()->name . ' manually update the authorized representative section with Sec-No.    ' 
                                  . $business->trustmark_id
                                  . ' dated ' . now()->format('Y-m-d H:i:s') . '.',
            'public_ip_address'=> $request->ip(),
            'status'           => $business->status,
            'remarks'          => $business->admin_remarks,
            'longitude'        => $request->input('longitude'), 
            'latitude'         => $request->input('latitude'),  
            'created_by'       => Auth::id(),
            'created_by_name'  => Auth::user()->name,
            
        ]);
        return response()->json(['status' => 'success']);
    }

    public function barangaysearch(Request $request)
    {
        $query = $request->get('q', '');

        $Barangay = Barangay::where('brgy_description', 'like', "%{$query}%")
            ->select('id', 'brgy_description')
            ->limit(50)
            ->get();

        return response()->json($Barangay);
    }

    public function eveluatorsearch(Request $request)
    {
        $query = $request->get('q', '');
        $eveluator = DB::table('user_admins AS a')
            ->join('users AS b', 'b.id', '=', 'a.user_id')
            ->select('a.user_id', 'b.name')
            ->where('b.name', 'like', "%{$query}%")
            ->limit(50)
            ->get();

        return response()->json($Barangay);
    }

    public function updatebusinessAddress(Request $request, $id)
    {
        $business = Business::findOrFail($id);
        $business->complete_address = $request->input('address');
        $Barangay = Barangay::findOrFail($request->input('barangay_id'));
        $business->barangay_id = $request->input('barangay_id');
        $business->region_id = $Barangay->reg_no;
        $business->province_id = $Barangay->prov_no;
        $business->municipality_id = $Barangay->mun_no;
        $business->save();
        $barangay_id = DB::table('barangays')->select('brgy_description')->where('id', $business->barangay_id)->first();
        DB::table('user_logs')->updateOrInsert(
            [
                'busn_id'   => $business->id,
                'action_id' => 3, 
                'created_date'     => now(),
            ],
            [
            'action_name'      => 'updated',
            'message'          => Auth::user()->name . ' manually update the business address section with Sec-No.  ' 
                                  . $business->trustmark_id. ' Complete Address: '. $business->complete_address.
                                  ' Barangay, Municipality, Province, Region: '. $barangay_id->brgy_description
                                  . ' dated ' . now()->format('Y-m-d H:i:s') . '.',
            'public_ip_address'=> $request->ip(),
            'status'           => $business->status,
            'remarks'          => $business->admin_remarks,
            'longitude'        => $request->input('longitude'), 
            'latitude'         => $request->input('latitude'),  
            'created_by'       => Auth::id(),
            'created_by_name'  => Auth::user()->name,
            
        ]);
        return response()->json(['status' => 'success']);
    }

    public function save_documentattachments(Request $request, $businessId)
    { 
        $business = Business::find($businessId);
        // dd($request->input('is_bmbe'));exit;
        if (! $business) {
            \Log::error('Business not found for ID: '.$businessId);

            return redirect()->back()->withErrors(['Business not found.']);
        }

        try {
            // dd($request->input('is_bmbe'));exit;
            if (empty($business->docs_business_reg)) {
                $rules['business_reg'] = 'required|file|mimes:jpg,jpeg,png,pdf|max:10240';
            } else {
                $rules['business_reg'] = 'nullable|file|mimes:jpg,jpeg,png,pdf|max:10240';
            }

            if (empty($business->docs_bir_2303)) {
                $rules['bir_2303'] = 'required|file|mimes:jpg,jpeg,png,pdf|max:10240';
            } else {
                $rules['bir_2303'] = 'nullable|file|mimes:jpg,jpeg,png,pdf|max:10240';
            }

            if (empty($business->docs_internal_redress)) {
                $rules['internal_redress'] = 'required|file|mimes:jpg,jpeg,png,pdf|max:10240';
            } else {
                $rules['internal_redress'] = 'nullable|file|mimes:jpg,jpeg,png,pdf|max:10240';
            }

            // if (empty($business->bmbe_doc)) {
            //     $rules['bmbe_doc'] = 'required|file|mimes:jpg,jpeg,png,pdf|max:10240';
            // } else {
            //     $rules['bmbe_doc'] = 'nullable|file|mimes:jpg,jpeg,png,pdf|max:10240';
            // }
            // if (empty($business->busn_valuation_doc)) {
            //     $rules['busn_valuation_doc'] = 'required|file|mimes:jpg,jpeg,png,pdf|max:10240';
            // } else {
            //     $rules['busn_valuation_doc'] = 'nullable|file|mimes:jpg,jpeg,png,pdf|max:10240';
            // }
            $validated = $request->validate($rules);
            
            $data = [];

            if ($request->hasFile('business_reg')) {

                $file = $request->file('business_reg');
                $originalName = $file->getClientOriginalName();
            
                $fileNameWithoutExt = pathinfo($originalName, PATHINFO_FILENAME);
                $extension = $file->getClientOriginalExtension();
            
                $now = now();
                $timestamp = $now->format('YmdHis');
                $year  = $now->format('Y');   // 2025
                $month = $now->format('M');   // Dec
            
                $fileName = $timestamp . '_' . $fileNameWithoutExt . '.' . $extension;
                $directory = "document-upload/business_registration/{$year}/{$month}";
                $business_reg_path = $file->storeAs($directory, $fileName, 'public');
                $data['docs_business_reg'] = $business_reg_path;
                if (!empty($business->docs_business_reg)) {
                    Storage::disk('public')->delete($business->docs_business_reg);
                }
            
            } else {
                // Keep existing file path
                $data['docs_business_reg'] = $business->docs_business_reg;
            }
            if ($request->hasFile('bir_2303')) {

                $file = $request->file('bir_2303');
                $originalName = $file->getClientOriginalName();
            
                $fileNameWithoutExt = pathinfo($originalName, PATHINFO_FILENAME);
                $extension = $file->getClientOriginalExtension();
            
                $now = now();
                $timestamp = $now->format('YmdHis');
                $year  = $now->format('Y');   
                $month = $now->format('M'); 
            
                $fileName = $timestamp . '_' . $fileNameWithoutExt . '.' . $extension;
                $directory = "document-upload/bir_2303/{$year}/{$month}";
                $bir_2303_path = $file->storeAs($directory, $fileName, 'public');
                $data['docs_bir_2303'] = $bir_2303_path;
                if (!empty($business->docs_bir_2303)) {
                    Storage::disk('public')->delete($business->docs_bir_2303);
                }
            
            } else {
                // Keep existing file path
                $data['docs_bir_2303'] = $business->docs_bir_2303;
            }
            if ($request->hasFile('internal_redress')) {

                $file = $request->file('internal_redress');
                $originalName = $file->getClientOriginalName();
            
                $fileNameWithoutExt = pathinfo($originalName, PATHINFO_FILENAME);
                $extension = $file->getClientOriginalExtension();
            
                $now = now();
                $timestamp = $now->format('YmdHis');
                $year  = $now->format('Y');   // 2025
                $month = $now->format('M');   // Dec
            
                $fileName = $timestamp . '_' . $fileNameWithoutExt . '.' . $extension;
                $directory = "document-upload/internal_redress/{$year}/{$month}";
                $internal_redress_path = $file->storeAs($directory, $fileName, 'public');
                $data['docs_internal_redress'] = $internal_redress_path;
                if (!empty($business->docs_internal_redress)) {
                    Storage::disk('public')->delete($business->docs_internal_redress);
                }
            
            } else {
                // Keep existing file path
                $data['docs_internal_redress'] = $business->docs_internal_redress;
            }
            $data['is_bmbe'] = $request->input('is_bmbe');
            
            if ($request->hasFile('bmbe_doc')) {

                $file = $request->file('bmbe_doc');
                $originalName = $file->getClientOriginalName();
            
                $fileNameWithoutExt = pathinfo($originalName, PATHINFO_FILENAME);
                $extension = $file->getClientOriginalExtension();
            
                $now = now();
                $timestamp = $now->format('YmdHis');
                $year  = $now->format('Y');   // 2025
                $month = $now->format('M');   // Dec
            
                $fileName = $timestamp . '_' . $fileNameWithoutExt . '.' . $extension;
                $uploadDir = "document-upload/bmbe_doc/{$year}/{$month}";
                if (!Storage::disk('public')->exists($uploadDir)) {
                    Storage::disk('public')->makeDirectory($uploadDir);
                }
                $bmbe_doc_path = $file->storeAs($uploadDir, $fileName, 'public');
                $data['bmbe_doc'] = $bmbe_doc_path;
                if (!empty($business->bmbe_doc)) {
                    Storage::disk('public')->delete($business->bmbe_doc);
                }
            
            } else {
                // Keep existing file
                $data['bmbe_doc'] = $business->bmbe_doc;
            }
            if ($request->input('is_bmbe') == 0) {
                $data['bmbe_doc'] = null;
                if ($business->bmbe_doc) {
                    Storage::disk('public')->delete($business->bmbe_doc);
                }
            }
            //&&  ($request->input('is_bmbe')!=$business->is_bmbe || $request->input('busn_category_id')!=$business->busn_category_id)
            if((int)$business->payment_id==0){
                if($business->status=='APPROVED'){
                    $applicationFees = ApplicationFees::where('app_code', $business->app_code)->get();
                    if(count($applicationFees)>0){
                        DB::table('business_fees')->where('busn_id', $businessId)->where('app_code', $business->app_code)->where('tax_year', $business->tax_year)->delete();
                    }
                    foreach ($applicationFees as $app_fee) {
                        if($app_fee->is_application_fee==1){
                            if ((int)$request->input('is_bmbe') == 0) {
                                $arrCatFee = DB::table('application_fee_category')->select('amount')->where('application_fee_id', $app_fee->id)->where('busn_category_id',$request->input('busn_category_id'))->get();
                                foreach ($arrCatFee as $catFee) {
                                    if($catFee->amount > 0){
                                        $business_fee = new BusinessFees;
                                        $business_fee->tax_year = $business->tax_year;
                                        $business_fee->busn_id = $businessId;
                                        $business_fee->app_code = $business->app_code;
                                        $business_fee->app_name = $app_fee->app_name;
                                        $business_fee->fee_id = $app_fee->fee_id;
                                        $business_fee->fee_name = $app_fee->fee_name;
                                        $business_fee->amount = $catFee->amount;
                                        $business_fee->category_id = $business->category_id;
                                        $business_fee->created_by = Auth::id();
                                        $business_fee->create_date = now();
                                        $business_fee->save();
                                    }
                                }
                            }
                        } else {
                            $business_fee = new BusinessFees;
                            $business_fee->tax_year = $business->tax_year;
                            $business_fee->busn_id = $businessId;
                            $business_fee->app_code = $business->app_code;
                            $business_fee->app_name = $app_fee->app_name;
                            $business_fee->fee_id = $app_fee->fee_id;
                            $business_fee->fee_name = $app_fee->fee_name;
                            $business_fee->amount = $app_fee->amount;
                            $business_fee->category_id = $business->category_id;
                            $business_fee->created_by = Auth::id();
                            $business_fee->create_date = now();
                            $business_fee->save();
                        }
                    }
                }
            }else{
                $data['is_bmbe']=$business->is_bmbe;
            }
            $data['busn_category_id'] = $request->input('busn_category_id');
            if ($request->hasFile('busn_valuation_doc')) {

                $file = $request->file('busn_valuation_doc');
                $originalName = $file->getClientOriginalName();
            
                $fileNameWithoutExt = pathinfo($originalName, PATHINFO_FILENAME);
                $extension = $file->getClientOriginalExtension();
            
                $now = now();
                $timestamp = $now->format('YmdHis');
                $year  = $now->format('Y');   // 2025
                $month = $now->format('M');   // Dec
            
                $fileName = $timestamp . '_' . $fileNameWithoutExt . '.' . $extension;
                $uploadDir = "document-upload/busn_valuation_doc/{$year}/{$month}";
                if (!Storage::disk('public')->exists($uploadDir)) {
                    Storage::disk('public')->makeDirectory($uploadDir);
                }
                $busn_valuation_doc_path = $file->storeAs($uploadDir, $fileName, 'public');
                $data['busn_valuation_doc'] = $busn_valuation_doc_path;
                if (!empty($business->busn_valuation_doc)) {
                    Storage::disk('public')->delete($business->busn_valuation_doc);
                }
            
            } else {
                // Keep existing file
                $data['busn_valuation_doc'] = $business->busn_valuation_doc;
            }
            if ($request->input('is_bmbe') == 1) {
                $data['busn_category_id'] = null;
                $data['busn_valuation_doc'] = null;
                if ($business->busn_valuation_doc) {
                    Storage::disk('public')->delete($business->busn_valuation_doc);
                }
            }
            $saved = $business->update($data);
            DB::table('user_logs')->updateOrInsert(
                [
                    'busn_id'   => $business->id,
                    'action_id' => 3, 
                    'created_date'     => now(),
                ],
                [
                'action_name'      => 'updated',
                'message'          => Auth::user()->name . ' manually update the documents attachment section with Sec-No.  ' 
                                      . $business->trustmark_id
                                      . ' dated ' . now()->format('Y-m-d H:i:s') . '.',
                'public_ip_address'=> $request->ip(),
                'status'           => $business->status,
                'remarks'          => $business->admin_remarks,
                'longitude'        => $request->input('longitude'), 
                'latitude'         => $request->input('latitude'),  
                'created_by'       => Auth::id(),
                'created_by_name'  => Auth::user()->name,
                
            ]);
            if ($saved) {
                \Log::info('Business documents saved successfully for business ID: '.$business->id);
            } else {
                \Log::error('Failed to save business documents for business ID: '.$business->id);
            }

            return redirect()->back()
                // ->with('go_to_payments', true)
                ->with('go_to_confirmations', true)
                ->with('business_id', $request->business_id)
                ->with('business', $business);
        } catch (\Exception $e) {
            \Log::error('Registration Error: '.$e->getMessage());

            return redirect()->back()->withErrors(['error' => 'Failed to save documents. Please try again.']);
        }
    }

    public function assignEvaluator(Request $request)
    {
        $request->validate([
            'business_id' => 'required|exists:businesses,id',
            'evaluator_id' => 'required|exists:users,id',
        ]);

        $currentUserId = Auth::id();
        $isAdmin = DB::table('user_admins')
            ->where('user_id', $currentUserId)
            ->value('is_admin');
        if (! $isAdmin) {
            $currentEvaluator = DB::table('businesses')
                ->where('id', $request->business_id)
                ->value('evaluator_id');
            if ($currentEvaluator != $currentUserId || $request->evaluator_id != $currentUserId) {
                return response()->json([
                    'success' => false,
                    'message' => 'Non-admin accounts are not allowed to assign/change evaluators for this application.',
                ], 200);
            }
        }
        DB::table('businesses')
            ->where('id', $request->business_id)
            ->update([
                'evaluator_id' => $request->evaluator_id,
                'evaluator_assigned_date' => now(),
            ]);

        $evaluatorName = DB::table('users')
            ->where('id', $request->evaluator_id)
            ->value('name');

        $businessdata = DB::table('businesses')
            ->where('id', $request->business_id)->select('trustmark_id', 'status')
            ->first();
        $lat = $request->lat;
        $long = $request->long;
        $status = $request->status;

        $remark = $request->input('remark');
        if (! empty($request->reason)) {
            $remark.' Reason: '.$request->reason;
        }

        $message = Auth::user()->name.' assigned the application with Sec-No.'.$businessdata->trustmark_id.' to evaluator '.$evaluatorName.' dated '.date('Y-m-d H:i:s');
        saveUserLogs($lat, $long, $request->business_id, 3, 'updated', $message, $businessdata->status, $remark);

        return response()->json([
            'success' => true,
            'message' => "Evaluator assigned successfully: {$evaluatorName}",
        ]);
    }

    public function getCheckRecords($id)
    {
        $business = DB::table('businesses')->where('id', $id)->first();
        $selectedTIN = $business->tin ?? '';
        $records = DB::table('businesses as a')
        ->leftJoin('users as b', 'a.user_id', '=', 'b.id')
        ->leftJoin('users as c', 'a.evaluator_id', '=', 'c.id')          

        ->select(
            DB::raw("NULLIF(a.trustmark_id,'') as `SecurityNo`"),
            DB::raw("NULLIF(a.business_name,'') as `BusinessName`"),
            DB::raw("NULLIF(a.reg_num,'') as `RegistrationNo`"),
            DB::raw("NULLIF((case a.corporation_type
                    when 1 then 'Sole Proprietorship'
                    when 2 then 'Corporation/Partnership'
                    when 4 then 'Cooperative'
                end),'') as `BusinessType`"),
            DB::raw("NULLIF(c.name,'') as Evaluator"),
                DB::raw("NULLIF(a.tin,'') as `TIN`"),
                DB::raw('b.name as `Representative`'),
                DB::raw("DATE_FORMAT(a.submit_date, '%m/%d/%Y') as `Submitted`"),
                DB::raw("NULLIF(a.admin_remarks,'') as `Remarks`"),
                DB::raw("NULLIF(a.status,'') as `Status`")
            )
            ->where('a.tin', 'like', substr($selectedTIN, 0, 11).'%')
            ->where('a.is_active', 1)
            ->orderByDesc('a.submit_date')
            ->get();

        return response()->json(['data' => $records]);
    }

    public function getCheckRecordBusinessName($id)
    {
        $business = DB::table('businesses')->where('id', $id)->first();
        $selectedBusinessName = $business->business_name ?? '';
        $records = DB::table('businesses as a')
        ->leftJoin('users as b', 'a.user_id', '=', 'b.id')
        ->leftJoin('users as c', 'a.evaluator_id', '=', 'c.id')          

        ->select(
            DB::raw("NULLIF(a.trustmark_id,'') as `SecurityNo`"),
            DB::raw("NULLIF(a.business_name,'') as `BusinessName`"),
            DB::raw("NULLIF(a.reg_num,'') as `RegistrationNo`"),
            DB::raw("NULLIF((case a.corporation_type
                    when 1 then 'Sole Proprietorship'
                    when 2 then 'Corporation/Partnership'
                    when 4 then 'Cooperative'
                end),'') as `BusinessType`"),
            DB::raw("NULLIF(c.name,'') as Evaluator"),
                DB::raw("NULLIF(a.tin,'') as `TIN`"),
                DB::raw('b.name as `Representative`'),
                DB::raw("DATE_FORMAT(a.submit_date, '%m/%d/%Y') as `Submitted`"),
                DB::raw("NULLIF(a.admin_remarks,'') as `Remarks`"),
                DB::raw("NULLIF(a.status,'') as `Status`")
            )
            ->where('a.business_name', 'like', '%'.$selectedBusinessName.'%')
            ->where('a.is_active', 1)
            ->orderByDesc('a.submit_date')
            ->get();

        return response()->json(['data' => $records]);
    }

    public function getCheckRecordBusinessRegistration($id)
    {
        $business = DB::table('businesses')->where('id', $id)->first();
        $selectedreg_num = $business->reg_num ?? '';
        $records = DB::table('businesses as a')
            ->leftJoin('users as b', 'a.user_id', '=', 'b.id')
            ->leftJoin('users as c', 'a.evaluator_id', '=', 'c.id')          

            ->select(
                DB::raw("NULLIF(a.trustmark_id,'') as `SecurityNo`"),
                DB::raw("NULLIF(a.business_name,'') as `BusinessName`"),
                DB::raw("NULLIF(a.reg_num,'') as `RegistrationNo`"),
                DB::raw("NULLIF((case a.corporation_type
                        when 1 then 'Sole Proprietorship'
                        when 2 then 'Corporation/Partnership'
                        when 4 then 'Cooperative'
                    end),'') as `BusinessType`"),
                DB::raw("NULLIF(c.name,'') as Evaluator"),
                DB::raw("NULLIF(a.tin,'') as `TIN`"),
                DB::raw('b.name as `Representative`'),
                DB::raw("DATE_FORMAT(a.submit_date, '%m/%d/%Y') as `Submitted`"),
                DB::raw("NULLIF(a.admin_remarks,'') as `Remarks`"),
                DB::raw("NULLIF(a.status,'') as `Status`")
            )
            ->where('a.reg_num', 'like', '%'.$selectedreg_num.'%')
            ->where('a.is_active', 1)
            ->orderByDesc('a.submit_date')
            ->get();

        return response()->json(['data' => $records]);
    }

    public function getAuditLogsList(Request $request)
    {
        $startdate = $request->input('fromdate');
        $enddate = $request->input('todate');
        $businessId = $request->input('businessId');
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
            ->where('a.busn_id', $businessId)
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
    
    public function getFollowupEmailList(Request $request)
    {
        $busn_id = $request->input('busn_id');
        $startdate = $request->input('fromdate');
        $enddate = $request->input('todate');
        $query = DB::table('business_followups as a')
            ->select(
                'a.followup_date as date_time',
                DB::raw("(CASE a.is_type 
                            WHEN 1 THEN 'Unpaid' 
                            WHEN 2 THEN 'Returned'
			    WHEN 3 THEN 'Archived' 
                            ELSE '-' END) as typeData"),
                'a.followup_message as message_description'
            )->WHERE('busn_id',$busn_id);
        if (!empty($startdate)) {
            $sdate = explode('-', $startdate);
            $startdate = date('Y-m-d', strtotime("{$sdate[2]}-{$sdate[1]}-{$sdate[0]}"));
            $query->whereDate('a.followup_date', '>=', trim($startdate));
        }
        if (!empty($enddate)) {
            $edate = explode('-', $enddate);
            $enddate = date('Y-m-d', strtotime("{$edate[2]}-{$edate[1]}-{$edate[0]}"));
            $query->whereDate('a.followup_date', '<=', trim($enddate));
        }
        
        if (!empty($request->q)) {
            $search = $request->q;
            $query->where(function ($q) use ($search) {
                $q->where('a.followup_message', 'like', "%{$search}%")
                ->orWhere(DB::raw('LOWER(CASE a.is_type WHEN 1 THEN "unpaid" WHEN 2 THEN "un-returned" END)'), 'like', '%' . strtolower($search) . '%');
            });
        }

        $totalRecords = DB::table('business_followups')->count();
        $totalFiltered = $query->count();
        $limit = $request->input('length');
        $start = $request->input('start');
        $query->skip($start)->take($limit);
        $orderColumnIndex = $request->input('order.0.column');
        $orderDirection = $request->input('order.0.dir', 'asc');

        $columns = [
            0 => null,
            1 => 'date_time',
            2 => 'typeData',
            3 => 'message_description',
        ];

        $orderColumn = $columns[$orderColumnIndex] ?? null;
        if (!empty($orderColumn)) {
            $query->orderBy($orderColumn, $orderDirection);
        } else {
            $query->orderByDesc('a.followup_date');
        }

        $followups = $query->get();
        $data = [];
        $i = $start + 1;

        foreach ($followups as $row) {
            $data[] = [
                'no' => $i++,
                'date_time' => $row->date_time ? date('d-m-Y h:i A', strtotime($row->date_time)) : ' ',
                'typeData' => $row->typeData ?? ' ',
                'message_description' => $row->message_description ?? ' ',
            ];
        }

        return response()->json([
            'draw' => intval($request->input('draw')),
            'recordsTotal' => $totalRecords,
            'recordsFiltered' => $totalFiltered,
            'data' => $data,
        ]);
    }

    public function mytasklistdestroy(Request $request, $id)
    {
        if (! \Hash::check($request->password, auth()->user()->password)) {
            return response()->json(['message' => 'Invalid password.'], 422);
        }

        Business::where('id', $id)
            ->update(['is_active' => 3,
                'date_archived' => date('Y-m-d H:i:s'),
            ]);
        
        $businessdata = DB::table('businesses')
            ->where('id', $id)->select('trustmark_id','evaluator_id', 'status')
            ->first();
        DB::table('business_performance')->insert(
            [
            'busn_id'   => $id,
            'year'      => date('Y'),
            'user_id'   => $businessdata->evaluator_id,
            'process'   => "ARCHIVED",
            'process_date'     => now(),
        ]);
        $lat = $request->lat;
        $long = $request->long;
        $remark = '';
        $message = Auth::user()->name.' archived the application with Sec-No.'.$businessdata->trustmark_id.' dated '.date('Y-m-d H:i:s');
        saveUserLogs($lat, $long, $id, 14, 'archived', $message, $businessdata->status, $remark);

        return response()->json(['message' => 'Business Archived updated successfully.']);
    }

    public function savelogview(Request $request)
    {
        $id = $request->business_id;
        $businessdata = DB::table('businesses')
            ->where('id', $id)->select('trustmark_id', 'status')
            ->first();
        $lat = $request->lat;
        $long = $request->long;
        $remark = '';
        $message = Auth::user()->name.'  view the application with Sec-No. '.$businessdata->trustmark_id.' dated '.date('Y-m-d H:i:s');
        saveUserLogs($lat, $long, $id, 13, 'view', $message, $businessdata->status, $remark);

        return response()->json(['success' => true, 'message' => 'user log saved successfully.']);
    }

    public function bulkassigment(Request $request)
    {
        $ids = $request->ids;
        $evaluator_id = $request->evaluator_id;
        if (! $ids) {
            return response()->json(['message' => 'No IDs selected'], 400);
        }

        $idsArray = explode(',', $ids);

        try {
            Business::whereIn('id', $idsArray)
                ->update(['evaluator_id' => $evaluator_id]);

            return response()->json([
                'message' => 'Status updated successfully',
                'updated_ids' => $idsArray,
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Something went wrong',
                'error' => $e->getMessage(),
            ], 500);
        }
    }
    public function updateComplianceStatus(Request $request)
    {
        $busnId = $request->input('busn_id');
        $field  = $request->input('field');
        $value  = $request->input('value');

        $allowedFields = [
            'busn_type_is_compliance',
            'busn_name_is_compliance',
            'busn_trade_is_compliance',
            'busn_category_is_compliance',
            'busn_regno_is_compliance',
            'tin_is_compliance',
            'url_is_compliance',
            'authrep_name_is_compliance',
            'authrep_mobile_is_compliance',
            'authrep_email_is_compliance',
            'authrep_govtid_is_compliance',
            'authrep_govtid_doc_is_compliance',
            'authrep_govtid_expiry_is_compliance',
            'add_comp_is_compliance',
            'add_barangay_is_compliance',
            'add_muncity_is_compliance',
            'add_province_is_compliance',
            'add_region_is_compliance',
            'doc_busnreg_is_compliance',
            'doc_bir_is_compliance',
            'doc_irm_is_compliance',
            'doc_bmbe_is_compliance',
            'asset_category_is_compliance',
            'asset_valuation_is_compliance',
            'doc_addpermit_is_compliance'
        ];

        if (!in_array($field, $allowedFields)) {
            return response()->json(['success' => false, 'message' => 'Invalid field'], 400);
        }
        $compliance = DB::table('business_compliance')->where('busn_id', $busnId)->first();
        $business = DB::table('businesses')->where('id', $busnId)->first();

        if ($compliance) {
            $remarksField = str_replace('_is_compliance', '_remarks', $field);
            $updateData = [
                $field => $value,
                'evaluator_id' => $business->evaluator_id,
            ];
        
            if ($value == 0) {
                $updateData[$remarksField] = null;
            }
        
            DB::table('business_compliance')
                ->where('busn_id', $busnId)
                ->update($updateData);
        } else {
            DB::table('business_compliance')->insert([
                'busn_id'       => $busnId,
                $field          => $value,
                'year'          => date('Y'),
                'evaluator_id'  => $business->evaluator_id,
                'created_by'    => Auth::id(),
                'created_date'  => now(),
                'modified_by'   => Auth::id(),
                'modified_date' => now(),
            ]);
        }

        return response()->json([
            'success' => true,
            'message' => ucfirst(str_replace('_', ' ', $field)) . ' updated to ' . $value
        ]);
    }
    public function updateRemarks(Request $request)
    {
        $busnId = $request->busn_id;
        $compliance = DB::table('business_compliance')->where('busn_id', $busnId)->first();

        if (!$compliance) {
            return response()->json(['success' => false, 'message' => 'Compliance record not found.']);
        }
        $historyData = (array) $compliance; 
        $data = $request->except(['_token', 'busn_id']);
        DB::table('business_compliance')->where('busn_id', $busnId)->update($data);
        
        // foreach ($data as $field => $value) {
        //     if (str_ends_with($field, '_remarks')) {
        //         $isComplianceField = str_replace('_remarks', '_is_compliance', $field);
        //         $data[$isComplianceField] = 1;
        //     }
        // }

        // $historyData = $data;
        // $historyData['compliance_id'] = $compliance->id;
        // $historyData['year'] = date('Y');
        // DB::table('business_compliance_history')->updateOrInsert(
        //     ['busn_id' => $busnId], 
        //     $historyData 
        // );
        return response()->json(['success' => true]);
    }
    public function getRemarks(Request $request)
    {
        $busnId = $request->busn_id;
        $business_compliance = DB::table('business_compliance')->where('busn_id', $busnId)->first();
        $business = DB::table('businesses')->where('id', $busnId)->first();
        $regLabel = ''; 

        if ($business) {
            switch ($business->corporation_type) {
                case 1:
                    $regLabel = 'Business Info: DTI Registration No.';
                    break;
                case 2:
                    $regLabel = 'Business Info: SEC Registration No.';
                    break;
                case 3:
                    $regLabel = 'Business Info: CDA Registration No.';
                    break;
            }
        }
        $complianceFields = [
            'busn_type_is_compliance' => 'Business Info: Business Type',
            'busn_name_is_compliance' => 'Business Info: Business Name',
            'busn_trade_is_compliance' => 'Business Info: Trade Name',
            'busn_category_is_compliance' => 'Business Info: Business Category',
            'busn_regno_is_compliance' => $regLabel,
            'tin_is_compliance' => 'Business Info: Taxpayers Identification No.(TIN)',
            'url_is_compliance' => 'Business URL | Website | Social Media Platform Link',
            'authrep_name_is_compliance' => 'Authorized Representative: Name',
            'authrep_mobile_is_compliance' => 'Authorized Representative: Mobile No.',
            'authrep_email_is_compliance' => 'Authorized Representative: Email',
            'authrep_govtid_is_compliance' => 'Authorized Representative: Gov-Issued ID',
            'authrep_govtid_doc_is_compliance' => 'Authorized Representative: Gov-Issued Attachment',
            'authrep_govtid_expiry_is_compliance' => 'Authorized Representative: Gov-Issued ID Expiry Date',
            'add_comp_is_compliance' => 'Address Info: Complete Address',
            'add_barangay_is_compliance' => 'Address Info: Barangay',
            'add_muncity_is_compliance' => 'Address Info: Municipality',
            'add_province_is_compliance' => 'Address Info: Province',
            'add_region_is_compliance' => 'Address Info: Region',
            'doc_busnreg_is_compliance' => 'Attachments: Business Registration',
            'doc_bir_is_compliance' => 'Attachments: BIR 2303',
            'doc_irm_is_compliance' => 'Attachments: Internal Redress Mechanism',
            'doc_bmbe_is_compliance' => 'Attachments: BMBE',
            'asset_category_is_compliance' => 'Attachments: Business Category (based on Asset Size)',
            'asset_valuation_is_compliance' => 'Attachments: Proof of Total Asset Valuation',
            'doc_addpermit_is_compliance' => 'Additional Permits (For Regulated Products)',
        ];

        $html = '';
        $no = 1;
        foreach ($complianceFields as $field => $label) {
            if (optional($business_compliance)->{$field} == 1) {
                $remarksField = str_replace('_is_compliance', '_remarks', $field);
                $remarksValue = optional($business_compliance)->{$remarksField};

                $html .= '<tr>
                    <td>' . e($no) . '</td>
                    <td>' . e($label) . '</td>
                    <td style="padding:5px;">
                        <textarea class="form-control custom-input" name="' . e($remarksField) . '" id="' . e($remarksField) . '" cols="30" rows="1">' . e($remarksValue) . '</textarea>
                    </td>
                </tr>';
                $no++;
            }
        }
        
        return response()->json(['html' => $html,
        'complianceFieldsData' => [
            'busn_type_is_compliance' => $business_compliance->busn_type_is_compliance ?? 0,
            'busn_name_is_compliance' => $business_compliance->busn_name_is_compliance ?? 0,
            'busn_trade_is_compliance' => $business_compliance->busn_trade_is_compliance ?? 0,
            'busn_category_is_compliance' => $business_compliance->busn_category_is_compliance ?? 0,
            'busn_regno_is_compliance' => $business_compliance->busn_regno_is_compliance ?? 0,
            'busn_regno_is_compliance' => $business_compliance->busn_regno_is_compliance ?? 0,
            'tin_is_compliance' => $business_compliance->tin_is_compliance ?? 0,
            'url_is_compliance' => $business_compliance->url_is_compliance ?? 0,
            'authrep_name_is_compliance' => $business_compliance->authrep_name_is_compliance ?? 0,
            'authrep_mobile_is_compliance' => $business_compliance->authrep_mobile_is_compliance ?? 0,
            'authrep_email_is_compliance' => $business_compliance->authrep_email_is_compliance ?? 0,
            'authrep_govtid_is_compliance' => $business_compliance->authrep_govtid_is_compliance ?? 0,
            'authrep_govtid_doc_is_compliance' => $business_compliance->authrep_govtid_doc_is_compliance ?? 0,
            'authrep_govtid_expiry_is_compliance' => $business_compliance->authrep_govtid_expiry_is_compliance ?? 0,
            'add_comp_is_compliance' => $business_compliance->add_comp_is_compliance ?? 0,
            'add_barangay_is_compliance' => $business_compliance->add_barangay_is_compliance ?? 0,
            'add_muncity_is_compliance' => $business_compliance->add_muncity_is_compliance ?? 0,
            'add_province_is_compliance' => $business_compliance->add_province_is_compliance ?? 0,
            'add_region_is_compliance' => $business_compliance->add_region_is_compliance ?? 0,
            'doc_busnreg_is_compliance' => $business_compliance->doc_busnreg_is_compliance ?? 0,
            'doc_bir_is_compliance' => $business_compliance->doc_bir_is_compliance ?? 0,
            'doc_irm_is_compliance' => $business_compliance->doc_irm_is_compliance ?? 0,
            'doc_bmbe_is_compliance' => $business_compliance->doc_bmbe_is_compliance ?? 0,
            'asset_category_is_compliance' => $business_compliance->asset_category_is_compliance ?? 0,
            'asset_valuation_is_compliance' => $business_compliance->asset_valuation_is_compliance ?? 0,
            'doc_addpermit_is_compliance' => $business_compliance->doc_addpermit_is_compliance ?? 0,
        ],
        'remarksFieldsData' => [
            'busn_type_remarks' => $business_compliance->busn_type_remarks ?? '',
            'busn_name_remarks' => $business_compliance->busn_name_remarks ?? '',
            'busn_trade_remarks' => $business_compliance->busn_trade_remarks ?? '',
            'busn_category_remarks' => $business_compliance->busn_category_remarks ?? '',
            'busn_regno_remarks' => $business_compliance->busn_regno_remarks ?? '',
            'tin_remarks' => $business_compliance->tin_remarks ?? '',
            'url_remarks' => $business_compliance->url_remarks ?? '',
            'authrep_name_remarks' => $business_compliance->authrep_name_remarks ?? '',
            'authrep_mobile_remarks' => $business_compliance->authrep_mobile_remarks ?? '',
            'authrep_email_remarks' => $business_compliance->authrep_email_remarks ?? '',
            'authrep_govtid_remarks' => $business_compliance->authrep_govtid_remarks ?? '',
            'authrep_govtid_doc_remarks' => $business_compliance->authrep_govtid_doc_remarks ?? '',
            'authrep_govtid_expiry_remarks' => $business_compliance->authrep_govtid_expiry_remarks ?? '',
            'add_comp_remarks' => $business_compliance->add_comp_remarks ?? '',
            'add_barangay_remarks' => $business_compliance->add_barangay_remarks ?? '',
            'add_muncity_remarks' => $business_compliance->add_muncity_remarks ?? '',
            'add_province_remarks' => $business_compliance->add_province_remarks ?? '',
            'add_region_remarks' => $business_compliance->add_region_remarks ?? '',
            'doc_busnreg_remarks' => $business_compliance->doc_busnreg_remarks ?? '',
            'doc_bir_remarks' => $business_compliance->doc_bir_remarks ?? '',
            'doc_irm_remarks' => $business_compliance->doc_irm_remarks ?? '',
            'doc_bmbe_remarks' => $business_compliance->doc_bmbe_remarks ?? '',
            'asset_category_remarks' => $business_compliance->asset_category_remarks ?? '',
            'asset_valuation_remarks' => $business_compliance->asset_valuation_remarks ?? '',
            'doc_addpermit_remarks' => $business_compliance->doc_addpermit_remarks ?? '',
            
        ],]);
    }
    public function getRemarksHistory(Request $request)
    {
        $busnId = $request->busn_id;
        $business_compliance = DB::table('business_compliance')->where('busn_id', $busnId)->first();
        $compliance_id = $business_compliance->id;
        $business_compliance = DB::table('business_compliance_history')
        ->where('compliance_id', $compliance_id)
        ->where('busn_id', $busnId)
        ->first();
        $business = DB::table('businesses')->where('id', $busnId)->first();
        $regLabel = ''; 

        if ($business) {
            switch ($business->corporation_type) {
                case 1:
                    $regLabel = 'Business Info: DTI Registration No.';
                    break;
                case 2:
                    $regLabel = 'Business Info: SEC Registration No.';
                    break;
                case 3:
                    $regLabel = 'Business Info: CDA Registration No.';
                    break;
            }
        }
        $complianceFields = [
            'busn_type_is_compliance' => 'Business Info: Business Type',
            'busn_name_is_compliance' => 'Business Info: Business Name',
            'busn_trade_is_compliance' => 'Business Info: Trade Name',
            'busn_category_is_compliance' => 'Business Info: Business Category',
            'busn_regno_is_compliance' => $regLabel,
            'tin_is_compliance' => 'Business Info: Taxpayers Identification No.(TIN)',
            'url_is_compliance' => 'Business URL | Website | Social Media Platform Link',
            'authrep_name_is_compliance' => 'Authorized Representative: Name',
            'authrep_mobile_is_compliance' => 'Authorized Representative: Mobile No.',
            'authrep_email_is_compliance' => 'Authorized Representative: Email',
            'authrep_govtid_is_compliance' => 'Authorized Representative: Gov-Issued ID',
            'authrep_govtid_doc_is_compliance' => 'Authorized Representative: Gov-Issued Attachment',
            'authrep_govtid_expiry_is_compliance' => 'Authorized Representative: Gov-Issued ID Expiry Date',
            'add_comp_is_compliance' => 'Address Info: Complete Address',
            'add_barangay_is_compliance' => 'Address Info: Barangay',
            'add_muncity_is_compliance' => 'Address Info: Municipality',
            'add_province_is_compliance' => 'Address Info: Province',
            'add_region_is_compliance' => 'Address Info: Region',
            'doc_busnreg_is_compliance' => 'Attachments: Business Registration',
            'doc_bir_is_compliance' => 'Attachments: BIR 2303',
            'doc_irm_is_compliance' => 'Attachments: Internal Redress Mechanism',
            'doc_bmbe_is_compliance' => 'Attachments: BMBE',
            'asset_category_is_compliance' => 'Attachments: Business Category (based on Asset Size)',
            'asset_valuation_is_compliance' => 'Attachments: Proof of Total Asset Valuation',
            'doc_addpermit_is_compliance' => 'Additional Permits (For Regulated Products)',
        ];

        $html = '';
        $no = 1;
        $hasData = false;
        foreach ($complianceFields as $field => $label) {
            if (optional($business_compliance)->{$field} == 1) {
                $remarksField = str_replace('_is_compliance', '_remarks', $field);
                $remarksValue = optional($business_compliance)->{$remarksField};

                $html .= '<tr>
                    <td>' . e($no) . '</td>
                    <td>' . e($label) . '</td>
                    <td style="padding:5px;">' . e($remarksValue) . '</td>
                </tr>';
                $no++;
                $hasData = true;
            }
        }
        if (!$hasData) {
            $html .= '<tr>
                <td colspan="3" class="text-center" style="padding:5px;text-align: center !important;">No data found</td>
            </tr>';
        }
        return response()->json(['html' => $html ]);
    }
    public function tabCorporations($id)
    {
        $business = Business::findOrFail($id);
        $user = DB::table('users')
            ->where('id', $business->user_id)
            ->first();
        $business_compliance = DB::table('business_compliance')->where('busn_id', $business->id)->first();
        $currentUserId = Auth::id();
        $isAdmin = DB::table('user_admins')
            ->where('user_id', $currentUserId)
            ->value('is_admin');
        return view('business.tab_corporations', compact('business','isAdmin', 'business_compliance','user'));
    }
    public function tabDocument($id)
    {
        $business = Business::findOrFail($id);
        $user = DB::table('users')
            ->where('id', $business->user_id)
            ->first();
        $AdditionalDocuments = DB::table('business_documents')
        ->where('busn_id', $business->id)
        ->where('year', now()->year)
        ->get();
        $business_category = DB::table('business_category')
        ->get();
        $currentUserId = Auth::id();
        $isAdmin = DB::table('user_admins')
            ->where('user_id', $currentUserId)
            ->value('is_admin');
        $business_compliance = DB::table('business_compliance')->where('busn_id', $business->id)->first();
        $businessCatName  = DB::table('application_fee_category')->where('busn_category_id',$business->busn_category_id)->first();
        return view('business.tab_documents', compact('business','isAdmin','businessCatName','business_category','business_compliance','user','AdditionalDocuments'));
    }
    public function getMonthlyPendingSummary(Request $request)
    {
        $data = DB::table('businesses as a')
            ->selectRaw("DATE_FORMAT(a.submit_date, '%Y - %M') as Month, COUNT(a.id) as Pending")
            ->where('a.is_active', 1)
            ->where('a.status', '<>', 'DRAFT')
            ->whereRaw('IFNULL(a.payment_id, 0) = 0')
            ->whereRaw('IFNULL(a.evaluator_id, 0) = 0')
            ->whereNotNull('a.submit_date')
            ->groupBy(DB::raw("DATE_FORMAT(a.submit_date, '%Y - %M')"))
            ->orderBy('a.submit_date', 'ASC')
            ->get();

        return response()->json($data);
    }
    
    public function getMonthlyPendingSummaryEvaluator_id(Request $request)
    {
        $evaluator_id = $request->evaluator_id;
        $data = DB::table('businesses as a')
            ->selectRaw("DATE_FORMAT(a.submit_date, '%Y - %M') as Month, COUNT(a.id) as Pending")
            ->where('payment_id', null)
            ->where('a.is_active', 1)
            ->where('a.evaluator_id', $evaluator_id)
            ->groupBy(DB::raw("DATE_FORMAT(a.submit_date, '%Y - %M')"))
            ->orderBy('a.submit_date', 'ASC')
            ->get();

        return response()->json($data);
    }
    public function getPlatformDetails(Request $request)
    {
        $baseUrl = $request->input('base_url');

        $data = DB::table('platform_url as a')
            ->select(
                'a.platform_name as platform_name',
                DB::raw("CASE a.with_irm WHEN 0 THEN 'No' WHEN 1 THEN 'Yes' END as with_irm")
            )
            ->where('a.is_active', 1)
            ->where(function($q) use ($baseUrl) {
                $q->where('a.base_url', 'LIKE', "%{$baseUrl}%");
            })
            ->first();

        if ($data) {
            return response()->json($data);
        } else {
            return response()->json([
                'platform_name' => '',
                'with_irm' => ''
            ]);
        }
    }
    public function generateInternalRedCertificate($id)
    {
        $business_irm = DB::table('business_irm')->where('busn_id',$id)->first();
        // dd($business_irm);exit;
        $business = Business::findOrFail($business_irm->busn_id);
        $type_corporations = DB::table('type_corporations')->where('id',$business->corporation_type)->first();
        $pdf = new \TCPDF(PDF_PAGE_ORIENTATION, PDF_UNIT, 'A4', true, 'UTF-8', false);
        $pdf->SetCreator(PDF_CREATOR);
        $pdf->SetAuthor('Trustmark');
        $pdf->SetTitle('INTERNAL REDRESS MECHANISM POLICY');
        $pdf->SetMargins(10, 10, 10);
        $pdf->SetAutoPageBreak(TRUE, 10);
        $pdf->SetPrintHeader(false);
        $pdf->SetPrintFooter(false);
        $pdf->AddPage();
        $html = view('business.internal_red_certificate', compact('business','business_irm','type_corporations'))->render();
        $pdf->writeHTML($html, true, false, true, false, '');
        $pdf->AddPage();
        $pdf->SetFont('dejavusans', '', 11);
        $complaintHtml = <<<EOD
            <style>
                h3, h4 { text-align: center; font-weight: bold; }
                table td { font-size: 9px; vertical-align: top; }
                .section-title { font-weight: bold; margin-top: 10px; }
            </style>
            
            <div style="text-align:center; font-size:12px; margin-bottom:5px;">Annex A</div>
            <h3>CUSTOMER COMPLAINT FORM</h3>
            
            <p><b>Business Name:</b> </p>
            
            <p class="section-title">CUSTOMER DETAILS</p>
            <table width="100%" border="0" cellpadding="2">
            <tr><td width="30%">Name</td><td>: ____________________________________________</td></tr>
            <tr><td>Contact Number</td><td>: ____________________________________________</td></tr>
            <tr><td>Email Address</td><td>: ____________________________________________</td></tr>
            <tr><td>Invoice/Transaction Reference</td><td>: ____________________________________________</td></tr>
            </table>
            
            <p class="section-title">COMPLAINT DETAILS</p>
            <table width="100%" border="0" cellpadding="2">
            <tr><td width="40%">Date of Purchase/Transaction</td><td>: ___________________________</td></tr>
            <tr><td>Platform Used</td><td>: ___________________________</td></tr>
            <tr><td>Product/Service Concerned (Please specify Brand/Model)</td><td>: ___________________________</td></tr>
            </table>
            
            <p class="section-title">NATURE OF COMPLAINT (CHECK ALL THAT APPLY):</p>
            <table width="100%" border="0" cellpadding="1">
            <tr><td>&#9744; Damaged/Defective Item</td></tr>
            <tr><td>&#9744; Wrong Item Delivered</td></tr>
            <tr><td>&#9744; Late Delivery</td></tr>
            <tr><td>&#9744; Item Not Received</td></tr>
            <tr><td>&#9744; Poor Customer Service</td></tr>
            <tr><td>&#9744; Others (please specify): ___________________________</td></tr>
            </table>
            
            <p class="section-title">DETAILED DESCRIPTION OF COMPLAINT:</p>
            <table width="100%" border="1" cellpadding="10" cellspacing="0">
            <tr><td height="100"></td></tr>
            </table>
            
            <p class="section-title" style="color:#0070C0;">For Business Use Only (To be filled out by Company's Representative)</p>
            <table width="100%" border="0" cellpadding="2">
            <tr><td width="35%">Date Received</td><td>: ___________________________</td></tr>
            <tr><td>Received By</td><td>: ___________________________</td></tr>
            <tr><td>Action Taken</td><td>: ___________________________</td></tr>
            <tr><td>Resolution Provided On</td><td>: ___________________________</td></tr>
            <tr><td>Remarks</td><td>: ___________________________</td></tr>
            </table>
            EOD;
    
        $pdf->writeHTML($complaintHtml, true, false, true, false, '');
    
        return response($pdf->Output('INTERNAL_REDRESS_MECHANISM_POLICY_CERTIFICATE.pdf', 'I'))
            ->header('Content-Type', 'application/pdf');
    
    }
    public function saveirm(Request $request)
    {
        DB::table('business_irm')->updateOrInsert(
            [
                'busn_id'   => $request->input('busn_idIrm'), 
            ],
            [
            'year' => date('Y'),
            'irm_busn_phone_no' => $request->input('irm_busn_phone_no'),
            'irm_busn_email' => $request->input('irm_busn_email'),
            'social_media_page' => json_encode($request->social_media_page ?? []),
            'online_platform' => json_encode($request->online_platform ?? []),
            'messaging_apps' => json_encode($request->messaging_apps ?? []),
            'complaint_hour' => $request->input('complaint_hour'),
            'complaint_others' => $request->input('complaint_others'),
            'reso_hours' => $request->input('reso_hours'),
            'reso_others' => $request->input('reso_others'),
            'reso_not_limited_to' => json_encode($request->reso_not_limited_to ?? []),
            'authorized_rep' => $request->input('authorized_rep'),
            'authorized_rep_position' => $request->input('authorized_rep_position'),
            'busn_name' => $request->input('busn_name'),
            'created_by' => Auth::id(),
            'created_date' => now(),
        ]);

        return response()->json(['success' => true]);
    }
    
    public function generateStatmentOfAccutCertificate($id)
    {
        $business = Business::findOrFail($id);
        $type_corporations = DB::table('type_corporations')->where('id',$business->corporation_type)->first();
        $busines_fee = BusinessFees::where('busn_id', $id)->get();
        $pdf = new \TCPDF(PDF_PAGE_ORIENTATION, PDF_UNIT, 'A4', true, 'UTF-8', false);
        $pdf->SetCreator(PDF_CREATOR);
        $pdf->SetMargins(10, 10, 10);
        $pdf->SetAutoPageBreak(TRUE, 10);
        $pdf->SetPrintHeader(false);
        $pdf->SetPrintFooter(false);
        $pdf->AddPage();
        $pdf->SetAlpha(0.20);
        $logoPath = public_path('assets/img/DTI-BP-transparent-statement.png');
        $logo = 'data:image/png;base64,' . base64_encode(file_get_contents($logoPath));
        $pdf->Image(public_path('assets/img/trustmark_logo.PNG'), 26, 46, 160, 200);
        $pdf->SetAlpha(1);
        $barangays = DB::table('barangays')->select('id','brgy_description')->where('id',$business->barangay_id)->first();
        $complete_address = $business->complete_address.', '.$barangays->brgy_description;
        $html = view('business.certificate_statement', compact('business','type_corporations','busines_fee','logo','complete_address'))->render();
        $pdf->writeHTML($html, true, false, true, false, '');

        return response($pdf->Output('STATEMENT_CERTIFICATE.pdf', 'I'))
            ->header('Content-Type', 'application/pdf');
    }
    public function performance(Request $request){
        DB::table('business_performance')->insert(
            [
            'busn_id'   => $request->id,
            'year'      => date('Y'),
            'user_id'   => $request->evaluator_id,
            'process'   => $request->status,
            'process_date'     => now(),
        ]);
        return response()->json([
            'status'  => 'success',
            'message' => 'Business updated successfully!'
        ]);
    }

}
