<!DOCTYPE html>
<html>

<head>
    <meta charset="UTF-8">
    <title>ECPT Application Approved</title>
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
                The DTI E-Commerce Bureau is pleased to inform you that your application for E-Commerce Philippine
                Trustmark (TRUSTMARK) has been <strong>APPROVED</strong>.
            </p>
            <p>
                To proceed, kindly settle the processing fees through the TRUSTMARK web application’s online payment facility. Please retrieve your application’s reference number from the previous email that was previously sent to you. The TRUSTMARK Certificate will be issued once payment has been confirmed.
            </p>
            <p>
                If you have any further questions or need assistance, please feel free to contact the DTI E-Commerce
                Bureau at 
                <strong>
                    <a href="mailto:trustmark@dti.gov.ph">TRUSTMARK@dti.gov.ph</a> or 
                    <a href="tel:+63277913282">(+632) 7791-3282</a>
                </strong>
            </p>
            <p>Thank you!</p>
            <p><strong>DTI E-Commerce Bureau</strong></p>
        </div>
        <div class="footer">
            Do not reply to this auto-generated message.
        </div>
    </div>
</body>

</html>