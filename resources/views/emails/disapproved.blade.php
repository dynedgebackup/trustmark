<!DOCTYPE html>
<html>

<head>
    <meta charset="UTF-8">
    <title>Application Returned - ECPT</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            color: #222;
            background: #f9f9f9;
        }

        .container {
            background: #fff;
            padding: 24px;
            margin: 24px auto;
            max-width: 600px;
            border-radius: 8px;
            box-shadow: 0 2px 8px rgba(0, 0, 0, 0.05);
        }

        .header {
            font-size: 20px;
            font-weight: bold;
            margin-bottom: 16px;
        }

        .content {
            font-size: 16px;
            line-height: 1.6;
        }

        .reason {
            background: #f2f2f2;
            padding: 12px;
            border-radius: 6px;
            margin: 16px 0;
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
    <div class="container">
        <div class="header">
            {{ $business->business_name }}:
        </div>
        <div class="content">
            After evaluating your submitted documents, the DTI E-Commerce Bureau would like to inform you that your application for the E-Commerce Philippine Trustmark has been denied due to:
            <p>{{ $business->admin_remarks }}</p>
            
            You may resubmit through our website, ensuring that all documents comply with the applicable laws, rules and regulations.
            <br><br>
            If you have any further questions or need assistance, please feel free to contact the DTI E-Commerce Bureau at 
            <strong>
                <a href="mailto:trustmark@dti.gov.ph">TRUSTMARK@dti.gov.ph</a> or 
                <a href="tel:+63277913282">(+632) 7791-3282</a>
            </strong>
            <br><br>
            Thank you!
            <p><strong>DTI E-Commerce Bureau</strong></p>
        </div>
        <div class="footer">
            Do not reply to this auto-generated message
        </div>

    </div>
</body>

</html>