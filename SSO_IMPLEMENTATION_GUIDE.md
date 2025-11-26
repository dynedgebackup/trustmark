# SSO Implementation for Project 2 (Trustmark)

**Status**: ✅ COMPLETED - Full SSO functionality with force logout implemented

This document describes the Single Sign-On (SSO) implementation that allows users to authenticate with Project 2 using tokens from Project 1 (the ma4. **Database connection errors**
   - Verify Project 1 database credentials in .env
   - Check database server availability

5. **Force logout not working**
   - Verify API endpoint is accessible: `POST /api/sso/force-logout`
   - Check if endpoint is in CSRF exceptions
   - Verify user_id parameter is being sent correctly
   - Check Laravel logs for session deletion details

6. **Sessions not being deleted properly**
   - Verify `SESSION_DRIVER=database` in .env
   - Check if sessions table exists and is accessible
   - Review force logout logs for session parsing errorsauthentication server).

## Overview

The SSO implementation allows users who are already logged into Project 1 to seamlessly access Project 2 without re-entering their credentials. This is achieved through secure token verification between the two applications. The implementation also includes master logout functionality where Project 1 can force logout users from Project 2.

## Components Implemented

## Components Implemented

### 1. SSO Controller (`app/Http/Controllers/SsoController.php`)
- **authenticate()**: Handles SSO token verification and user login
- **logout()**: Handles SSO user logout with Project 1 notification
- **checkSession()**: Validates SSO session status
- **forceLogout()**: Handles master logout requests from Project 1 with comprehensive session cleanup
  - Logs out current authenticated user if matching
  - Revokes all Sanctum tokens for the user
  - Deletes all database sessions including those with null user_id
  - Enhanced session payload parsing for edge cases

### 2. SSO Session Middleware (`app/Http/Middleware/SsoSessionMiddleware.php`)
- Periodically verifies SSO sessions with Project 1
- Automatically logs out users if their SSO session expires
- Prevents stale sessions

### 3. Database Configuration
- Added `project1` database connection in `config/database.php`
- User model configured to connect to Project 1's database using `protected $connection = 'project1'`
- Session storage configured with `SESSION_DRIVER=database` for persistent session management
- Enhanced session handling for force logout functionality

### 4. Frontend Components
- SSO session management JavaScript
- Updated login view with SSO button
- Session expiry notifications

### 5. User Model Enhancement
- Added `HasApiTokens` trait from Laravel Sanctum
- Enables token management for force logout functionality
- Maintains Project 1 database connection

### 6. Route Configuration
- Web routes for SSO authentication flow
- API routes for force logout endpoint
- CSRF exception for external API calls

## Configuration

### Environment Variables (.env)
```bash
# Project 1 SSO Configuration
PROJECT1_URL=http://localhost:8000
PROJECT1_API_TIMEOUT=10
PROJECT1_DB_HOST=127.0.0.1
PROJECT1_DB_PORT=3306
PROJECT1_DB_DATABASE=project1_database_name
PROJECT1_DB_USERNAME=root
PROJECT1_DB_PASSWORD=

# SSO Session Configuration
SSO_SESSION_CHECK_INTERVAL=300
SSO_TOKEN_EXPIRY=1800
SSO_REQUIRE_HTTPS=false

# Application Configuration
APP_NAME="Trustmark Project 2"
APP_URL=http://localhost:8001
```

### Configuration Files
All SSO settings are managed through `config/sso.php`:

```php
return [
    'project1' => [
        'url' => env('PROJECT1_URL', 'http://localhost:8000'),
        'api_timeout' => env('PROJECT1_API_TIMEOUT', 10),
    ],
    'session' => [
        'check_interval' => env('SSO_SESSION_CHECK_INTERVAL', 300), // 5 minutes
        'token_expiry' => env('SSO_TOKEN_EXPIRY', 1800), // 30 minutes
    ],
    'security' => [
        'require_https' => env('SSO_REQUIRE_HTTPS', false),
        'allowed_redirects' => [
            env('APP_URL', 'http://localhost:8001'),
        ],
    ],
];
```

