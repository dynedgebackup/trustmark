<!DOCTYPE html>
<html>

<head>
    <meta charset="UTF-8">
    <title>Forgot Password</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            background: #f7f7f7;
            margin: 0;
            padding: 0;
        }

        .email-container {
            background: #fff;
            max-width: 600px;
            margin: 40px auto;
            padding: 32px 24px;
            border-radius: 8px;
            box-shadow: 0 2px 8px rgba(0, 0, 0, 0.05);
        }

        .header {
            font-size: 20px;
            font-weight: bold;
            color: #2c3e50;
            margin-bottom: 24px;
        }

        .content {
            font-size: 16px;
            color: #333;
            line-height: 1.7;
        }

        .footer {
            margin-top: 32px;
            font-size: 14px;
            color: #888;
        }
    </style>
</head>

<body>
    <div class="email-container">
        <div class="header">
            Dear {{ $user->name }}:
        </div>
        <div class="content">
            <p>Good day!</p>
            <p>
                We received a request to reset your password. Use the OTP below to proceed:
            </p>
            <p>
                {{-- <strong>{{ $passwordOtp->otp }}</strong> --}}
                <strong>{{ $otp }}</strong>

            </p>
            <p>
                This OTP will expire in 10 minutes.
            </p>
            <p>If you didn't request a password reset, you can safely ignore this email.</p>
            <p>Thank you!</p>
        </div>
    </div>
</body>

</html>