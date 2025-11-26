<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Cache;
class SsoController extends Controller
{
    /**
     * Handle SSO authentication from Project 1
     */
    public function authenticate(Request $request)
    {
        $token = $request->query('token');

        if (!$token) {
            return redirect()->route('login')->with('error', 'No authentication token provided.');
        }

        // Verify token with Project 1
        try {
            $response = Http::timeout(config('sso.project1.api_timeout'))->post(config('sso.project1.url') . '/api/sso/verify-token', [
                'token' => $token
            ]);

            if (!$response->successful()) {
                Log::warning('SSO token verification failed', [
                    'token' => substr($token, 0, 10) . '...',
                    'status' => $response->status(),
                    'response' => $response->body()
                ]);
                return redirect()->route('login')->with('error', 'Invalid or expired authentication token.');
            }

            $responseData = $response->json();
            $userData = $responseData['user'] ?? null;

            if (!$userData) {
                Log::error('SSO response missing user data', ['response' => $responseData]);
                return redirect()->route('login')->with('error', 'User data not found.');
            }

            // Find user in Project 1's database directly (since we're connected to it)
            $user = User::find($userData['id']);

            if (!$user || !$user->is_active) {
                return redirect()->route('login')->with('error', 'User account not found or inactive.');
            }

            // Store SSO information in session
            Session::put('sso_token', $token);
            Session::put('sso_authenticated', true);
            Session::put('last_sso_check', time());

            // Log the user in
            Auth::login($user);

            Log::info('SSO authentication successful', ['user_id' => $user->id, 'email' => $user->email]);

            return redirect()->intended('/')->with('success', 'Successfully authenticated via SSO!');

        } catch (\Exception $e) {
            Log::error('SSO authentication error', [
                'error' => $e->getMessage(),
                'token' => substr($token, 0, 10) . '...'
            ]);

            return redirect()->route('login')->with('error', 'Authentication service temporarily unavailable. Please try again.');
        }
    }

    /**
     * Show login form with SSO option
     */
    public function showLogin()
    {
        return view('auth.login', [
            'project1_url' => config('sso.project1.url')
        ]);
    }

    /**
     * Handle logout - only logout from Project 2, keep Project 1 session
     */
    public function logout(Request $request)
    {
        // Get SSO token before logout (for logging purposes)
        $ssoToken = Session::get('sso_token');

        // Perform local logout ONLY - don't notify Project 1
        Auth::logout();

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        Log::info('SSO user logged out from Project 2 only', [
            'token_prefix' => $ssoToken ? substr($ssoToken, 0, 10) . '...' : 'none'
        ]);

        return redirect('/login')->with('success', 'Successfully logged out from this module.');
    }

    /**
     * Check if user session is still valid with Project 1
     */
    public function checkSession()
    {
        if (!Auth::check() || !Session::get('sso_authenticated')) {
            return response()->json(['valid' => false]);
        }

        $ssoToken = Session::get('sso_token');
        if (!$ssoToken) {
            return response()->json(['valid' => false]);
        }

        try {
            $response = Http::timeout(5)->post(config('sso.project1.url') . '/api/sso/verify-token', [
                'token' => $ssoToken
            ]);

            $valid = $response->successful();

            if (!$valid) {
                // Token is invalid, logout user
                Auth::logout();
                Session::flush();
            } else {
                Session::put('last_sso_check', time());
            }

            return response()->json(['valid' => $valid]);

        } catch (\Exception $e) {
            Log::warning('SSO session check failed', ['error' => $e->getMessage()]);
            return response()->json(['valid' => true]); // Assume valid if Project 1 is down
        }
    }

