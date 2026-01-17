<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Email;
use App\Models\PasswordOtp;
use App\Services\EmailTokenService;
use Illuminate\Http\Request;
use App\Models\RequirementReps;
use Illuminate\Validation\Rule;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Support\Facades\Storage;

class UserController extends Controller
{
    protected $user;

    protected $email;

    protected $emailTokenService;

    public function __construct(User $user, Email $email, EmailTokenService $emailTokenService)
    {
        $this->user = $user;
        $this->email = $email;
        $this->emailTokenService = $emailTokenService;
    }

    public function index()
    {
        $admins = User::where('role', 2)->where('is_active', 1)->get();

        return view('user.index', compact('admins'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'first_name' => 'required|string|max:255',
            'middle_name' => 'nullable|string|max:255',
            'last_name' => 'required|string|max:255',
            'suffix' => 'nullable|string|max:255',
            'email' => 'required|email|unique:users,email',
            'ctc_num' => 'required',
            'password' => 'required|min:8|confirmed',
        ]);

        try {

            $user = new User;
            $fullName = trim(
                $request->input('first_name').' '.
                    ($request->input('middle_name') ? $request->input('middle_name').' ' : '').
                    $request->input('last_name').
                    ($request->input('suffix') ? ', '.$request->input('suffix') : '')
            );
            $user->name = $fullName;
            $user->first_name = $request->first_name;
            $user->middle_name = $request->middle_name;
            $user->last_name = $request->last_name;
            $user->suffix = $request->suffix;
            $user->ctc_no = $request->ctc_num;
            $user->email = $request->email;
            $user->password = Hash::make($request->password);
            $user->role = 2;
            $user->is_active = 1;
            $user->created_by = Auth::id();

            $user->save();

            return redirect()->route('user.index')->with('success', 'Admin Created Successfully!');
            } catch (\Illuminate\Database\QueryException $e) {
                // Check for duplicate email error (MySQL error code 1062)
                if ($e->errorInfo[1] == 1062) {
                    return back()->withInput()->with('error', 'Email already exists.');
                }

                \Log::error('Failed to create user: '.$e->getMessage());
                return back()->withInput()->with('error', 'Something went wrong. Please try again.');
            } catch (\Exception $e) {
                \Log::error('Failed to create user: '.$e->getMessage());
                return back()->withInput()->with('error', 'Something went wrong. Please try again.');
            }
    }

    // public function savePermission(Request $request)
    // {
    //     $permissions = $request->input('permissions');
    //     $userId = $permissions[0]['user_id'] ?? null;

    //     if (! $userId) {
    //         return response()->json(['status' => 'error', 'message' => 'Missing user_id'], 400);
    //     }
    //     $selectedModuleIds = collect($permissions)->pluck('menu_module_id')->toArray();
    //     DB::table('menu_permissions')
    //         ->where('user_id', $userId)
    //         ->whereNotIn('menu_module_id', $selectedModuleIds)
    //         ->delete();

    //     // 2. Insert or update the selected permissions
    //     foreach ($permissions as $perm) {
    //         DB::table('menu_permissions')->updateOrInsert(
    //             [
    //                 'user_id' => $perm['user_id'],
    //                 'menu_group_id' => $perm['menu_group_id'],
    //                 'menu_module_id' => $perm['menu_module_id'],
    //             ],
    //             ['created_at' => now(), 'updated_at' => now(), 'created_by' => Auth::id(), 'updated_by' => Auth::id()]
    //         );
    //     }

    //     return response()->json(['status' => 'success']);
    // }
    
