<!DOCTYPE html>
<html>

<head>
    <meta charset="UTF-8">
    <title>ECPT Email Verification</title>
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
            Dear {{ $user->name }},
        </div>
        <div class="content">
            <p>You are receiving this email because an account registration was initiated using this email address on
                the <strong>E-Commerce Phillipine Trustmark</strong></p>
            <p>
                As part of the DTI E-Commerce Bureau's (ECB) verification process and commitment to data security, the
                DTI ECB kindly requests that you confirm the validity of this email address to complete your
                registration.
            </p>
            <p>
                To verify your email address and activate your account, please click the button of link provided below:
            </p>
            <p>
                <a href="{{ $verificationUrl }}"
                    style="display: inline-block; background: #007bff; color: #fff; padding: 12px 24px; border-radius: 4px; text-decoration: none; font-weight: bold;">
                    Verify My Email Address
                </a>
            </p>
            <p>
                (Alternatively, you can copy and paste the following URL into your browser: <br>
                {{ $verificationUrl }})
            </p>
            <p>
                This step ensures that the DTI ECB is able to communicate with you effectively and that your account is
                protected against unathorized access.
            </p>
            <p>
                If you did not initiate this registration, please disregard this message. No further action is required
                on your part, and your email address will not be retained in the DTI ECB's records.
            </p>
            <p>
                Should you required any assistance or have any questions regarding this process, please feel free to
                contact the DTI ECB support team at Trustmark@dti.gov.ph or call us at 7791-3282.
            </p>
            <p>
                Thank you for your attention and cooperation.
            </p>
            <p>
                The DTI ECB looks forward to your successful registration and continued engagement.
            </p>
            <p>
                Sincerely,
            </p>
            <p>
                DTI ECB
            </p>
        </div>
    </div>
</body>

</html>