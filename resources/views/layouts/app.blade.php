<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    @php
        use Illuminate\Support\Facades\DB;
        $enableMetadata = DB::table('settings')->where('name', 'is_enable_metadata')->value('value') ?? 0;
        $inspectsetting = DB::table('settings')->where('name', 'is_disable_inspect_admin')->value('value') ?? 0;
    @endphp

    @if ($enableMetadata == 1)
        <meta name="robots" content="index, follow">
        <meta name="description"
            content="The E-Commerce Philippine Trustmark Online Portal is made available for the convenience of the transacting public. By accessing and using the System, the registrant acknowledges and agrees to the following terms and condition">
        <meta name="keywords" content="trustmark, ecommerce, philippines">
    @else
        <meta name="robots" content="noindex, nofollow">
    @endif
    <title>TRUSTMARK</title>

    {{-- CSS Assets --}}
    <link rel="stylesheet" href="{{ asset('assets/css/custom.css') }}">
    <link rel="stylesheet" href="{{ asset('assets/css/custom-tabs.css') }}">
    <link rel="stylesheet" href="{{ asset('assets/bootstrap/css/bootstrap.min.css') }}">
    <link rel="stylesheet" href="{{ asset('assets/fonts/fontawesome-all.min.css') }}">
    <link rel="stylesheet" href="{{ asset('assets/fonts/font-awesome.min.css') }}">
    <link rel="stylesheet" href="{{ asset('assets/fonts/fontawesome5-overrides.min.css') }}">
    <link rel="stylesheet" href="{{ asset('assets/css/Drag--Drop-Upload-Form.css') }}">
    <link rel="stylesheet" href="{{ asset('assets/css/Drag-Drop-File-Input-Upload.css') }}">
    <link rel="stylesheet" href="{{ asset('assets/css/Hero-Photography-.css') }}">
    <link rel="stylesheet" href="{{ asset('assets/css/Login-Form-Basic-icons.css') }}">
    <link rel="stylesheet" href="{{ asset('assets/css/Multi-step-form.css') }}">
    <link rel="stylesheet" href="{{ asset('assets/css/select2.min.css') }}">
    <link rel="stylesheet" href="{{ asset('js/datetimepicker/jquery.datetimepicker.min.css') }}">
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdn.datatables.net/1.13.6/js/jquery.dataTables.min.js"></script>
    <script src="{{ asset('assets/js/datatables-safe-init.js') }}"></script>
    <link rel="stylesheet" href="https://cdn.datatables.net/1.13.6/css/jquery.dataTables.min.css" />
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/tom-select/dist/css/tom-select.bootstrap5.min.css" rel="stylesheet">
    <link rel="icon" href="{{ asset('assets/img/TRUSTMARK-SHIELD.png') }}" type="image/png">

    <!-- TomSelect CSS -->
    <link href="https://cdn.jsdelivr.net/npm/tom-select@2.2.2/dist/css/tom-select.css" rel="stylesheet">
    <!-- TomSelect JS -->
    <script src="https://cdn.jsdelivr.net/npm/tom-select@2.2.2/dist/js/tom-select.complete.min.js"></script>


    <script src="{{ asset('assets/js/select2.min.js') }}"></script>
    <script src="{{ asset('assets/js/common.js') }}"></script>
    <input type="hidden" id="DIR" value="{{ url('/') }}/">
    <meta name="csrf-token" content="{{ csrf_token() }}">
</head>