    public function savePermission(Request $request)
    {
        $permissions = $request->input('permissions', []);
        $userId = $permissions[0]['user_id'] ?? null;

        if (!$userId) {
            return response()->json(['status' => 'error', 'message' => 'Missing user_id'], 400);
        }

        $groupedPermissions = collect($permissions)->groupBy('menu_group_id');

        $allModuleIds = []; 
        $allGroupIdsWithNoModule = []; 

        foreach ($groupedPermissions as $groupId => $perms) {
            $modules = collect($perms)->pluck('menu_module_id')->filter(fn($id) => $id != 0)->toArray();

            if (empty($modules)) {
                DB::table('menu_permissions')->updateOrInsert(
                    [
                        'user_id' => $userId,
                        'menu_group_id' => $groupId,
                        'menu_module_id' => 0,
                    ],
                    [
                        'created_at' => now(),
                        'updated_at' => now(),
                        'created_by' => Auth::id(),
                        'updated_by' => Auth::id()
                    ]
                );
                $allGroupIdsWithNoModule[] = $groupId;
            } else {
                foreach ($modules as $moduleId) {
                    DB::table('menu_permissions')->updateOrInsert(
                        [
                            'user_id' => $userId,
                            'menu_group_id' => $groupId,
                            'menu_module_id' => $moduleId,
                        ],
                        [
                            'created_at' => now(),
                            'updated_at' => now(),
                            'created_by' => Auth::id(),
                            'updated_by' => Auth::id()
                        ]
                    );
                    $allModuleIds[] = $moduleId;
                }
            }
        }
        DB::table('menu_permissions')
            ->where('user_id', $userId)
            ->where('menu_module_id', '!=', 0)
            ->whereNotIn('menu_module_id', $allModuleIds)
            ->delete();
        DB::table('menu_permissions')
            ->where('user_id', $userId)
            ->where('menu_module_id', 0)
            ->whereNotIn('menu_group_id', $allGroupIdsWithNoModule)
            ->delete();

        return response()->json(['status' => 'success']);
    }


    public function profile(Request $request)
    {
        $user = User::where('id', Auth::id())
            ->first();

        $requirements = RequirementReps::where('status', 'Active')->get();

        return view('user.profile', compact('user', 'requirements'));
    }

    public function create(Request $request)
    {
        return view('user.create');
    }

    public function admin_profile($id)
    {
        $id = Crypt::decrypt($id);
        $user = User::findOrFail($id);
        $modules = DB::table('menu_groups as mg')
            ->leftJoin('menu_modules as mm', 'mm.menu_group_id', '=', 'mg.id')
            ->select(
                'mg.id as group_id',
                'mg.name as group_name',
                'mm.id as module_id',
                'mm.name as module_name'
            )
            ->orderBy('mg.name')
            ->orderBy('mm.name')
            ->get()
            ->groupBy('group_id');
        $assignedModuleIds = DB::table('menu_permissions')
            ->where('user_id', $id)
            ->where('menu_module_id', '!=', 0)
            ->pluck('menu_module_id')
            ->toArray();
        $assignedGroupIdsWithoutModules = DB::table('menu_permissions')
            ->where('user_id', $id)
            ->where('menu_module_id', 0)
            ->pluck('menu_group_id')
            ->toArray();
        $user_admins = DB::connection('mysql')
            ->table('user_admins')
            ->where('user_id', $id)
            ->first();

        return view('user.view', compact(
            'user', 
            'modules', 
            'assignedModuleIds', 
            'assignedGroupIdsWithoutModules', 
            'user_admins'
        ));
    }


