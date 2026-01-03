<?php

namespace App\Http\Controllers;

use App\Models\Business;
use App\Models\Email;
use App\Models\User;
use App\Services\EmailTokenService;
use DB;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\URL;

class AuthenticatedSessionController extends Controller
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

    public function login()
    {
        $activate_registration = DB::connection('mysql')
            ->table('settings')->where('name', 'activate_registration')->first();

        $isMaintainace = 0;
        $setting = DB::connection('mysql')->table('settings')->where('name', 'maintenance_mode')->first();
        if ($setting && $setting->value == 1) {
            $isMaintainace = 1;
        }

        return view('auth.login', compact('activate_registration','isMaintainace') + [
            'project1_url' => config('sso.project1.url'),
        ]);
    }

    // server
    public function auth(Request $request)
    {
        $credentials = $request->validate([
            'email' => 'required|string',
            'password' => 'required|string',
        ]);

        $user = User::where('email', $credentials['email'])->first();
        $setting = DB::table('settings')->where('name', 'maintenance_mode')->first();
        if ($setting && $setting->value == 1) {
            return redirect()->back()->with('error', 'System maintenance is on-going.<br>We apologize for the inconvenience it caused.<br>
            <b style="font-size:15px;">Please come back after few minutes.</b>');
        }
        //echo "<pre>"; print_r($request->all()); exit;
        $latitude = $request->latitude; 
        $longitude = $request->longitude;
        if (Auth::attempt($credentials)) {
            $request->session()->regenerate();

            // Business::where('user_id', Auth::id())
            //     ->whereNull('corporation_type')
            //     ->delete();

            if ($user->email_verified_at === null) {
                // Check if we can send email verification using email token service
                $countdown = $this->emailTokenService->getCountdownMessage(
                    $user->email, 
                    'Please verify your email before logging-in.'
                );

                if (!$countdown['can_send']) {
                    Auth::logout();
                    $request->session()->invalidate();
                    $request->session()->regenerateToken();

                    return redirect()->back()
                        ->with('error', $countdown['message'])
                        ->with('countdown_seconds', $countdown['seconds_remaining']);
                }

                // Create email token before sending
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
                Log::info('Email verification attempt', [
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

                Auth::logout();
                $request->session()->invalidate();
                $request->session()->regenerateToken();

                return redirect()->back()->with('success', 'Please verify your email before logging in. Verification email sent.');
            }

            session()->flash('show_popup', true);
            $this->saveuserlogs($latitude,$longitude,$credentials['email']);
            return redirect()->intended('/');
        }

        return redirect()->back()->with('error', 'Email or password is incorrect.');
    }

    public function logout(Request $request)
    {
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect('/login');
    }

    public function forgot_password()
    {
        return view('auth.forgot_password');
    }

    public function saveuserlogs($latitude,$longitude,$username){
        $userlogs =array();
        if (Auth::check() && Auth::user()->role == 1) {
                $message ='Customer '.$username.' log-in successfully dated '.date('Y-m-d H:i:s');
        }else{
              $message ='Admin '.$username.' log-in successfully dated '.date('Y-m-d H:i:s');
        }
        $userlogs['action_id'] = '11';
        $userlogs['action_name'] = 'Log-in';
        $userlogs['message'] = $message;
        $userlogs['public_ip_address'] = getClientIp();
        $userlogs['status'] = '';
        $userlogs['longitude'] = $longitude;
        $userlogs['latitude'] = $latitude;
        $userlogs['created_by'] = Auth::id();
        $userlogs['created_by_name'] = Auth::user()->name;
        $userlogs['created_date'] = date('Y-m-d H:i:s');
        DB::table('user_logs')->insert($userlogs);
    }
}