### Routes Added
```php
// SSO Authentication Routes
Route::get('/sso/authenticate', [SsoController::class, 'authenticate'])
Route::get('/sso/check-session', [SsoController::class, 'checkSession'])
Route::post('/sso/logout', [SsoController::class, 'logout'])
```

### API Routes
```php
// For Project 1 to verify Project 2 status
Route::post('/api/sso/check-session', [SsoController::class, 'checkSession'])
```

## How It Works

### SSO Authentication Flow
1. User logs into Project 1 at `http://localhost:8000`
2. User clicks "Access Project 2" or similar link in Project 1
3. Project 1 generates a temporary SSO token (30-minute expiry)
4. User is redirected to Project 2: `http://localhost:8001/sso/authenticate?token=1|abc123...`
5. Project 2 verifies the token with Project 1's API
6. If valid, Project 2 creates/finds the user account and logs them in
7. User is redirected to the dashboard with SSO status indicated

#### Force Logout Flow (Master Logout)
1. User is logged out from Project 1 or admin initiates force logout
2. Project 1 sends POST request to Project 2: `http://localhost:8001/api/sso/force-logout`
3. Project 2 validates the request and performs comprehensive cleanup:
   - Logs out current authenticated user (if matches user_id)
   - Revokes all Sanctum tokens for the user
   - Deletes all database sessions with matching user_id
   - Parses session payloads to find sessions with null user_id but matching user data
   - Deletes additional sessions based on payload analysis
4. Project 2 responds with success confirmation and session deletion count
5. User is forced to re-authenticate on next page refresh or request

### Session Management
- SSO sessions are validated every 5 minutes
- If Project 1 session expires, Project 2 automatically logs out the user
- JavaScript component provides real-time session monitoring

### User Account Handling
The implementation supports two approaches:

**Option A (Current)**: Create local user accounts
- When a user authenticates via SSO, a local account is created
- User data is synchronized from Project 1
- Subsequent logins update user information

**Option B**: Direct database connection
- Connect directly to Project 1's database
- No local user duplication
- Requires database access to Project 1

## Project 1 Integration Requirements

For this implementation to work, Project 1 must provide these API endpoints:

### 1. Token Verification
```http
POST /api/sso/verify-token
Content-Type: application/json

{
    "token": "1|abc123def456..."
}
```

**Response (Success)**:
```json
{
    "success": true,
    "user": {
        "id": 1,
        "name": "John Doe",
        "email": "john@example.com",
        "first_name": "John",
        "last_name": "Doe",
        "role": "user",
        "is_active": 1
    }
}
```

### 2. SSO Token Generation
```http
POST /api/sso/generate-token
Authorization: Bearer {user_token}
Content-Type: application/json

{
    "redirect_url": "http://localhost:8001/sso/authenticate"
}
```

### 3. SSO Redirect Page
Project 1 should have a page (e.g., `/sso`) that:
- Generates SSO tokens for authenticated users
- Redirects to Project 2 with the token
- Handles the redirect_url parameter

## Testing the Implementation

### Prerequisites
1. Project 1 running on `http://localhost:8000`
2. Project 2 running on `http://localhost:8001`
3. Project 1 database accessible from Project 2
4. Project 1 SSO API endpoints implemented

### Test Steps
1. **Start both applications**:
   ```bash
   # Terminal 1 - Project 1
   cd /path/to/project1
   php artisan serve --port=8000

   # Terminal 2 - Project 2 (this project)
   cd /path/to/project2
   php artisan serve --port=8001
   ```

2. **Login to Project 1**:
   - Navigate to `http://localhost:8000/login`
   - Login with valid credentials

3. **Test SSO Authentication**:
   - From Project 1, navigate to SSO page
   - Click "Access Project 2" button
   - Should automatically redirect and login to Project 2

4. **Test Direct SSO**:
   - Navigate to `http://localhost:8001/login`
   - Click "Login with Main Account (SSO)"
   - Should redirect to Project 1, then back to Project 2 when authenticated

## Security Features

### Token Security
- Tokens are temporary (30-minute expiry)
- Tokens are validated with origin server
- Invalid tokens result in immediate rejection

### Session Security
- Regular session validation with Project 1
- Automatic logout on session expiry
- CSRF protection on all forms

