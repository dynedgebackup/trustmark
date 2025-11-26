<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>TRUSTMARK</title>
    <link rel="stylesheet" href="{{ public_path('assets/bootstrap/css/bootstrap.min.css') }}">
</head>

<body>
    <div class="d-flex min-vh-100 align-items-center">
        <div class="container shadow-lg p-3"
            style="border-radius: 15px;border: 21px solid rgb(62,67,134);width: 415px;">
            <div class="row p-3">
                <div class="col" style="text-align: center;margin-top: 20px;">
                    <div class="d-flex justify-content-center align-items-start gap-1">
                        <div class="text-center">
                            <img src="{{ public_path('assets/img/TRUSTMARK-SHIELD.png') }}" width="106" height="126" />
                        </div>
                        <div class="text-center d-flex flex-column align-items-center">
                            <img src="{{ public_path($business->qr_code) }}" width="95" height="95" />
                            <img class="mt-0" src="{{ public_path('assets/img/TRUSTMARK-REGISTERED-ONLY.png') }}" width="87" height="21" style="margin-top: -15px;" />
                            <p class="mb-0" style="font-size: 12px;">{{ $business->trustmark_id }}</p>
                        </div>
                    </div>
                </div>
            </div>
            @php
                $primaryUser = \App\Models\User::where('is_primary', 1)->first();
            @endphp
            @if($primaryUser && $primaryUser->profile_photos)
                <div class="row" style="margin-bottom: 0px!important;padding-bottom: 0px!important;">
                    <div class="col" style="text-align: center;">
                    <!-- <img class="img-fluid" src="{{ public_path('assets/img/signature_1752930308.png') }}" width="150" height="35" style="margin-bottom: 0px!important;padding-bottom: 0px!important;" /> -->
                        <img class="img-fluid" src="{{ public_path('storage/' . $primaryUser->profile_photos) }}" width="150" height="35" style="margin-bottom: 0px!important;padding-bottom: 0px!important;" />
                        <!-- <hr style="width: 150px; margin: 4px auto 0 auto; border-top: 1px solid #000;"> -->
                    </div>
                </div>
            @endif
            <div class="row" style="margin-top: 0px !important;padding-top: 0px!important;">
                <div class="col" style="text-align: center;margin-top: 0px !important;padding-top: 0px!important;">
                    <p class="mb-0" style="font-weight: bold;font-size: 14px;color:black">MA. CRISTINA A. ROQUE</p>
                    <p class="lh-1 mb-0" style="font-size: 12px;color:black">Secretary</p>
                </div>
            </div>
        </div>
    </div>

</body>

</html>