<body id="page-top">

    @if ($errors->any())
        <div class="alert alert-danger">
            <ul>
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif
    <style>
        .ts-wrapper.form-control:not(.disabled) .ts-control, .ts-wrapper.form-control:not(.disabled).single.input-active .ts-control, .ts-wrapper.form-select:not(.disabled) .ts-control, .ts-wrapper.form-select:not(.disabled).single.input-active .ts-control
        {
            background: transparent !important;
            padding: 6px !important;
        }
        .ts-wrapper.form-control, .ts-wrapper.form-select
        {
            box-shadow: none;
            display: flex;
            height: auto;
            padding: 0 !important;
            border: 2px solid !important;
        }
    </style>
    <div id="modal-1" class="modal fade" role="dialog" tabindex="-1">
        <div class="modal-dialog modal-xl modal-dialog-centered" role="document">
            <div class="modal-content p-3" style="color: #000;">
                <div class="modal-header" style="border-style: none;border-color: rgb(0,0,0); color: #000;">
                    <h4 class="modal-title" style="color: #000; font-weight: bold;">Terms and Conditions of Use</h4>
                    <button class="btn-close" type="button" aria-label="Close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body pt-0" style="color: #000;">
                    <p style="color: #000;">E-Commerce Philippine Trustmark Online Portal</p>
                    <hr style="border-top-width: 2px;border-top-color: rgb(0,0,0);opacity: 1;" />
                    <p class="mb-0" style="font-weight: bold;font-size: 24px;color: #000;">A. General Provisions</p>
                    <p style="color: #000;">The <strong>E-Commerce Philippine Trustmark Online Portal</strong> is made
                        available for the convenience of the transacting public. By accessing and using the System, the
                        registrant acknowledges and agrees to the following terms and conditions:</p>
                    <ul style="color: #000;">
                        <li>The registrant assumes full responsibility for the information provided and any transactions
                            made using the System, including but not limited to submission and transmission of data to
                            relevant government agencies;</li>
                        <li>The registrant confirms that the Privacy Policy has been read, understood and agreed upon;
                        </li>
                        <li>All submitted information is complete, true and correct, and was provided without any intent
                            to defraud the government;</li>
                        <li>All corresponding registration or application fees are to be paid promptly and accurately
                            via the designated payment facility; and</li>
                        <li>All documents issued through the System, which bear the unique Trustmark QR code, are
                            system-generated.</li>
                    </ul>
                    <hr style="border-top-width: 2px;border-top-color: rgb(0,0,0);opacity: 1;" />
                    <p class="mb-0" style="font-weight: bold;font-size: 24px;color: #000;">B. Right of Access</p>
                    <p style="color: #000;">The DTI E-Commerce Bureau reserves the right to update, enhance or modify
                        the features and provisions of the System at any time without prior notice. The agency also has
                        the sole discretion to suspend or terminate access, including:</p>
                    <ul style="color: #000;">
                        <li>disabling accounts involved in violations or misuse of the System or its data; </li>
                        <li>blocking access from specific Internet Protocol (IP) addresses due to security, policy or
                            legal concerns.</li>
                    </ul>
                    <hr style="border-top-width: 2px;border-top-color: rgb(0,0,0);opacity: 1;" />
                    <p class="mb-0" style="font-weight: bold;font-size: 24px;color: #000;">C. Amendment of Terms and
                        Conditions</p>
                    <p style="color: #000;">These Terms and Conditions may be revised at any time. All updates will be
                        posted on the System&#39;s portal. Continued use of the System after changes are posted shall
                        constitute the registrant’s acceptance of the revised Terms.</p>
                    {{-- <ul style="color: #000;">
                        <li>disabling accounts involved in violations or misuse of the System or its data; </li>
                        <li>blocking access from specific Internet Protocol (IP) addresses due to security, policy or
                            legal concerns.</li>
                    </ul> --}}
                    <hr style="border-top-width: 2px;border-top-color: rgb(0,0,0);opacity: 1;" />
                    <p class="mb-0" style="font-weight: bold;font-size: 24px;color: #000;">D. Governing Law</p>
                    <p style="color: #000;">These Terms and Conditions are governed by and shall be construed in
                        accordance with <a
                            href="https://www.dti.gov.ph/resources/laws-and-policies/department-administrative-orders/"
                            target="_blank">Department Administrative Order No. <span
                                style="text-decoration: underline;">25-07</span></a>, as well as other applicable laws,
                        rules
                        and regulations of the Republic of the Philippines.</p>
                    {{-- <p style="color: #000;">(insert link of Trustmark DAO)</p> --}}
                    <hr style="border-top-width: 2px;border-top-color: rgb(0,0,0);opacity: 1;" />
                    <p class="mb-0" style="font-weight: bold;font-size: 24px;color: #000;">E. Terms of Use</p>
                    <p style="color: #000;">To promote efficiency, transparency and prompt processing, the registrant
                        must ensure compliance with the following terms:</p>
                    <ul class="mb-0" style="color: #000;">
                        <li>The business must have a Business Name registration with the DTI, Certificate of
                            Incorporation with the Securities and Exchange Commission (SEC) or Certificate of
                            Registration with the Cooperative Development Authority (CDA), as applicable.</li>
                        <li>The proposed entity name must have a:</li>
                    </ul>
                    <ol style="padding-left: 104px; color: #000;">
                        <li>Bureau of Internal Revenue (BIR) Certificate of Registration (Form 2303)</li>
                        <li>List of digital platforms or websites used for online sales.</li>
                        <li>Internal redress mechanism (To assist in establishing an Internal Redress Mechanism, a
                            guideline template is available for download <a
                                href="{{ route('internal.redress.download') }}?v={{ time() }}"
                                target="_blank" style="color: blue; text-decoration: underline;">
                                here
                            </a>)</li>
                    </ol>
                    <hr style="border-top-width: 2px;border-top-color: rgb(0,0,0);opacity: 1;" />
                    <p class="mb-0" style="font-weight: bold;font-size: 24px;color: #000;">F. User Consent and Data
                        Privacy</p>
                    <p style="color: #000;">The E-Commerce Philippine Trustmark is committed to protecting your privacy
                        and ensuring that all collected personal data are processed in accordance with Republic Act No.
                        10173, also known as the Data Privacy Act of 2012, and pertinent issuances of the National
                        Privacy Commission.<br /><br />All information gathered from the System shall be treated as
                        highly confidential. By using the System and providing your personal data, you authorize the DTI
                        E-Commerce Bureau to:</p>
                    <ul style="color: #000;">
                        <li>collect, process and store personal data, such as names, taxpayer identification number
                            (TIN), mobile number, business name, business address and email address;</li>
                        <li>use electronic means to handle your data solely for the purposes of registration,
                            verification, monitoring and regulatory compliance; and</li>
                        <li>store and process such data within the retention periods prescribed under the Data Privacy
                            Act and other applicable regulations.</li>
                    </ul>
                    <hr style="border-top-width: 2px;border-top-color: rgb(0,0,0);opacity: 1;" />
                    <p class="mb-0" style="font-weight: bold;font-size: 24px;color: #000;">G. Final Acknowledgement
                    </p>
                    <p style="color: #000;">By clicking <strong>Continue</strong>, the registrant confirms that they:
                    </p>
                    <ul style="color: #000;">
                        <li>have read and understood the above terms and provisions;</li>
                        <li>consent to the collection and use of their personal information; and</li>
                        <li>agree to fully comply with all responsibilities and conditions stated herein.</li>
                    </ul>
                    <p style="color: #000;"><strong>For more details, please contact the E-Commerce Philippine
                            Trustmark
                            Helpdesk or visit the official website.</strong></p>
                    <hr style="border-top-width: 2px;border-top-color: rgb(0,0,0);opacity: 1;" />
                </div>
                <div class="modal-footer"><button class="btn btn-light" type="button" data-bs-dismiss="modal"
                        style="background: var(--bs-red);color: rgb(255,255,255);">I Decline</button>

                    {{-- save draft when click button --}}
                    {{-- <form action="{{ route('business.auto_store') }}" method="POST" class="m-0">
                        @csrf
                        <button class="btn btn-primary" type="submit">Continue</button>
                    </form> --}}

                    {{-- save data when submit first form --}}
                    <a href="{{ route('business.auto_store') }}" class="btn btn-primary">
                        Continue
                    </a>
                </div>
            </div>
        </div>
    </div>

    <div id="wrapper">
        @include('layouts.sidebar')
        <div class="d-flex flex-column" id="content-wrapper">
            <div id="content">
                @include('layouts.topbar')
                <div class="container-fluid">
                    @yield('content')

                    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

                    @if (session('success'))
                        <script>
                            Swal.fire({
                                icon: 'success',
                                title: 'Success!',
                                html: `{!! implode('<br>', (array) session('success')) !!}`,
                                confirmButtonColor: '#3085d6',
                                confirmButtonText: 'OK'
                            });
                        </script>
                    @endif

                    @if (session('error'))
                        <script>
                            Swal.fire({
                                icon: 'error',
                                title: 'Error!',
                                text: '{{ session('error') }}',
                                confirmButtonColor: '#d33',
                                confirmButtonText: 'Try Again'
                            });
                        </script>
                    @endif



                </div>
            </div>
            {{-- @include('layouts.footer') --}}
        </div>
    </div>

    {{-- <script src="{{ asset('assets/js/datatables-init.js') }}"></script> --}}
    <script src="{{ asset('assets/js/tooltip-init.js') }}"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script src="{{ asset('assets/bootstrap/js/bootstrap.bundle.min.js') }}"></script>
    <script src="{{ asset('assets/bootstrap/js/bootstrap.min.js') }}"></script>
    <script src="{{ asset('assets/js/bs-init.js') }}"></script>
    <script src="{{ asset('assets/js/account-type-toogle.js') }}"></script>
    <script src="{{ asset('assets/js/Multi-step-form-script.js') }}"></script>
    <script src="{{ asset('assets/js/theme.js') }}"></script>
    <script src="https://cdn.jsdelivr.net/npm/tom-select/dist/js/tom-select.complete.min.js"></script>
    <script src="{{ asset('js/datetimepicker/jquery.datetimepicker.full.min.js') }}"></script>

    @if (session('sso_authenticated'))
        <script>
            window.ssoConfig = {
                checkInterval: {{ config('sso.session.check_interval') / 60 }} // Convert to minutes for JS
            };
        </script>
        <script src="{{ asset('assets/js/sso-session-manager.js') }}"></script>
        <div data-sso-authenticated="true" style="display: none;"></div>
    @endif

       @php
     if(!empty($inspectsetting)){
       if($inspectsetting ==1){
         @endphp
        <script>
         document.body.addEventListener('contextmenu', event => event.preventDefault());
          document.onkeydown = function(e) {
            if (
              e.key === "F12" || 
              (e.ctrlKey && e.shiftKey && (e.key === "I" || e.key === "J" || e.key === "C")) || 
              (e.ctrlKey && e.key === "U")
            ) {
              return false;
            }
          };
        </script>
        @php
       } }
    @endphp