### Network Security
- All API calls use HTTPS in production
- Timeout protection for API calls
- Graceful handling of network failures

## Monitoring and Logging

The implementation logs:
- SSO authentication attempts
- Token verification failures
- Session expiry events
- Network errors

Check logs at `storage/logs/laravel.log`

## Troubleshooting

### Common Issues

1. **"Invalid or expired authentication token"**
   - Check Project 1 is running and accessible
   - Verify PROJECT1_URL in .env
   - Check token hasn't expired (30 minutes)

2. **"User data not found"**
   - Verify Project 1 returns proper user data
   - Check API response format matches expected structure

3. **Session keeps expiring**
   - Check network connectivity to Project 1
   - Verify Project 1 token verification endpoint is working
   - Check firewall/proxy settings

4. **Database connection errors**
   - Verify Project 1 database credentials in .env
   - Check database server accessibility
   - Ensure database user has proper permissions

### Debug Commands
```bash
# Test database connection
php artisan tinker
>>> DB::connection('project1')->select('SELECT 1 as test');

# Test API connectivity
>>> Http::post('http://localhost:8000/api/sso/verify-token', ['token' => 'test']);

# Check current user session
>>> Auth::user();
>>> session()->all();
```

## Customization

### Modifying User Data Sync
Edit `SsoController::authenticate()` method to customize which user fields are synchronized:

```php
$user = User::create([
    'name' => $userData['name'],
    'email' => $userData['email'],
    // Add more fields as needed
]);
```

### Changing Session Check Interval
Modify `SsoSessionMiddleware` or JavaScript component:

```php
// In middleware
$checkInterval = 300; // 5 minutes

// In JavaScript
this.checkInterval = 5 * 60 * 1000; // 5 minutes
```

### Custom Login Page
Update `resources/views/auth/login.blade.php` to modify the SSO button appearance or add additional options.

## Production Deployment

### Required Changes for Production

1. **Use HTTPS**: Update all URLs to use HTTPS
2. **Secure Database**: Use encrypted database connections
3. **Environment Variables**: Secure storage of sensitive configuration
4. **Error Handling**: Implement proper error pages
5. **Monitoring**: Set up monitoring for SSO authentication flows

### Performance Considerations

1. **Database Connection Pooling**: Configure for high traffic
2. **API Timeouts**: Adjust based on network conditions
3. **Session Storage**: Consider Redis for session storage
4. **Caching**: Implement caching for user data if needed

## Support

For issues related to this SSO implementation:
1. Check the troubleshooting section
2. Review application logs
3. Verify Project 1 integration
4. Check network connectivity between servers

## Future Enhancements

Possible improvements to consider:
1. **Multiple SSO Providers**: Support for multiple authentication servers
2. **Role Synchronization**: Real-time role updates from Project 1
3. **Audit Logging**: Enhanced logging for compliance
4. **Token Refresh**: Automatic token refresh for longer sessions
5. **Mobile Support**: Mobile app SSO integration

## Implementation Status

### ✅ Completed Features
- **SSO Authentication**: Token-based authentication from Project 1
- **Session Management**: Automatic session validation and renewal
- **Force Logout**: Master logout functionality with comprehensive session cleanup
- **Database Sessions**: Persistent session storage for reliable logout
- **Token Management**: Sanctum integration for token revocation
- **Session Payload Parsing**: Enhanced session detection for edge cases
- **API Integration**: Complete API endpoints for both web and API routes
- **Error Handling**: Comprehensive logging and error responses
- **CSRF Protection**: Proper CSRF exceptions for external API calls

### 🔧 Technical Implementation
- **Laravel 11**: Full framework compatibility
- **Sanctum Integration**: Token management and revocation
- **Database Sessions**: Enhanced session handling for force logout
- **PowerShell Testing**: Windows-compatible testing commands
- **Session Analytics**: Detailed logging of session operations

### 📝 Testing Verification
- **Local Development**: localhost:8000 ↔ localhost:8001 tested
- **Force Logout**: Comprehensive session cleanup verified
- **Page Refresh**: Logout persistence across browser refreshes
- **Edge Cases**: Sessions with null user_id handling
- **API Responses**: JSON success/error responses implemented
