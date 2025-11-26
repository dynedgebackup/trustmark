<!DOCTYPE html>
<html>

<head>
    <meta charset="UTF-8">
    <title>FOLLOW UP OF RETURNED APPLICATIONS</title>
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
            border-top: 1px solid #eee;
            margin-top: 32px;
            padding-top: 16px;
            font-size: 13px;
            color: gray;
            text-align: center;
        }
    </style>
</head>

<body>
    <div class="email-container">
        <div class="header">
            {{ $business->business_name }}:
        </div>
        <div class="content">
            <p>Good day!</p>
            <p>
                This serves as the final notification regarding your E-Commerce Philippine Trustmark application with Reference Number <strong>{{ $business->trustmark_id }}</strong>.
            </p>
            <p>
                Upon checking, our records show that your Trustmark application is still incomplete, as the required next step has not been fulfilled.
            </p>
            <p>
                Please be advised that we will be archiving your application. Should you wish to apply for the Trustmark in the future, you are welcome to submit a new application through the Trustmark Portal at any time.
            </p>
            <p>
                For any questions or assistance, you may reach us at 
                <strong>
                    <a href="mailto:trustmark@dti.gov.ph">TRUSTMARK@dti.gov.ph</a> or 
                    <a href="tel:+63277913282">(+632) 7791-3282</a>
                </strong>
            </p>
            <p>Thank you for your attention.</p>
            <p><strong>DTI E-Commerce Bureau</strong></p>
        </div>
        <div class="footer">
            Do not reply to this auto-generated message.
        </div>
    </div>
</body>

</html>