    public function admin_update(Request $request, $id)
    {
        $user = User::findOrFail($id);

        $request->validate([
            'first_name' => 'required|string|max:255',
            'middle_name' => 'nullable|string|max:255',
            'last_name' => 'required|string|max:255',
            'suffix' => 'nullable|string|max:255',
            'email' => 'required|email',
            'ctc_no' => 'required',
            'password' => 'nullable|min:8|confirmed',
            'profile_photos' => 'nullable|image|mimes:png|max:2048',
        ]);

        try {
            $fullName = trim(
                $request->first_name.' '.
                ($request->middle_name ? $request->middle_name.' ' : '').
                $request->last_name.
                ($request->suffix ? ', '.$request->suffix : '')
            );
            $updateData = [
                'name' => $fullName,
                'first_name' => $request->first_name,
                'middle_name' => $request->middle_name,
                'last_name' => $request->last_name,
                'suffix' => $request->suffix,
                'ctc_no' => $request->ctc_no,
                'email' => $request->email,
                'is_primary' => $request->is_primary ? 1 : 0,
                'updated_by' => Auth::id(),
            ];
            if ($request->is_primary == 1) {
                DB::table('users')
                    ->where('id', '!=', $id)
                    ->update(['is_primary' => 0]);
            }
            if ($request->filled('password')) {
                $updateData['password'] = Hash::make($request->password);
            }
            if ($request->filled('profile_photos_base64')) {
                $imageData = $request->input('profile_photos_base64');
                $base64Str = preg_replace('#^data:image/\w+;base64,#i', '', $imageData);

                $filename = 'signature_'.time().'.png';
                $path = 'signatures/'.$filename;
                Storage::disk('public')->put($path, base64_decode($base64Str));
                $updateData['profile_photos'] = $path;
                if ($user->profile_photos && Storage::disk('public')->exists($user->profile_photos)) {
                    Storage::disk('public')->delete($user->profile_photos);
                }
            }
            $user->update($updateData);
            if ($request->is_admin == 1 || $request->is_evaluator == 1) {
                DB::connection('mysql')
                    ->table('user_admins')
                    ->updateOrInsert(
                        ['user_id' => $id],
                        [
                            'is_admin' => $request->is_admin ? 1 : 0,
                            'is_evaluator' => $request->is_evaluator ? 1 : 0,
                            'created_by' => Auth::id(),
                            'created_date' => now(),
                            'modified_by' => Auth::id(),
                            'modified_date' => now(),
                        ]
                    );
            } else {
                DB::connection('mysql')
                    ->table('user_admins')
                    ->where('user_id', $id)
                    ->delete();
            }

            return redirect()->route('user.index')->with('success', 'Updated successfully!');
        } catch (\Exception $e) {
            \Log::error('Update Profile Error: '.$e->getMessage());

            return back()->withInput()->with('error', 'Something went wrong. Please try again.');
        }
    }
    // public function admin_update(Request $request, $id)
    // {
    //     $user = User::findOrFail($id);

    //     $request->validate([
    //         'first_name' => 'required|string|max:255',
    //         'middle_name' => 'nullable|string|max:255',
    //         'last_name' => 'required|string|max:255',
    //         'suffix' => 'nullable|string|max:255',
    //         'email' => 'required|email',
    //         'ctc_no' => 'required',
    //         'password' => 'nullable|min:8|confirmed',
    //     ]);

    //     try {

    //         $fullName = trim(
    //             $request->input('first_name') . ' ' .
    //                 ($request->input('middle_name') ? $request->input('middle_name') . ' ' : '') .
    //                 $request->input('last_name') .
    //                 ($request->input('suffix') ? ', ' . $request->input('suffix') : '')
    //         );

    //         $user->update([
    //             'name' => $fullName,
    //             'first_name' => $request->first_name,
    //             'middle_name' => $request->middle_name,
    //             'last_name' => $request->last_name,
    //             'suffix' => $request->suffix,
    //             'ctc_no' => $request->ctc_no,
    //             'email' => $request->email,
    //             'password' => Hash::make($request->password),
    //             'updated_by' => Auth::id(),
    //         ]);

    //         return redirect()->route('user.index')->with('success', 'Update Successfully!');
    //     } catch (\Exception $e) {
    //         \Log::error('Update Profile Error: ' . $e->getMessage());
    //         return back()->withInput()->with('error', 'Something went wrong. Please try again.');
    //     }
    // }

