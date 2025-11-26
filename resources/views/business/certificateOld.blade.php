<!DOCTYPE html>
<html data-bs-theme="light" lang="en">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, shrink-to-fit=no">
    <title>Trustmark</title>
    <link rel="stylesheet" href="{{ public_path('assets/bootstrap/css/bootstrap.min.css') }}">
</head>

<style>
    @font-face {
        font-family: 'Montserrat';
        src: url('{{ public_path('fonts/montserrat/Montserrat-Regular.ttf') }}') format('truetype');
        font-weight: normal;
        font-style: normal;
    }

    @font-face {
        font-family: 'Baskerville';
        src: url('{{ public_path('fonts/baskerville/Baskerville-Regular.ttf') }}') format('truetype');
        font-weight: normal;
        font-style: normal;
    }

    body {
        font-family: 'Montserrat', sans-serif;
    }

    .trustmark-title {
        font-family: 'Baskerville', serif;
    }

    .text-justify {
        text-align: justify;
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
        width: calc(210mm - 80px);
        /* A4 width minus outer margins */
        height: calc(292mm - 80px);
        /* A4 height minus outer margins */
        border: 10px solid #000;
        padding: 30px;
        margin: 0 auto;
        box-sizing: border-box;
    }
</style>

<body>
    <div class="certificate-container">
        <div class="row">
            <div class="col"><img src="{{ public_path('assets/img/DTI-BP-transparent.png') }}" width="181" height="98">
            </div>
        </div>
        <div class="row mt-3 mb-3">
            <div class="col" style="text-align: center;">
                <p class="lh-1 mb-0 trustmark-title" style="font-size: 38px;font-weight: bold;">E-COMMERCE
                    PHILIPPINE<br>TRUSTMARK</p>
            </div>
        </div>
        <div class="row mt-2">
            <div class="col" style="text-align: center;">
                <div class="d-flex justify-content-center align-items-start gap-3">
                    <div class="text-center"><img src="{{ public_path('assets/img/TRUSTMARK-SHIELD.png') }}" width="106"
                            height="126" /></div>
                    <div class="text-center d-flex flex-column align-items-center"><img
                            src="{{ public_path($business->qr_code) }}" width="95" height="95" /><img class="mt-0"
                            src="{{ public_path('assets/img/TRUSTMARK-REGISTERED-ONLY.png') }}" width="87" height="21"
                            style="margin-top: -15px;" />
                        <p class="mb-0" style="font-size: 12px;">{{ $business->trustmark_id }}</p>
                    </div>
                </div>
            </div>
        </div>
        <div class="row mt-3">
            <div class="col text-center">
                <p class="mb-0">This certifies that</p>
                @php
                    $nameLength = strlen($business->business_name);
                    if ($nameLength <= 30) {
                        $fontSize = 36;
                    }
                    elseif ($nameLength <= 38) {
                        $fontSize = 30;
                    } elseif ($nameLength <= 50) {
                        $fontSize = 28;
                    } elseif ($nameLength <= 70) {
                        $fontSize = 22;
                    } else {
                        $fontSize = 16;
                    }
                @endphp
                <p class="mb-0" style="font-weight: bold; font-size: {{ $fontSize }}px;word-wrap: break-word !important;">
                    {{ $business->business_name }}
                </p>
                <div class="mt-3 px-4">
                    <p class="text-justify mb-0" style="font-size:14px;">Has successfully complied with the E-Commerce
                        Philippine Trustmark requirements set forth under Department Administrative Order No. <span
                            style="text-decoration: underline;">25-07</span> pursuant to Republic Act No. 11967 or the
                        Internet Transactions Act of 2023.</p>
                        <p class="text-justify mb-0" style="font-size:14px;">The
                        DTI E-Commerce Bureau grants <strong style="word-wrap: break-word !important;"> {{ $business->business_name }} </strong>this E-Commerce Philippine
                        Trustmark—a recognition for online merchants and platforms that commit to trustworthiness,
                        safety and adherence to fair e-commerce practices.</p>
                </div>
                <div class="mt-3 px-4">
                    <p class="text-justify mb-0">
                        <strong>Security No:</strong> {{ $business->trustmark_id }}<br>
                        <strong>Date of Issuance:</strong>
                        {{ \Carbon\Carbon::parse($business->date_issued)->format('d M Y') }}<br>
                        <strong>Validity:</strong> One year from the date of issuance<br><br>
                        Signed this <u>{{ \Carbon\Carbon::parse($business->date_issued)->format('d M Y') }}</u> at the
                        City of Makati, Republic of the Philippines.
                    </p>
                </div>
            </div>
        </div>
        @php
            $primaryUser = \App\Models\User::where('is_primary', 1)->first();
        @endphp
        @if($primaryUser && $primaryUser->profile_photos)
            <div class="row" style="margin-top: 18px;">
                <div class="col" style="text-align: center;">
                    <img class="img-fluid" src="{{ public_path('storage/' . $primaryUser->profile_photos) }}" width="190"
                        height="30" style="margin-bottom: 0px;" />
                    <!-- <hr style="width: 220px; margin: 4px auto 0 auto; border-top: 1px solid #000;"> -->
                </div>
            </div>
            <div class="row" style="margin-top: 0px;">
                <div class="col" style="text-align: center;">
                    <p class="mb-0" style="font-weight: bold;">MA. CRISTINA A. ROQUE</p>
                    <p class="mb-0">Secretary</p>
                </div>
            </div>
        @else
            <div class="row" style="margin-top: 90px;">
                <div class="col" style="text-align: center;">
                    <p class="mb-0" style="font-weight: bold;">MA. CRISTINA A. ROQUE</p>
                    <p class="mb-0">Secretary</p>
                </div>
            </div>
        @endif
    </div>
</body>

</html>