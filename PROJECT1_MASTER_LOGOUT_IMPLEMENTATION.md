# Project 1 SSO Master Logout Implementation Prompt

## Overview
This document provides instructions for implementing the master logout functionality in Project 1 (the main authentication server) to properly manage logout across all SSO-connected modules.

## Current Problem
The SSO logout flow was backwards - logging out from modules (Project 2) was logging out the main system (Project 1), which is incorrect. The proper flow should be:

- **✅ Correct**: Logout from Project 1 → Logout from all modules
- **❌ Wrong**: Logout from modules → Logout from Project 1

## Changes Already Made in Project 2 (Module Side)

### What Was Fixed in Project 2:
1. **Removed Project 1 logout notification** - Modules no longer notify Project 1 when they logout
2. **Added force logout endpoint** - Project 1 can now trigger logout in Project 2
3. **Module-only logout** - Project 2 logout only affects Project 2, keeps Project 1 session active
4. **API endpoint for master logout** - `/api/sso/force-logout` to receive logout commands from Project 1

### Project 2 New Endpoints:
- `POST /api/sso/force-logout` - Receives logout commands from Project 1
- `POST /sso/force-logout` - Web route for force logout
- Updated `POST /sso/logout` - Now only logs out locally

## Required Implementation for Project 1

### Step 1: Create SSO Configuration File

Create `config/sso.php` in Project 1:

```php
<?php

return [
    /*
    |--------------------------------------------------------------------------
    | SSO Modules Configuration
    |--------------------------------------------------------------------------
    |
    | List of all modules/systems that use SSO authentication from this server.
    | When a user logs out from the main system, all these modules will be
    | notified to logout the user as well.
    |
    */

    'modules' => [
        [
            'name' => 'Trustmark',
            'url' => env('TRUSTMARK_MODULE_URL', 'https://trustmark.bahayko.app/application'),
            'force_logout_endpoint' => '/api/sso/force-logout',
            'timeout' => 5, // seconds
        ],
        
        // Add more modules here as they're created
        // [
        //     'name' => 'Another Module',
        //     'url' => env('ANOTHER_MODULE_URL', 'https://another-module.example.com'),
        //     'force_logout_endpoint' => '/api/sso/force-logout',
        //     'timeout' => 5,
        // ],
    ],

    /*
    |--------------------------------------------------------------------------
    | SSO Token Configuration
    |--------------------------------------------------------------------------
    */
    
    'token' => [
        'expiry_minutes' => 30,
        'cleanup_interval' => 60, // minutes - how often to clean expired tokens
    ],

    /*
    |--------------------------------------------------------------------------
    | Logging Configuration
    |--------------------------------------------------------------------------
    */
    
    'logging' => [
        'log_module_notifications' => true,
        'log_failed_notifications' => true,
    ],
];
```

### Step 2: Add Environment Variables

Add to `.env` in Project 1:

```bash
# SSO Module URLs
TRUSTMARK_MODULE_URL=https://trustmark.bahayko.app/application

# Add more module URLs as needed
# ANOTHER_MODULE_URL=https://another-module.example.com
```

### Step 3: Create SSO Service Class

Create `app/Services/SsoService.php`:

```php
<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class SsoService
{
    /**
     * Notify all registered modules that a user has logged out
     *
     * @param int $userId
     * @param string|null $reason
     * @return array
     */
    public function notifyModulesLogout($userId, $reason = null)
    {
        $modules = config('sso.modules', []);
        $results = [];

        foreach ($modules as $module) {
            $result = $this->notifyModuleLogout($module, $userId, $reason);
            $results[$module['name']] = $result;
        }

        return $results;
    }

    /**
     * Notify a specific module about user logout
     *
     * @param array $module
     * @param int $userId
     * @param string|null $reason
     * @return array
     */
    private function notifyModuleLogout($module, $userId, $reason = null)
    {
        try {
            $response = Http::timeout($module['timeout'] ?? 5)
                ->post($module['url'] . $module['force_logout_endpoint'], [
                    'user_id' => $userId,
                    'reason' => $reason ?? 'main_logout',
                    'timestamp' => now()->toISOString(),
                    'source' => 'project1_master',
                ]);

            if ($response->successful()) {
                if (config('sso.logging.log_module_notifications')) {
                    Log::info('Successfully notified module about logout', [
                        'module' => $module['name'],
                        'user_id' => $userId,
                        'url' => $module['url'],
                        'response' => $response->json(),
                    ]);
                }

                return [
                    'success' => true,
                    'module' => $module['name'],
                    'response' => $response->json(),
                ];
            } else {
                throw new \Exception('HTTP ' . $response->status() . ': ' . $response->body());
            }

        } catch (\Exception $e) {
            if (config('sso.logging.log_failed_notifications')) {
                Log::warning('Failed to notify module about logout', [
                    'module' => $module['name'],
                    'user_id' => $userId,
                    'url' => $module['url'],
                    'error' => $e->getMessage(),
                ]);
            }

            return [
                'success' => false,
                'module' => $module['name'],
                'error' => $e->getMessage(),
            ];
        }
    }

    /**
     * Revoke all SSO tokens for a user
     *
     * @param \App\Models\User $user
     * @return int
     */
    public function revokeUserTokens($user)
    {
        // Revoke all tokens for the user
        $tokenCount = $user->tokens()->count();
        $user->tokens()->delete();

        Log::info('Revoked SSO tokens for user', [
            'user_id' => $user->id,
            'tokens_revoked' => $tokenCount,
        ]);

        return $tokenCount;
    }

    /**
     * Get list of registered modules
     *
     * @return array
     */
    public function getRegisteredModules()
    {
        return config('sso.modules', []);
    }
}
```