    /**
     * Handle forced logout from Project 1 (when main system logs out)
     */
    public function forceLogout(Request $request)
    {
        try {
            // Validate the request format expected by Project 1
            $request->validate([
                'user_id' => 'required|integer',
                'user_email' => 'required|email',
            ]);

            $userId = $request->input('user_id');
            $userEmail = $request->input('user_email');

            Log::info('SSO force logout received', [
                'user_id' => $userId,
                'user_email' => $userEmail,
                'ip_address' => $request->ip(),
                'user_agent' => $request->userAgent(),
            ]);

            // Find the user first
            $user = \App\Models\User::find($userId);
            if (!$user || $user->email !== $userEmail) {
                return response()->json([
                    'success' => false,
                    'error' => 'User not found or email mismatch'
                ], 404);
            }

            // Option 1: If this specific user is currently authenticated in this request, logout immediately
            if (Auth::check() && Auth::id() == $userId) {
                Auth::logout();
                Session::invalidate();
                Session::regenerateToken();

                Log::info('Current session forcefully logged out', [
                    'user_id' => $userId,
                    'user_email' => $userEmail
                ]);
            }

            // Option 2: Revoke all Sanctum tokens for this user
            $tokenCount = $user->tokens()->count();
            $user->tokens()->delete();

            Log::info('All tokens revoked for user', [
                'user_id' => $userId,
                'user_email' => $userEmail,
                'tokens_revoked' => $tokenCount
            ]);

            // Option 3: Invalidate all stored sessions for this user
            // This will force logout from all devices/browsers
            try {
                $deletedSessions = 0;

                // Method A: Delete sessions with matching user_id
                $deletedSessions += \DB::table('sessions')
                    ->where('user_id', $userId)
                    ->delete();

                // Method B: Also delete sessions that contain this user's data in the payload
                // This handles cases where user_id might be null but user data is in session
                $sessions = \DB::table('sessions')->get();
                foreach ($sessions as $session) {
                    try {
                        $payload = base64_decode($session->payload);
                        if (strpos($payload, '"_token"') !== false) {
                            // Decode Laravel session data
                            $sessionData = unserialize($payload);
                            if (isset($sessionData['login_web_' . sha1('App\Models\User')]) &&
                                $sessionData['login_web_' . sha1('App\Models\User')] == $userId) {
                                \DB::table('sessions')->where('id', $session->id)->delete();
                                $deletedSessions++;
                            }
                        }
                    } catch (\Exception $e) {
                        // Skip malformed session data
                        continue;
                    }
                }

                Log::info('All sessions invalidated for user', [
                    'user_id' => $userId,
                    'user_email' => $userEmail,
                    'sessions_deleted' => $deletedSessions
                ]);
            } catch (\Exception $e) {
                Log::warning('Failed to invalidate sessions for user', [
                    'user_id' => $userId,
                    'error' => $e->getMessage()
                ]);
            }

            return response()->json([
                'success' => true,
                'message' => 'User successfully logged out from all sessions',
                'user_id' => $userId,
                'tokens_revoked' => $tokenCount
            ], 200);

        } catch (\Exception $e) {
            Log::error('SSO force logout failed', [
                'error' => $e->getMessage(),
                'user_id' => $request->input('user_id'),
                'user_email' => $request->input('user_email'),
            ]);

            return response()->json([
                'success' => false,
                'error' => 'Force logout failed',
                'message' => $e->getMessage()
            ], 500);
        }
    }
    public function verifyToken(Request $request)
    {
        $token = $request->input('token');

        if (!$token) {
            return response()->json(['success' => false, 'error' => 'Token missing'], 400);
        }

        $userId = Cache::get("sso_token_{$token}");

        if (!$userId) {
            return response()->json(['success' => false, 'error' => 'Invalid or expired token'], 401);
        }

        $user = User::find($userId);

        if (!$user) {
            return response()->json(['success' => false, 'error' => 'User not found'], 404);
        }

        return response()->json([
            'success' => true,
            'user' => [
                'id' => $user->id,
                'email' => $user->email,
                'name' => $user->name,
            ]
        ]);
    }

    public function generateToken(User $user)
    {
        $token = Str::random(60);
        Cache::put("sso_token_{$token}", $user->id, now()->addMinutes(5)); // valid for 5 minutes
        return $token;
    }

    public function redirectToApp2()
    {
        return redirect()->route('dashboard');
        $user = Auth::user();
        if (!$user) {
            return redirect()->route('login')->with('error', 'Please login first.');
        }

        $token = $this->generateToken($user);

        return redirect(config('sso.project2.url') . '/sso/authenticate?token=' . $token);
    }
}
