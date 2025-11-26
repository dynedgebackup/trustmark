# Email Token Spam Prevention System

This implementation follows the exact specifications from the other developer's requirements in `email.md`.

## Overview

The email token system prevents email spam by:
- Creating unique tokens for each email send attempt
- Enforcing a 10-minute cooldown period between emails
- Tracking token usage to prevent reuse
- Providing countdown timers for better user experience

## Database Structure

### Email Tokens Table
```sql
CREATE TABLE email_tokens (
    id INT AUTO_INCREMENT PRIMARY KEY,
    email VARCHAR(255) NOT NULL,
    token VARCHAR(64) NOT NULL UNIQUE,
    is_taken tinyint(1) NOT NULL DEFAULT 0,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    expires_at TIMESTAMP GENERATED ALWAYS AS (created_at + INTERVAL 10 MINUTE) STORED
);
```

## Implementation Components

### 1. EmailToken Model (`app/Models/EmailToken.php`)
- Handles token generation using SHA256
- Provides validation methods
- Manages token lifecycle

### 2. EmailTokenService (`app/Services/EmailTokenService.php`)
- Core business logic for email spam prevention
- Countdown message generation
- Token validation and consumption

### 3. Updated Controllers
- **AuthenticatedSessionController**: Email verification during login
- **RegisteredUserController**: Email verification during registration  
- **UserController**: OTP email sending with spam prevention

### 4. Frontend Integration
- **Login page**: SweetAlert countdown with form disabling
- **Forgot password page**: Real-time countdown display
- **JavaScript**: `public/js/email-countdown.js` for countdown handling

### 5. API Endpoints
- `POST /api/email-tokens/check-countdown`: Check countdown status
- `POST /api/email-tokens/time-until-next`: Get remaining time

## How It Works

### Email Verification (Login/Registration)
1. User attempts login/registration with unverified email
2. System checks if email token exists and is not expired
3. If cooldown active, shows countdown message
4. If allowed, creates new token and sends email
5. Email verification link includes token parameter
6. Token is validated and marked as taken upon verification

### Password OTP
1. User requests password reset OTP
2. System checks email token cooldown
3. If cooldown active, shows countdown with disabled form
4. If allowed, creates token and sends OTP email
5. Frontend shows real-time countdown

### Token Lifecycle
1. **Creation**: Token created before email send
2. **Validation**: Token checked on email link click
3. **Consumption**: Token marked as taken after use
4. **Expiration**: Automatic after 10 minutes via MySQL GENERATED column

## Spam Prevention Features

### Backend Protection
- ✅ 10-minute cooldown between emails per email address
- ✅ Unique tokens prevent replay attacks  
- ✅ Automatic token expiration
- ✅ Database-level constraints

### Frontend Experience
- ✅ Real-time countdown timers
- ✅ Form disabling during cooldown
- ✅ Clear messaging about wait times
- ✅ Progressive countdown updates

### Countdown Messages
```
"Please verify your email before logging-in.
New verification will be sent after 10 minutes."

"Please verify your email before logging-in. 
New verification will be sent after 9 minutes."

... (continues until 0)
```

## Usage Examples

### Check if email can be sent
```php
$emailTokenService = app(EmailTokenService::class);

if (!$emailTokenService->canSendEmail($email)) {
    $countdown = $emailTokenService->getCountdownMessage($email);
    return redirect()->back()
        ->with('error', $countdown['message'])
        ->with('countdown_seconds', $countdown['seconds_remaining']);
}
```

### Create and send email with token
```php
$emailToken = $emailTokenService->createEmailToken($email);
$verificationUrl = URL::temporarySignedRoute(
    'verification.verify',
    now()->addMinutes(60),
    ['id' => $encryptedId, 'token' => $emailToken->token]
);
// Send email...
```

### Validate token on verification
```php
$emailTokenService = app(EmailTokenService::class);
if (!$emailTokenService->validateAndConsumeToken($request->token)) {
    abort(403, 'Invalid or expired verification token');
}
```

## Maintenance

### Cleanup Command
```bash
php artisan email-tokens:clean
```

This can be scheduled in `app/Console/Kernel.php`:
```php
$schedule->command('email-tokens:clean')->hourly();
```

## Key Benefits

1. **Exact Specification Compliance**: Follows the original developer's requirements precisely
2. **Database-Level Expiration**: Uses MySQL GENERATED column for automatic expiration
3. **User-Friendly**: Clear countdown messages and disabled forms
4. **Secure**: SHA256 tokens with uniqueness constraints
5. **Maintainable**: Clean service layer with comprehensive logging
6. **Flexible**: Easy to extend for other email types

## Files Modified/Created

### New Files
- `app/Models/EmailToken.php`
- `app/Services/EmailTokenService.php`
- `app/Console/Commands/CleanExpiredEmailTokens.php`
- `app/Http/Controllers/Api/EmailTokenController.php`
- `public/js/email-countdown.js`

### Modified Files
- `app/Http/Controllers/AuthenticatedSessionController.php`
- `app/Http/Controllers/RegisteredUserController.php`
- `app/Http/Controllers/UserController.php`
- `routes/web.php`
- `routes/api.php`
- `resources/views/auth/login.blade.php`
- `resources/views/auth/forgot_password.blade.php`

This implementation provides robust email spam prevention while maintaining excellent user experience through progressive countdown displays.