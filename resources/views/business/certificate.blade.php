<!DOCTYPE html>
<html data-bs-theme="light" lang="en">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, shrink-to-fit=no">
    <title>Trustmark</title>
    <link rel="stylesheet" href="{{ public_path('assets/bootstrap/css/bootstrap.min.css') }}">
    <link href="https://fonts.googleapis.com/css2?family=Montserrat:wght@400&display=swap" rel="stylesheet">
</head>


<style>
    
    @font-face {
        font-family: 'Montserrat';
        src: url('{{ public_path('fonts/montserrat/Montserrat-Regular.ttf') }}') format('truetype');
        font-weight: normal;
        font-style: normal;
        color:#4a4444;
    }

    @font-face {
        font-family: 'Baskerville';
        src: url('{{ public_path('fonts/baskerville/Baskerville-Regular.ttf') }}') format('truetype');
        font-weight: normal;
        font-style: normal;
        color:#4a4444;
    }

    body {
        font-family: 'Montserrat', sans-serif;
        color:#4a4444;
    }

    .trustmark-title {
        font-family: 'Baskerville', serif;
    }

    .text-justify {
        text-align: justify;
        color:#4a4444;
    }

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
</style>

<body>
    <div class="certificate-container">
    <table width="100%" cellpadding="0" cellspacing="0">
        <tr>
            <td width="100%" align="left" style="padding-left: 50px; padding-bottom: 0;">
                <img src="{{ public_path('assets/img/DTI-BP-transparent.png') }}" width="160" height="80">
            </td>
        </tr>
        <tr>
            <td width="100%" align="center" style="font-family: 'Baskerville', serif;font-size: 32px; font-weight: bold;color:#4a4444; line-height: normal;">
                E-COMMERCE PHILIPPINE<br>TRUSTMARK
            </td>
        </tr>
    </table>
        <table width="100%" style="padding-top: 10px;padding-bottom: 10px;">
            <tr>
                <td align="right" width="52%" style="padding:0px;">
                <br /><br />
                    <img src="{{ public_path('assets/img/TRUSTMARK-SHIELD.png') }}" width="100" height="126" style="margin-top: 50px;" />
                
                </td>
                <td align="left" width="48%;" style="padding:0px;">
                    <div style="text-align: left;font-size:12px;font-family: 'Montserrat', serif !important;font-size: 10px !important;color:#4a4444;">
                       
                        <table width="100%" style="padding:0px;" cellpadding="0" cellspacing="0">
                            <tr>
                                
                                <td align="left" style="padding:0px;" left="10">
                                    @php
                                        $qr_code = str_replace('storage/', '', $business->qr_code);
                                        $filePath = asset('storage/app/public/' . $qr_code);

                                        $fileSystemPath = public_path('storage/' . $qr_code);
                                        if(file_exists($filePath)){
                                            $qr_code = asset('storage/' . $qr_code);
                                        }
                                    @endphp
                                    <img src="{{ $qr_code }}" width="85" />
                                   <!-- <img src="http://localhost/trustmark/storage/app/public/document-upload/qr_code/qr_1_25072013325498.png" width="85"/> -->
                                </td>
                            </tr>
                            <tr>
                               <td style="padding:0px;margin-left:20px;">
                                   <img src="{{ public_path('assets/img/TRUSTMARK-REGISTERED-ONLY.png') }}" width="80" 
                                            /><br />
                                        {{ $business->trustmark_id }}
                                </td>
                            </tr>
                        </table>
                    </div>
                </td>
            </tr>
        </table>

        <div class="row mt-3">
            <div class="mb-0" style="text-align:center;font-family:'Montserrat', serif !important">This certifies that</div>
            @php
                    $nameLength = strlen($business->business_name);
                    if ($nameLength <= 30) {
                        $fontSize = 20;
                    }
                    elseif ($nameLength <= 38) {
                        $fontSize = 18;
                    } elseif ($nameLength <= 50) {
                        $fontSize = 16;
                    } elseif ($nameLength <= 70) {
                        $fontSize = 15;
                    } else {
                        $fontSize = 14;
                    }
                @endphp
                <table width="100%" cellpadding="0" cellspacing="0">
                    <tr>
                        <td width="10%"></td>
                        <td width="80%" align="left" style=" padding-bottom: 0;font-size: 8px !important;">
                            <div style="font-weight: bold;color:#4a4444;font-family: 'Montserrat', sans-serif; font-size: {{ $fontSize }}px;word-wrap: break-word !important;padding-left:10px;padding-right:10px;text-align:center;">
                                {{ $business->business_name }}
                            </div>
                        </td>
                        <td width="10%"></td>
                    </tr>
                
                </table>
                
                <table width="100%" cellpadding="0" cellspacing="0">
                    <tr>
                        <td width="10%"></td>
                        <td width="80%" align="left" style=" padding-bottom: 0;font-size: 8px !important;">
                        <div class="text-justify mb-0" style="font-size:10px;font-family: 'Montserrat', sans-serif;">Has successfully complied with the E-Commerce
                        Philippine Trustmark requirements set forth under Department Administrative Order No. <span
                            style="text-decoration: underline;">25-07</span> pursuant to Republic Act No. 11967 or the
                        Internet Transactions Act of 2023.</div>
                        </td>
                        <td width="10%"></td>
                    </tr>
                    
                </table>
                <table width="100%" cellpadding="0" cellspacing="0">
                    <tr>
                        <td width="10%"></td>
                        <td width="80%" align="left" style=" padding-bottom: 0;font-size: 8px !important;">
                        <div class="text-justify mb-0" style="font-size:10px;font-family: 'Montserrat', sans-serif;">The DTI E-Commerce Bureau grants <strong style="word-wrap: break-word !important;color:#000;"> {{ $business->business_name }} </strong>this E-Commerce Philippine
                        Trustmark—a recognition for online merchants and platforms that commit to trustworthiness,
                        safety and adherence to fair e-commerce practices.</div>
                        </td>
                        <td width="10%"></td>
                    </tr>
                    
                </table>
                <table width="100%" cellpadding="0" cellspacing="0" border="0">
                    <tr>
                        <td width="9%"></td> 
                        <td width="80%" style="font-size:10px; font-family: montserrat; text-align: justify;">
                        <div class="text-justify mb-0" style="font-family: 'Montserrat', sans-serif;">
                            <strong style="color:#000;">Security No:</strong> {{ $business->trustmark_id }}<br>
                            <strong style="color:#000;">Date of Issuance:</strong>
                            {{ \Carbon\Carbon::parse($business->date_issued)->format('d M Y') }}<br>
                            <strong style="color:#000;">Validity:</strong> One year from the date of issuance<br><br>
                            Signed this <u>{{ \Carbon\Carbon::parse($business->date_issued)->format('d M Y') }}</u> at the
                            City of Makati, Republic of the Philippines.
                        </div>
                        </td>
                        <td width="10%"></td>
                    </tr>
                </table>
               
        
            </div>
        </div>
        
    </div>
</body>

</html>