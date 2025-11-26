<?php

namespace App\Http\Controllers;

use App\Models\RequirementReps;
use App\Models\User;
use App\Services\EmailTokenService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\URL;
use App\Models\Email;

class RegisteredUserController extends Controller
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

    public function create()
    {

        if (env('USER_BASE_URL') === 'https://tm.bahayko.app') {
            //return redirect()->route('login');
        }
        $requirements = RequirementReps::where('status', 'Active')->get();

        return view('auth.register', compact('requirements'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'first_name' => 'required|string|max:255',
            'middle_name' => 'nullable|string|max:255',
            'last_name' => 'required|string|max:255',
            'suffix' => 'nullable|string|max:255',
            // 'username' => 'required|string|max:255|unique:users,username',
            'email' => 'required|email|unique:users,email',
            'ctc_num' => 'required',
            'password' => 'required|min:8|confirmed',
            'issued_id' => 'nullable',
            'req_upload' => 'nullable',
            'expired_date' => 'nullable',
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
            // Uncomment the next line if you want to use username
            // $user->username = $request->username;
            $user->ctc_no = $request->ctc_num;
            $user->email = $request->email;
            $user->password = Hash::make($request->password);
            $user->role = 1;
            $user->is_active = 1;
            $user->requirement_id = $request->issued_id;

            if ($request->hasFile('req_upload')) {
                $file = $request->file('req_upload');
                $originalName = pathinfo($file->getClientOriginalName(), PATHINFO_FILENAME);
                $extension = $file->getClientOriginalExtension();

                $filename = now()->format('YmdHis').'_'.preg_replace('/[^A-Za-z0-9_\-]/', '', $originalName).'.'.$extension;

                $path = $file->storeAs('document-upload/requirement_reps', $filename, 'public');

                $user->requirement_upload = $path;
            }

            // $user->requirement_upload = $request->req_upload;
            $user->requirement_expired = $request->expired_date;

            $user->save();

            // Create email token before sending verification email
            $emailToken = $this->emailTokenService->createEmailToken($user->email);

            $encryptedId = Crypt::encryptString($user->id);

            $signedUrl = URL::temporarySignedRoute(
                'verification.verify',
                now()->addMinutes(10), // 10 minutes expiration
                ['id' => $encryptedId, 'token' => $emailToken->token]
            );

            // DTI Email
            // $sendEmail = $this->user->apiSendEmailVerify($user->email, $signedUrl, $user->name);
            // if (!$sendEmail->successful()) {
            //     return 'Email failed: ' . $sendEmail->json();
            // }

            // Mandrill
            $sendEmail = $this->email->sendMail('emailVerification', [
                'user' => $user,
                'verificationUrl' => $signedUrl,
            ]);

            // Log email verification attempt
            Log::info('Email verification after register attempt', [
                'email' => $user->email ?? null,
                'name' => $user->name ?? null,
                'verificationUrl' => $signedUrl,
                'token_id' => $emailToken->id,
                'status' => isset($sendEmail['error']) ? 'failed' : 'success',
                'error' => $sendEmail['error'] ?? null,
            ]);

            if (isset($sendEmail['error'])) {
                return 'Email failed: '.$sendEmail['error'];
            }

            return redirect()->route('login')->with('success', 'Registration completed!');
        } catch (\Exception $e) {
            // Log the error or handle it accordingly
            \Log::error('Registration Error: '.$e->getMessage());

            return back()->withInput()->with('error', 'Something went wrong. Please try again.');
        }
    }
}
