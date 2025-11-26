# SSO Client Implementation Prompt for AI Assistants

## Overview
This document provides a comprehensive prompt for AI assistants to implement Single Sign-On (SSO) client functionality for any system that needs to authenticate users with Project 1 (the main authentication server).

## AI Assistant Prompt

---

**PROMPT START:**

You need to implement SSO client functionality for a web application that will authenticate users using tokens from Project 1 (main authentication server). Here are the requirements and implementation details:

### System Architecture
- **Project 1**: Main authentication server running on `http://localhost:8000`
- **Current Project**: SSO client that needs to authenticate users from Project 1
- **Authentication Method**: Token-based SSO with API verification

### Required API Endpoints from Project 1
Project 1 provides these endpoints:

1. **Token Verification**:
   ```http
   POST /api/sso/verify-token
   Content-Type: application/json
   
   {
       "token": "1|abc123def456..."
   }
   ```
   
   **Success Response**:
   ```json
   {
       "success": true,
       "user": {
           "id": 1,
           "name": "John Doe",
           "email": "john@example.com",
           "first_name": "John",
           "last_name": "Doe",
           "middle_name": null,
           "suffix": null,
           "ctc_no": "+639123456789",
           "role": "user",
           "is_active": 1,
           "email_verified_at": "2025-01-01T00:00:00.000000Z"
       }
   }
   ```

2. **SSO Redirect Page**: `http://localhost:8000/sso?redirect_url={encoded_url}`

### Implementation Requirements

#### 1. Database Configuration
Create a Project 1 database connection:

```php
// config/database.php - Add this connection
'project1' => [
    'driver' => 'mysql',
    'host' => env('PROJECT1_DB_HOST', '127.0.0.1'),
    'port' => env('PROJECT1_DB_PORT', '3306'),
    'database' => env('PROJECT1_DB_DATABASE', 'unified_services'),
    'username' => env('PROJECT1_DB_USERNAME', 'root'),
    'password' => env('PROJECT1_DB_PASSWORD', ''),
    'charset' => 'utf8mb4',
    'collation' => 'utf8mb4_unicode_ci',
    'prefix' => '',
    'prefix_indexes' => true,
    'strict' => true,
    'engine' => null,
],
```

#### 2. Environment Variables
Add these variables to `.env`:

```bash
# Project 1 SSO Configuration
PROJECT1_URL=http://localhost:8000
PROJECT1_API_TIMEOUT=10
PROJECT1_DB_HOST=127.0.0.1
PROJECT1_DB_PORT=3306
PROJECT1_DB_DATABASE=unified_services
PROJECT1_DB_USERNAME=root
PROJECT1_DB_PASSWORD=

# SSO Session Configuration
SSO_SESSION_CHECK_INTERVAL=300
SSO_TOKEN_EXPIRY=1800
SSO_REQUIRE_HTTPS=false
```

#### 3. Configuration File
Create `config/sso.php`:

```php
<?php

return [
    'project1' => [
        'url' => env('PROJECT1_URL', 'http://localhost:8000'),
        'api_timeout' => env('PROJECT1_API_TIMEOUT', 10),
    ],
    'session' => [
        'check_interval' => env('SSO_SESSION_CHECK_INTERVAL', 300),
        'token_expiry' => env('SSO_TOKEN_EXPIRY', 1800),
    ],
    'security' => [
        'require_https' => env('SSO_REQUIRE_HTTPS', false),
        'allowed_redirects' => [
            env('APP_URL', 'http://localhost:8001'),
        ],
    ],
];
```

#### 4. User Model
Update the User model to connect to Project 1's database and add Sanctum support:

```php
<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;

class User extends Authenticatable
{
    use HasFactory, Notifiable, HasApiTokens;

    protected $connection = 'project1'; // Connect to Project 1's database
    
    // Include all user fields that exist in Project 1
    protected $fillable = [
        'name', 'first_name', 'middle_name', 'last_name', 'suffix',
        'username', 'email', 'ctc_no', 'role', 'email_verified_at',
        'password', 'is_active', 'profile_photos'
    ];

    protected $hidden = ['password', 'remember_token'];

    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
            'is_active' => 'boolean',
        ];
    }

    public function isSsoUser(): bool
    {
        return session('sso_authenticated', false);
    }

    public function getSsoToken(): ?string
    {
        return session('sso_token');
    }
}
```

#### 5. SSO Controller
Create `app/Http/Controllers/SsoController.php`:

