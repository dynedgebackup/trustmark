<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Application PNG</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <link rel="stylesheet" href="{{ asset('assets/bootstrap/css/bootstrap.min.css') }}">
    <script src="https://cdnjs.cloudflare.com/ajax/libs/html2canvas/1.4.1/html2canvas.min.js"></script>
    <script src="https://code.jquery.com/jquery-3.7.0.min.js"></script>
    <style>
       
        body {
            background: #fff;
            margin: 0;
            overflow: hidden;
        }
    </style>
</head>
<body>

<div id="captureArea" style="position: absolute; top: -9999px; left: -9999px; width: 500px; height: 450px; border-radius: 15px; background: #fff; padding: 50px;">
    <div class="row p-3" style="border-radius: 15px;border: 21px solid rgb(62,67,134);width: 415px;">
        <div class="col text-center">
            <div class="col" style="text-align: center;margin-top: 20px;">
            <div class="container  p-3"
            >
                <div class="d-flex justify-content-center align-items-start gap-1">
                    <div class="text-center">
                        <img src="{{ asset('assets/img/TRUSTMARK-SHIELD.png') }}" width="106" height="126" />
                    </div>
                    <div class="text-center d-flex flex-column align-items-center">
                        @php
                            $qr_code = str_replace('storage/', '', $business->qr_code);
                            $filePath = asset('storage/app/public/' . $qr_code);

                            $fileSystemPath = public_path('storage/' . $qr_code);
                            if(file_exists($filePath)){
                                $qr_code = asset('storage/' . $qr_code);
                            }
                        @endphp
                        <img src="{{ $qr_code }}" width="95" height="95" />
                        <!-- <img src="{{ asset('assets/img/qr_1_25072013313523.png') }}" width="95" height="95" /> -->
                        <img class="mt-0" src="{{ asset('assets/img/TRUSTMARK-REGISTERED-ONLY.png') }}" width="87" height="21" />
                        <p class="mb-0" style="font-size: 12px;">{{ $business->trustmark_id }}</p>
                    </div>
                </div>
            </div>
        </div>
    </div>

    @php
        $primaryUser = \App\Models\User::where('is_primary', 1)->first();
    @endphp
    @if($primaryUser && $primaryUser->profile_photos)
        <div class="row mb-0 pb-0">
            <div class="col text-center">
                <img src="{{ asset('storage/' . $primaryUser->profile_photos) }}" width="150" height="100%" />
            </div>
        </div>
    @endif

    <div class="row mt-0 pt-0">
        <div class="col text-center">
            <p class="mb-0 fw-bold" style="font-size: 15px; color: black;">MA. CRISTINA A. ROQUE</p>
            <p class="lh-1 mb-0" style="font-size: 13px; color: black;">Secretary</p>
        </div>
    </div>
</div>

<!-- Automatically download as PNG -->
<!-- HTML2Canvas Auto-Download PNG Without Closing Page -->
<!-- Include html2canvas -->
<!-- Include html2canvas -->
<script src="https://cdn.jsdelivr.net/npm/html2canvas@1.4.1/dist/html2canvas.min.js"></script>

<script>
    window.onload = function () {
        setTimeout(() => {
            const captureElement = document.getElementById('captureArea');
            if (!captureElement) {
                console.error('Element #captureArea not found.');
                return;
            }

            const timestamp = Date.now();

            html2canvas(captureElement, {
                useCORS: true,
                allowTaint: false,
                scale: 2
            }).then(function (canvas) {
                const imgData = canvas.toDataURL('image/png');
                const link = document.createElement('a');
                link.href = imgData;
                link.download = `TMKQR_${timestamp}.png`;
                document.body.appendChild(link);
                link.click();
                document.body.removeChild(link);

                // Close tab after short delay
                setTimeout(() => {
                    window.open('', '_self');
                    window.close();
                }, 1500);
            }).catch(err => {
                console.error('Canvas generation failed:', err);
            });
        }, 1000);
    };
</script>