### Step 4: Update Authentication Controller

Update your main authentication controller in Project 1:

```php
<?php

namespace App\Http\Controllers;

use App\Services\SsoService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;

class AuthController extends Controller
{
    protected $ssoService;

    public function __construct(SsoService $ssoService)
    {
        $this->ssoService = $ssoService;
    }

    /**
     * Handle user logout with SSO module notification
     */
    public function logout(Request $request)
    {
        $user = Auth::user();
        
        if (!$user) {
            return redirect('/login')->with('error', 'No active session found.');
        }

        $userId = $user->id;
        
        Log::info('Master logout initiated', ['user_id' => $userId]);

        // Step 1: Revoke all SSO tokens for this user
        $revokedTokens = $this->ssoService->revokeUserTokens($user);

        // Step 2: Notify all registered modules about the logout
        $moduleResults = $this->ssoService->notifyModulesLogout($userId, 'master_logout');

        // Step 3: Logout from the main system (Project 1)
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        // Step 4: Log the results
        $successfulNotifications = collect($moduleResults)->where('success', true)->count();
        $totalModules = count($moduleResults);

        Log::info('Master logout completed', [
            'user_id' => $userId,
            'tokens_revoked' => $revokedTokens,
            'modules_notified' => $successfulNotifications,
            'total_modules' => $totalModules,
            'module_results' => $moduleResults,
        ]);

        // Step 5: Redirect with appropriate message
        $message = "Successfully logged out from all systems.";
        if ($successfulNotifications < $totalModules) {
            $message .= " Note: Some modules may require manual logout.";
        }

        return redirect('/login')->with('success', $message);
    }

    /**
     * Handle logout from external modules (if needed)
     */
    public function apiLogout(Request $request)
    {
        $token = $request->input('token');
        $userId = $request->input('user_id');

        if (!$token && !$userId) {
            return response()->json(['error' => 'Token or user ID required'], 400);
        }

        try {
            if ($token) {
                // Find and revoke specific token
                $accessToken = \Laravel\Sanctum\PersonalAccessToken::findToken($token);
                if ($accessToken) {
                    $user = $accessToken->tokenable;
                    $accessToken->delete();
                    
                    Log::info('API logout - token revoked', [
                        'user_id' => $user->id,
                        'token_id' => $accessToken->id,
                    ]);

                    return response()->json([
                        'success' => true,
                        'message' => 'Token revoked successfully',
                        'user_id' => $user->id,
                    ]);
                }
            }

            if ($userId) {
                // Revoke all tokens for user
                $user = \App\Models\User::find($userId);
                if ($user) {
                    $revokedCount = $user->tokens()->count();
                    $user->tokens()->delete();

                    Log::info('API logout - all tokens revoked for user', [
                        'user_id' => $userId,
                        'tokens_revoked' => $revokedCount,
                    ]);

                    return response()->json([
                        'success' => true,
                        'message' => 'All tokens revoked for user',
                        'tokens_revoked' => $revokedCount,
                    ]);
                }
            }

            return response()->json(['error' => 'Token or user not found'], 404);

        } catch (\Exception $e) {
            Log::error('API logout error', [
                'error' => $e->getMessage(),
                'token' => $token ? substr($token, 0, 10) . '...' : null,
                'user_id' => $userId,
            ]);

            return response()->json(['error' => 'Logout failed'], 500);
        }
    }
}
```

### Step 5: Add API Routes

Add to `routes/api.php` in Project 1:

```php
use App\Http\Controllers\AuthController;

// SSO Management Routes
Route::prefix('sso')->group(function () {
    Route::post('/logout', [AuthController::class, 'apiLogout']);
    Route::post('/revoke-token', [AuthController::class, 'apiLogout']); // Alias
});
```

### Step 6: Update Web Logout Route

Update `routes/web.php` in Project 1:

```php
// Replace your existing logout route with this
Route::post('/logout', [AuthController::class, 'logout'])->name('logout');
// Or if you use GET for logout:
Route::get('/logout', [AuthController::class, 'logout'])->name('logout');
```

### Step 7: Enhanced SSO Token Generation

Update your SSO token generation method:

```php
/**
 * Generate SSO token and redirect to module
 */
public function generateSsoToken(Request $request)
{
    $user = Auth::user();
    $redirectUrl = $request->input('redirect_url');

    if (!$user) {
        return redirect('/login')->with('error', 'Please login first.');
    }

    if (!$redirectUrl) {
        return back()->with('error', 'No redirect URL provided.');
    }

    // Validate redirect URL is from a registered module
    $allowedModules = collect(config('sso.modules'))->pluck('url')->toArray();
    $isValidModule = false;
    
    foreach ($allowedModules as $moduleUrl) {
        if (str_starts_with($redirectUrl, $moduleUrl)) {
            $isValidModule = true;
            break;
        }
    }

    if (!$isValidModule) {
        Log::warning('SSO token requested for unregistered module', [
            'user_id' => $user->id,
            'redirect_url' => $redirectUrl,
        ]);
        return back()->with('error', 'Invalid module URL.');
    }

    // Create SSO token with metadata
    $tokenName = 'sso-token-' . now()->timestamp;
    $expiryMinutes = config('sso.token.expiry_minutes', 30);
    
    $token = $user->createToken($tokenName, ['sso-access'], now()->addMinutes($expiryMinutes));

    Log::info('SSO token generated', [
        'user_id' => $user->id,
        'token_name' => $tokenName,
        'expires_at' => $token->accessToken->expires_at,
        'redirect_url' => $redirectUrl,
    ]);

    return redirect($redirectUrl . '?token=' . $token->plainTextToken);
}
```

### Step 8: Add SSO Management Dashboard (Optional)

Create a simple SSO management page:

```php
// Add to your admin controller or create new SsoController
public function ssoStatus()
{
    $modules = app(SsoService::class)->getRegisteredModules();
    $activeTokens = \Laravel\Sanctum\PersonalAccessToken::where('name', 'like', 'sso-token-%')
        ->where('expires_at', '>', now())
        ->count();

    return view('admin.sso-status', compact('modules', 'activeTokens'));
}
```

### Step 9: Environment Configuration

Update your `.env` file in Project 1:

```bash
# Existing configuration...

# SSO Module Configuration
TRUSTMARK_MODULE_URL=https://trustmark.bahayko.app/application

# Add more modules as they're created
# ANOTHER_MODULE_URL=https://another-module.example.com
```

## Testing the Implementation

### Test Scenarios:

1. **Module Logout Test**:
   - Login to Project 1
   - Access Project 2 via SSO
   - Logout from Project 2
   - Check that Project 1 session is still active
   - ✅ Expected: Project 1 remains logged in

2. **Master Logout Test**:
   - Login to Project 1
   - Access Project 2 via SSO
   - Logout from Project 1
   - Check that Project 2 automatically logs out
   - ✅ Expected: Both systems logged out

3. **Multiple Module Test** (if you have more modules):
   - Login to Project 1
   - Access multiple modules via SSO
   - Logout from Project 1
   - Check all modules logout
   - ✅ Expected: All systems logged out

### Verification Commands:

```bash
# Check if routes are registered
php artisan route:list | grep sso

# Test SSO service
php artisan tinker
>>> app(App\Services\SsoService::class)->getRegisteredModules();

# Check active tokens
>>> \Laravel\Sanctum\PersonalAccessToken::where('expires_at', '>', now())->count();
```

## Security Considerations

1. **Module URL Validation**: Only allow SSO tokens for registered modules
2. **Token Expiry**: Set appropriate expiration times (default: 30 minutes)
3. **Logging**: Log all SSO activities for audit purposes
4. **Timeout Handling**: Use appropriate timeouts for module notifications
5. **Error Handling**: Gracefully handle module notification failures

## Benefits of This Implementation

✅ **Proper Hierarchy**: Project 1 is the master, modules are subordinate  
✅ **Flexible Module Management**: Easy to add/remove modules  
✅ **Comprehensive Logging**: Full audit trail of SSO activities  
✅ **Graceful Degradation**: System works even if some modules are down  
✅ **Security**: Proper token validation and URL checking  
✅ **Scalable**: Can handle multiple modules easily  

## Troubleshooting

### Common Issues:

1. **Module not receiving logout notification**:
   - Check module URL in config
   - Verify module's `/api/sso/force-logout` endpoint
   - Check firewall/network connectivity

2. **Tokens not being revoked**:
   - Verify Sanctum is properly configured
   - Check database for `personal_access_tokens` table

3. **Logout not working**:
   - Check logs for error messages
   - Verify SSO service is properly injected
   - Test with single module first

### Debug Commands:

```bash
# Check module configurations
php artisan tinker
>>> config('sso.modules');

# Test module connectivity
>>> \Illuminate\Support\Facades\Http::get('https://trustmark.bahayko.app/application');

# Check active SSO tokens
>>> \Laravel\Sanctum\PersonalAccessToken::where('name', 'like', 'sso-token-%')->get();
```

This implementation creates a proper SSO master-slave relationship where Project 1 controls authentication for all connected modules while allowing modules to logout independently without affecting the master system.