```php
<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Session;

class SsoController extends Controller
{
    public function authenticate(Request $request)
    {
        $token = $request->query('token');
        
        if (!$token) {
            return redirect()->route('login')->with('error', 'No authentication token provided.');
        }

        try {
            $response = Http::timeout(config('sso.project1.api_timeout'))->post(config('sso.project1.url') . '/api/sso/verify-token', [
                'token' => $token
            ]);

            if (!$response->successful()) {
                Log::warning('SSO token verification failed', [
                    'token' => substr($token, 0, 10) . '...',
                    'status' => $response->status()
                ]);
                return redirect()->route('login')->with('error', 'Invalid or expired authentication token.');
            }

            $responseData = $response->json();
            $userData = $responseData['user'] ?? null;
            
            if (!$userData) {
                return redirect()->route('login')->with('error', 'User data not found.');
            }

            $user = User::find($userData['id']);
            
            if (!$user || !$user->is_active) {
                return redirect()->route('login')->with('error', 'User account not found or inactive.');
            }

            // Store SSO information in session
            Session::put('sso_token', $token);
            Session::put('sso_authenticated', true);
            Session::put('last_sso_check', time());

            Auth::login($user);

            // Redirect to dashboard (adjust route as needed)
            return redirect()->intended('/')->with('success', 'Successfully authenticated via SSO!');

        } catch (\Exception $e) {
            Log::error('SSO authentication error', [
                'error' => $e->getMessage(),
                'token' => substr($token, 0, 10) . '...'
            ]);
            
            return redirect()->route('login')->with('error', 'Authentication service temporarily unavailable.');
        }
    }

    public function logout(Request $request)
    {
        $ssoToken = Session::get('sso_token');
        
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        // Optionally notify Project 1 about logout
        if ($ssoToken) {
            try {
                Http::timeout(5)->post(config('sso.project1.url') . '/api/sso/logout', [
                    'token' => $ssoToken
                ]);
            } catch (\Exception $e) {
                Log::warning('Failed to notify Project 1 about logout', ['error' => $e->getMessage()]);
            }
        }

        return redirect('/login')->with('success', 'Successfully logged out.');
    }

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

    public function forceLogout(Request $request)
    {
        $request->validate([
            'user_id' => 'required|integer'
        ]);

        $userId = $request->user_id;
        
        Log::info('Force logout requested', ['user_id' => $userId]);

        try {
            // Step 1: Log out current session if it matches the user
            if (Auth::check() && Auth::id() == $userId) {
                Log::info('Logging out current authenticated user', ['user_id' => $userId]);
                Auth::logout();
                $request->session()->invalidate();
                $request->session()->regenerateToken();
            }

            // Step 2: Revoke all tokens for this user (if using Sanctum)
            $user = User::find($userId);
            if ($user) {
                Log::info('Found user, revoking tokens', ['user_id' => $userId]);
                $user->tokens()->delete();
            }

            // Step 3: Delete all sessions for this user from database
            $deletedSessions = 0;
            
            // First, try to delete sessions with matching user_id
            $directDeletes = DB::table('sessions')
                ->where('user_id', $userId)
                ->delete();
            
            $deletedSessions += $directDeletes;
            Log::info('Deleted sessions with user_id match', [
                'user_id' => $userId, 
                'deleted_count' => $directDeletes
            ]);

            // Then, check sessions with null user_id but matching user data in payload
            $sessionsWithNullUserId = DB::table('sessions')
                ->whereNull('user_id')
                ->get();

            foreach ($sessionsWithNullUserId as $session) {
                try {
                    $payload = base64_decode($session->payload);
                    
                    // Look for user ID in the session payload
                    if (strpos($payload, '"_token"') !== false && 
                        (strpos($payload, 'login_web_' . $userId) !== false || 
                         strpos($payload, '"' . $userId . '"') !== false ||
                         strpos($payload, 's:' . strlen($userId) . ':"' . $userId . '"') !== false)) {
                        
                        DB::table('sessions')->where('id', $session->id)->delete();
                        $deletedSessions++;
                        Log::info('Deleted session with null user_id but matching payload', [
                            'user_id' => $userId,
                            'session_id' => $session->id
                        ]);
                    }
                } catch (\Exception $e) {
                    Log::warning('Failed to parse session payload', [
                        'session_id' => $session->id,
                        'error' => $e->getMessage()
                    ]);
                }
            }

            Log::info('Force logout completed', [
                'user_id' => $userId,
                'total_sessions_deleted' => $deletedSessions,
                'user_found' => $user ? 'yes' : 'no'
            ]);

            return response()->json([
                'success' => true,
                'message' => 'User force logged out successfully',
                'sessions_deleted' => $deletedSessions
            ]);

        } catch (\Exception $e) {
            Log::error('Force logout failed', [
                'user_id' => $userId,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Failed to force logout user'
            ], 500);
        }
    }
}
```

#### 6. SSO Session Middleware
Create `app/Http/Middleware/SsoSessionMiddleware.php`:

