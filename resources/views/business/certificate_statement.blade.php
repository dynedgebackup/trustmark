
<!DOCTYPE html>
<html data-bs-theme="light" lang="en">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, shrink-to-fit=no">
    <title>Trustmark</title>
    <link rel="stylesheet" href="{{ public_path('assets/bootstrap/css/bootstrap.min.css') }}">
</head>


<style>
    @page {
        size: A4;
        margin: 0;
    }

    body {
        margin: 0;
        padding: 40px;
        /* Outer margin for A4 */
    }

    .certificate-container {
    width: calc(100% - 60px); 
    height: calc(100% - 60px);
    padding: 30px;
    margin: 0 auto;
    box-sizing: border-box;
}
    .title {
        text-align: center;
        font-weight: bold;
        font-size: 13px;
        margin-bottom: 10px;
    }
    .right {
        text-align: right;
        font-size: 11px;
        line-height: 16px;
        padding-right:60px;
    }
    .section-title {
        font-weight: bold;
        margin-top: 15px;
        margin-bottom: 5px;
        font-size: 12px;
    }
    .section-title2 {
        margin-top: 15px;
        margin-bottom: 5px;
        font-size: 12px;
    }
    table {
        width: 100%;
        border-collapse: collapse;
        margin-top: 8px;
        font-size: 11px;
    }
    .table td, .table th {
        border: 1px solid #000;
        /* padding: 4px; */
    }
    .no-border td {
        border: none;
        /* padding: 3px; */
    }
</style>
    <div class="certificate-container">
    <table width="100%" cellpadding="0" cellspacing="0">
        <tr>
            <td width="100%" align="left" style="padding-left: 0px; padding-bottom: 0;">
                <img src="{{ $logo }}" width="130" height="70">
                <br><br>
            </td>
        </tr>
       
    </table>

<div class="title">STATEMENT OF ACCOUNT</div>

<table width="90%">
    <tr>
        <td style="text-align:right; font-size:11px;">
        <br><br><br>
            Date: <strong>{{ \Carbon\Carbon::parse($business->date_approved)->format('F d, Y') }}</strong><br>
            Reference Number: <strong>{{ $business->trustmark_id ?? 'N/A' }}</strong>
        </td>
    </tr>
</table>


<table class="no-border" >
    <tr><td style="width: 11.5%;">
        </td>
        <td style="width: 78.5%;">
        <div class="section-title2"><br><strong>Applicant Information</strong><br>Business Name: <strong>{{ $business->business_name }}</strong><br>Owner/Representative: <strong>{{ $business->pic_name }}</strong><br>Business Address: <strong>{{ $complete_address }}</strong><br>Email Address: <strong>{{ $business->pic_email }}</strong><br>Contact Number: <strong>{{ $business->pic_ctc_no }}</strong></div>
            

            <div class="section-title">Transaction Details</div>

            <table class="table" style="width: 100%;">
                <thead>
                    <tr>
                        <th style="width: 70%;text-align: center;font-weight: bold;height:10px;">Item Description</th>
                        <th style="width: 30%;text-align: center;font-weight: bold;height:10px;">Amount</th>
                    </tr>
                </thead>
                <tbody>
                @forelse ($busines_fee as $fee)
                    <tr>
                        <td>{{ $fee->fee_name }}</td>
                        <td style="text-align: right;">P {{ number_format($fee->amount, 2) }}</td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="2" class="text-center text-muted">No fees available</td>
                    </tr>
                @endforelse
                    <tr>
                        <td style="text-align: right;"><strong>Total Amount Due</strong></td>
                        <td style="text-align: right;"><strong>P {{ number_format($busines_fee->sum('amount'), 2) }}</strong></td>
                    </tr>
                </tbody>
            </table>
            <br><br>
            <div class="section-title">Payment Instructions</div>
           

            
        </td>
    </tr>
</table>
<table width="100%" class="no-border">
            <tr><td width="11.5%"></td>
                <td width="78.5%">
                    <ol style="font-size: 10px; line-height: 18px;">
                        <li>Log in to your account at trustmark.dti.gov.ph</li>
                        <li>Go to the Dashboard, click Approved, or click My Application on the sidebar and search for your application.</li>
                        <li>Click the eye icon to view the application details.</li>
                        <li>Go to the Payment tab and click Make Payment.</li>
                        <li>Choose your preferred payment method and complete the transaction.</li>
                        <li>After successful payment, refresh the portal and re-access your application to download your Trustmark Certificate and QR Code.
                        </li>
                    </ol>
                    <div class="section-title">Notes:</div>
                </td><td width="12%"></td>
            </tr>
        </table>
        <table width="100%" class="no-border">
            <tr><td width="10%"></td>
                <td width="78.5%">
                    <ol style="font-size: 10px; line-height: 18px;list-style-type:none;">
                        <li>-   This document is system-generated and does not require a signature.</li>
                        <li>-   For concerns, email us at trustmark@dti.gov.ph or call (02) 7791-3282.
                        </li>
                    </ol>
                </td><td width="12%"></td>
            </tr>
        </table>
</div>