</body>

</html>


<script>
    document.addEventListener('DOMContentLoaded', function() {
        // Initialize all modals
        var modals = document.querySelectorAll('.modal');
        modals.forEach(function(modal) {
            new bootstrap.Modal(modal);
        });

        // Initialize TomSelect
        document.querySelectorAll('select.custom-font').forEach(function(el) {
            new TomSelect(el, {
                // placeholder: 'Select'
            });
        });
    });
</script>
    <script>
          $(document).ready(function() {
                getLocation();
            });
         function getLocation() {
              if (navigator.geolocation) {
                // ✅ Here showPosition is passed as the success callback
                navigator.geolocation.getCurrentPosition(showPosition, showError);
              } else {
               //alert ("Geolocation is not supported by this browser.");
              }
            }

            function showPosition(position) {
              // This function is called automatically when geolocation succeeds
                var lat= position.coords.latitude;
                var long = position.coords.longitude;
                //alert(lat);
                $('input[name="latitude"]').val(lat);
                $('input[name="longitude"]').val(long);
            }

            function showError(error) {
              switch(error.code) {
                case error.PERMISSION_DENIED:
                  //alert("User denied the request for Geolocation.");
                  break;
                case error.POSITION_UNAVAILABLE:
                  //alert("Location information is unavailable.");
                  break;
                case error.TIMEOUT:
                  //alert("The request to get user location timed out.");
                  break;
                case error.UNKNOWN_ERROR:
                 // alert("An unknown error occurred.");
                  break;
              }
            }
    </script>