    public function applicant_update(Request $request, $id)
    {
        $user = User::findOrFail($id);

        $request->validate([
            'first_name' => 'required|string|max:255',
            'middle_name' => 'nullable|string|max:255',
            'last_name' => 'required|string|max:255',
            'suffix' => 'nullable|string|max:255',
            'email' => 'required|email',
            'ctc_no' => 'required',
            'password' => 'nullable|min:8|confirmed',
            'issued_id' => 'required',
            'req_upload' => 'nullable|file|mimes:jpg,jpeg,png,pdf|max:10240',
            'expired_date' => ['nullable', 'date'],
        ]);

        $requirements = RequirementReps::where('status', 'Active')->get();
        // Add conditionally required rule for expired_date
        if ($request->issued_id) {
            // Find the requirement with the given id
            $requirement = $requirements->firstWhere('id', $request->issued_id);

            if ($requirement && trim($requirement->with_expiration) === '1') {
                $request->validate([
                    'expired_date' => 'required|date|after_or_equal:'.now()->format('Y-m-d'),
                ]);
            }
        }

        try {

            $fullName = trim(
                $request->input('first_name').' '.
                    ($request->input('middle_name') ? $request->input('middle_name').' ' : '').
                    $request->input('last_name').
                    ($request->input('suffix') ? ', '.$request->input('suffix') : '')
            );

            $data = [
                'name' => $fullName,
                'first_name' => $request->first_name,
                'middle_name' => $request->middle_name,
                'last_name' => $request->last_name,
                'suffix' => $request->suffix,
                'ctc_no' => $request->ctc_no,
                'email' => $request->email,
                'updated_by' => Auth::id(),
                'requirement_id' => $request->issued_id,
                'requirement_expired' => $request->expired_date,
            ];

            if ($request->filled('password')) {
                $data['password'] = Hash::make($request->password);
            }

            if (empty($user->requirement_upload)) {
                $rules['req_upload'] = 'required|file|mimes:jpg,jpeg,png,pdf|max:10240'; // max 10MB
            } else {
                $rules['req_upload'] = 'nullable|file|mimes:jpg,jpeg,png,pdf|max:10240';
            }

            $request->validate($rules);

            if ($request->hasFile('req_upload')) {
                $file = $request->file('req_upload');
                $originalName = $file->getClientOriginalName();
                $fileNameWithoutExt = pathinfo($originalName, PATHINFO_FILENAME);
                $extension = $file->getClientOriginalExtension();

                $timestamp = now()->format('YmdHis');
                $fileName = $timestamp.'_'.$fileNameWithoutExt.'.'.$extension;
                $req_upload_path = $file->storeAs('document-upload/requirement_reps', $fileName, 'public');

                // Delete old file if exists
                if ($user->requirement_upload) {
                    Storage::disk('public')->delete($user->requirement_upload);
                }

                $data['requirement_upload'] = $req_upload_path;
            } else {
                // No new file uploaded, keep existing path
                $data['requirement_upload'] = $user->requirement_upload;
            }

            $user->update($data);

            return redirect()->route('dashboard')->with('success', 'Update Successfully!');
        } catch (\Exception $e) {
            \Log::error('Update Profile Error: '.$e->getMessage());

            return back()->withInput()->with('error', 'Something went wrong. Please try again.');
        }
    }

    public function destroy($id)
    {
        $user = User::findOrFail($id);
        $user->is_active = 0;
        $user->save();

        return redirect()->route('user.index')->with('success', 'User deleted successfully.');
    }