```php
<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Session;
use Symfony\Component\HttpFoundation\Response;

class SsoSessionMiddleware
{
    public function handle(Request $request, Closure $next): Response
    {
        if (Auth::check() && Session::get('sso_authenticated')) {
            $lastCheck = Session::get('last_sso_check', 0);
            $checkInterval = config('sso.session.check_interval', 300);
            
            if ($lastCheck < (time() - $checkInterval)) {
                $ssoToken = Session::get('sso_token');
                
                if ($ssoToken) {
                    try {
                        $response = Http::timeout(5)->post(config('sso.project1.url') . '/api/sso/verify-token', [
                            'token' => $ssoToken
                        ]);
                        
                        if (!$response->successful()) {
                            Auth::logout();
                            Session::flush();
                            
                            if ($request->expectsJson()) {
                                return response()->json(['error' => 'Session expired'], 401);
                            }
                            
                            return redirect()->route('login')->with('error', 'Your session has expired. Please login again.');
                        }
                        
                        Session::put('last_sso_check', time());
                        
                    } catch (\Exception $e) {
                        Log::warning('SSO session verification failed', ['error' => $e->getMessage()]);
                        Session::put('last_sso_check', time());
                    }
                }
            }
        }

        return $next($request);
    }
}
```

#### 7. Register Middleware
Add to `bootstrap/app.php`:

```php
->withMiddleware(function (Middleware $middleware): void {
    $middleware->group('web', [
        // ... existing middleware
        \App\Http\Middleware\SsoSessionMiddleware::class,
    ]);
    
    $middleware->alias([
        'sso.session' => \App\Http\Middleware\SsoSessionMiddleware::class,
    ]);
})
```

#### 8. Routes
Add to `routes/web.php`:

```php
use App\Http\Controllers\SsoController;

// SSO Authentication Routes
Route::get('/sso/authenticate', [SsoController::class, 'authenticate'])->name('sso.authenticate');
Route::get('/sso/check-session', [SsoController::class, 'checkSession'])->name('sso.check');
Route::post('/sso/logout', [SsoController::class, 'logout'])->name('sso.logout');
Route::post('/sso/force-logout', [SsoController::class, 'forceLogout'])->name('sso.force-logout');

// Update logout route to handle SSO
Route::get('/logout', function(Request $request) {
    // Check if this is an SSO user
    if (session('sso_authenticated')) {
        return app(App\Http\Controllers\SsoController::class)->logout($request);
    } else {
        // Handle regular logout here or redirect to your auth controller
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();
        return redirect('/login')->with('success', 'Successfully logged out.');
    }
})->name('logout');
```

Add to `routes/api.php`:

```php
use App\Http\Controllers\SsoController;

// SSO API Routes (for Project 1 to notify about logout)
Route::prefix('sso')->group(function () {
    Route::post('/check-session', [SsoController::class, 'checkSession']);
    Route::post('/force-logout', [SsoController::class, 'forceLogout']);
});
```

#### 9. CSRF Exception
Add force logout endpoint to CSRF exceptions in `app/Http/Middleware/VerifyCsrfToken.php`:

```php
protected $except = [
    '/api/sso/force-logout'
];
```

#### 10. Login View Integration
Update your login view to include SSO option:

```html
<!-- Add this CSS for styling -->
<style>
.btn-sso {
    background-color: #198754;
    border-color: #198754;
    color: white;
    transition: all 0.3s ease;
}

.btn-sso:hover {
    background-color: #157347;
    border-color: #146c43;
    color: white;
    transform: translateY(-1px);
    box-shadow: 0 4px 8px rgba(25, 135, 84, 0.3);
}

.sso-divider {
    position: relative;
    text-align: center;
    margin: 1.5rem 0;
}

.sso-divider::before {
    content: '';
    position: absolute;
    top: 50%;
    left: 0;
    right: 0;
    height: 1px;
    background: #dee2e6;
}

.sso-divider span {
    background: white;
    padding: 0 1rem;
    color: #6c757d;
    font-size: 13px;
}
</style>

<!-- Add this HTML after your regular login form -->
<div class="sso-divider">
    <span>Or continue with</span>
</div>

<div class="text-center mb-3">
    <a href="{{ config('sso.project1.url') }}/sso?redirect_url={{ urlencode(url('/sso/authenticate')) }}" 
       class="btn btn-sso w-100 d-flex align-items-center justify-content-center">
        <i class="fas fa-shield-alt me-2"></i>
        <span>Login with Main Account</span>
    </a>
    <p class="text-muted small mt-2 mb-0">Secure Single Sign-On authentication</p>
</div>
```

#### 11. JavaScript Session Manager
Create `public/assets/js/sso-session-manager.js`:

```javascript
class SsoSessionManager {
    constructor(options = {}) {
        this.checkInterval = (options.checkInterval || 5) * 60 * 1000;
        this.isChecking = false;
        this.init();
    }

    init() {
        if (document.querySelector('[data-sso-authenticated]')) {
            this.startPeriodicCheck();
        }
    }

    startPeriodicCheck() {
        setInterval(() => {
            this.checkSession();
        }, this.checkInterval);
    }

    async checkSession() {
        if (this.isChecking) return;
        
        this.isChecking = true;
        
        try {
            const response = await fetch('/sso/check-session', {
                method: 'GET',
                headers: {
                    'X-Requested-With': 'XMLHttpRequest',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.content || ''
                }
            });

            const data = await response.json();
            
            if (!data.valid) {
                this.handleSessionExpired();
            }
        } catch (error) {
            console.warn('SSO session check failed:', error);
        } finally {
            this.isChecking = false;
        }
    }

    handleSessionExpired() {
        if (typeof Swal !== 'undefined') {
            Swal.fire({
                icon: 'warning',
                title: 'Session Expired',
                text: 'Your SSO session has expired. You will be redirected to login.',
                confirmButtonColor: '#3085d6',
                timer: 3000,
                timerProgressBar: true
            }).then(() => {
                window.location.href = '/login';
            });
        } else {
            alert('Your SSO session has expired. You will be redirected to login.');
            window.location.href = '/login';
        }
    }
}

document.addEventListener('DOMContentLoaded', function() {
    const ssoConfig = window.ssoConfig || {};
    new SsoSessionManager(ssoConfig);
});
```

#### 12. Layout Integration
Add to your main layout (e.g., `resources/views/layouts/app.blade.php`):

```html
@if(session('sso_authenticated'))
    <script>
        window.ssoConfig = {
            checkInterval: {{ config('sso.session.check_interval') / 60 }}
        };
    </script>
    <script src="{{ asset('assets/js/sso-session-manager.js') }}"></script>
    <div data-sso-authenticated="true" style="display: none;"></div>
@endif
```

#### 13. Update Controller to Pass Config
Update your login controller method:

```php
public function login()
{
    return view('auth.login', [
        'project1_url' => config('sso.project1.url')
    ]);
}
```

### Framework-Specific Notes

#### For Laravel:
- Use the exact code above
- Ensure Laravel Sanctum is installed for token management: `composer require laravel/sanctum`
- Add `HasApiTokens` trait to the User model for force logout functionality
- Use `php artisan serve --port=8001` for testing

#### For Other PHP Frameworks:
- Adapt the authentication logic to your framework's auth system
- Use your framework's HTTP client for API calls
- Adjust middleware registration to your framework's method

#### For Non-PHP Systems:
- Convert the logic to your programming language
- Use appropriate HTTP client libraries
- Implement session management according to your platform

### Testing Steps

1. **Start Project 1**: `php artisan serve --port=8000`
2. **Start Client Project**: `php artisan serve --port=8001`
3. **Test SSO Flow**:
   - Go to client project login page
   - Click "Login with Main Account"
   - Should redirect to Project 1, then back to client
   - User should be automatically logged in
4. **Test Force Logout**:
   - Use PowerShell to test force logout endpoint:
   ```powershell
   $body = @{user_id = 1} | ConvertTo-Json
   Invoke-RestMethod -Uri "http://localhost:8001/api/sso/force-logout" -Method POST -Body $body -ContentType "application/json"
   ```

### Important Notes

- Replace `/` with your actual dashboard route if different
- Adjust user model fields based on your Project 1 user table structure
- Update styling to match your application's theme
- Add proper error handling for your specific use case
- Configure proper HTTPS and security settings for production
- The force logout endpoint handles comprehensive session cleanup including:
  - Current session invalidation
  - Token revocation via Sanctum
  - Database session deletion with user_id matching
  - Enhanced session cleanup for sessions with null user_id values
- Sessions are stored in database with `SESSION_DRIVER=database`
- Force logout works across page refreshes and handles edge cases

**PROMPT END**

---

## Usage Instructions for AI Assistants

1. **Copy the entire prompt** from "PROMPT START" to "PROMPT END"
2. **Paste it into your conversation** with an AI assistant
3. **Add specific details** about the target system:
   - Framework being used (Laravel, Symfony, Django, etc.)
   - Current authentication system
   - Specific styling requirements
   - Any custom user fields or requirements

## Example AI Request

```
[PASTE THE FULL PROMPT HERE]

Additional Context:
- I'm using Laravel 11 with Breeze authentication
- My dashboard route is `/dashboard` instead of `/`
- I need to sync additional user fields: department_id, employee_code
- My project uses Tailwind CSS for styling
- I want to implement role-based redirects after SSO login
```

This prompt ensures any AI assistant can implement SSO client functionality efficiently and consistently across different projects.