    public function sendOtp(Request $request)
    {
        // $request->validate(['email' => 'required|email|exists:users,email']);

        // $user = User::where('email', $request->email)->first();

        $request->validate([
            'email' => [
                'required',
                'email',
                Rule::exists('project1.users', 'email'), // <- point to project1 connection
            ],
        ]);

        // Check if we can send OTP email using email token service
        $countdown = $this->emailTokenService->getCountdownMessage(
            $request->email, 
            'Please wait before requesting another OTP.'
        );

        if (!$countdown['can_send']) {
            return redirect()->back()
                ->with('error', $countdown['message'])
                ->with('countdown_seconds', $countdown['seconds_remaining']);
        }

        $user = DB::connection('project1')->table('users')
            ->where('email', $request->email)
            ->first();

        // Create email token before sending OTP
        $emailToken = $this->emailTokenService->createEmailToken($request->email);

        $otp = rand(100000, 999999);

        PasswordOtp::updateOrCreate(
            ['email' => $request->email],
            ['otp' => $otp, 'expires_at' => now()->addMinutes(10)]
        );

        // DTI Email
        // $sendEmail = $this->user->apiSendOTPEmail($request->email, $otp, $user->name);
        // if (!$sendEmail->successful()) {
        //     return 'Email failed: ' . $sendEmail->json();
        // }

        // Mandrill
        $sendEmail = $this->email->sendMail('sendOtp', [
            'email' => $request->email,
            'otp' => $otp,
            'user' => $user,
        ]);

        // Log OTP send attempt
        Log::info('OTP email attempt', [
            'email' => $request->email,
            'otp' => $otp,
            'user' => $user ? $user->name : null,
            'token_id' => $emailToken->id,
            'status' => isset($sendEmail['error']) ? 'failed' : 'success',
            'error' => $sendEmail['error'] ?? null,
        ]);

        if (isset($sendEmail['error'])) {
            return 'Email failed: '.$sendEmail['error'];
        }

        return view('auth.verify_otp', compact('user'));
    }

    public function verifyOtp(Request $request)
    {
        $request->validate([
            'email' => 'required|email',
            'otp' => 'required|string',
        ]);

        $user = User::where('email', $request->email)->first();

        $otpRecord = PasswordOtp::where('email', $request->email)
            ->where('otp', $request->otp)
            ->first();

        if (! $otpRecord || $otpRecord->isExpired()) {
            // Instead of back(), redirect to the OTP form GET route
            return redirect()->route('login.otp.form', ['email' => $request->email])
                ->withInput()
                ->with('otp_error', 'Invalid or expired OTP.');
        } else {
            $otpRecord->delete();

            session(['otp_verified_email' => $request->email]);

            return view('auth.reset', compact('user'));
        }
    }

    public function resetPassword(Request $request)
    {
        $request->validate([
            'password' => 'required|confirmed|min:6',
        ]);

        $email = $request->email ?? session('otp_verified_email');
        if (! $email) {
            return response()->json(['error' => 'OTP verification required.'], 403);
        }

        $user = User::where('email', $email)->first();
        $user->password = Hash::make($request->password);
        $user->save();

        session()->forget('otp_verified_email'); // Clean up

        return redirect('/login')->with('success', 'Password reset successfully. You can now log in.');
    }

    public function download_authorized($id)
    {
        $user = User::findOrFail($id);

        if (! $user->requirement_upload) {
            abort(404, 'File not found');
        }

        // Remove 'storage/' prefix if present
        $fileRelativePath = str_replace('storage/', '', $user->requirement_upload);
        $filePath = storage_path('app/public/'.$fileRelativePath);

        if (! file_exists($filePath)) {
            $filePath = public_path('storage/'.$fileRelativePath);
            if (! file_exists($filePath)) {
                abort(404, 'File not found on server');
            }
        }

        $extension = pathinfo($filePath, PATHINFO_EXTENSION);
        $originalFilename = pathinfo($filePath, PATHINFO_FILENAME);

        $mimeTypes = [
            'pdf'  => 'application/pdf',
            'jpg'  => 'image/jpeg',
            'jpeg' => 'image/jpeg',
            'png'  => 'image/png',
        ];

        $contentType = $mimeTypes[strtolower($extension)] ?? 'application/octet-stream';
        $fileName = $originalFilename . '.' . $extension;

        // Serve the file inline so it opens in browser
        return response()->file($filePath, [
            'Content-Type' => $contentType,
            'Content-Disposition' => 'inline; filename="' . $fileName . '"'
        ]);
    }

}
