@extends('layouts.auth')
@section('title', 'Login')
<style>
    html,
    body {
        height: 100vh;
        margin: 0;
        overflow: hidden;
        /* Background image full screen */
        background: url('{{ asset('assets/img/banner.jpg') }}') no-repeat center center fixed;
        background-size: cover;
        font-family: sans-serif;
    }

    /* Center container vertically and horizontally */
    .login-wrapper {
        height: 100dvh;
        display: flex;
        justify-content: center;
        align-items: center;
        padding: 20px;
        background: #ffffffb3;
        backdrop-filter: blur(10px);
    }

    /* Semi-transparent card so bg shows behind */
    .login-card {
        background-color: rgba(255, 255, 255, 0.98);
        border-radius: 0.375rem;
        padding: 2rem;
        box-shadow: 0 4px 12px rgba(0, 0, 0, 0.15);
        max-width: 400px;
        width: 100%;
        margin-bottom: 130px;
    }

    .login-card .btn-primary {
        background-color: #09325d;
        border-color: #09325d;
        font-size: 14px;
        font-family: sans-serif;
    }

    .login-card .btn-sso {
        background-color: #198754;
        border-color: #198754;
        color: white;
        font-size: 14px;
        font-family: sans-serif;
        transition: all 0.3s ease;
        border-radius: 0.375rem;
    }

    .login-card .btn-sso:hover {
        background-color: #157347;
        border-color: #146c43;
        color: white;
        transform: translateY(-1px);
        box-shadow: 0 4px 8px rgba(25, 135, 84, 0.3);
    }

    .login-card .btn-sso:focus {
        box-shadow: 0 0 0 0.25rem rgba(25, 135, 84, 0.25);
    }

    .sso-divider {
        position: relative;
        text-align: center;
        margin: 1.5rem 0;
    }

    .sso-divider::before {
        content: '';
        position: absolute;
        top: 50%;
        left: 0;
        right: 0;
        height: 1px;
        background: #dee2e6;
    }

    .sso-divider span {
        background: rgba(255, 255, 255, 0.98);
        padding: 0 1rem;
        color: #6c757d;
        font-size: 13px;
    }

    .login-card input {
        font-size: 14px;
        font-family: sans-serif;
    }

    .forgot-password {
        font-size: 14px;
        font-family: sans-serif;
        color: #6c757d;
    }

    /* Icon circle */
    .icon-circle {
        background-color: #09325d;
        width: 48px;
        height: 48px;
        border-radius: 50%;
        display: flex;
        justify-content: center;
        align-items: center;
        margin: 0 auto 1.5rem auto;
        color: white;
        font-size: 24px;
    }
</style>
</head>
@section('navbar')
@php
    $userBaseUrl = env('USER_BASE_URL');
@endphp

    <nav class="navbar navbar-expand-lg navbar-white bg-white">
      <div class="container">
        <div class="col">
          <ul class="brand-logo navbar-left gap-2 justify-content-sm-start justify-content-center">
            <li class="nav-item">
              <a class="navbar-brand d-flex gap-3 m-0 p-0" href="{{ config('app.user_url') }}">
                <img
                  class="navbar-img"
                  src="{{ asset('assets/img/dti-bagong-text.png') }}"
                  alt="DTI Bagong Logo Text"
                />
              </a>
            </li>
          </ul>
        </div>

        <div class="col">
          <ul class="navbar-right gap-2 justify-content-sm-end justify-content-center">
            <li class="nav-item">
                <a target="_blank" href="https://www.dti.gov.ph/good-governance-program/transparency-seal">
                    <img class="navbar-img" src="{{ asset('assets/img/ph-seal.png') }}"/>
                </a>
            </li>
            <li class="nav-item">
                <a target="_blank" href="https://www.foi.gov.ph/requests/dti">
                    <img class="navbar-img" src="{{ asset('assets/img/freedom-ph.png') }}"/>
                </a>
            </li>
            <li class="nav-item">
                <a target="_blank" href="https://dtiwebfiles.s3.ap-southeast-1.amazonaws.com/Data+Privacy/DTI_NPC_RegistrationCert.pdf">
                    <img class="navbar-img" src="{{ asset('assets/img/dpo.png') }}"/>
                </a>
            </li>
          </ul>
        </div>
      </div>
    </nav>

@section('content')
    <div class="login-wrapper">
        <div class="login-card rounded-4">
            <!-- Your Brand Icon -->
            <div class="brand-logo-wrapper d-flex gap-2 mb-3 p-0 justify-content-center">
                <a href="#">
                    <img
                        class="brand-logo"
                        src="{{ asset('assets/img/e-commerce-phl.png') }}"
                        alt="E-Commerce Phl"
                    />
                </a>
            </div>
            <!-- <div class="icon-circle">
            </div> -->

            <form action="{{ route('auth') }}" method="post" class="text-center">
                @csrf
                <div class="mb-3">
                    <input type="hidden" id ="latitude" name="latitude" value="">
                    <input type="hidden" id ="longitude" name="longitude"  value="">
                    <input type="text" class="form-control" name="email" placeholder="Email" />
                </div>
                <div class="mb-3">
                    <input type="password" class="form-control" name="password" placeholder="Password" />
                </div>
                <div class="mb-3">
                    <button type="submit" class="btn btn-primary w-100">Login</button>
                </div>
            </form>

            {{-- SSO Login Option - DISABLED FOR NOW
            <div class="sso-divider">
                <span>Or continue with</span>
            </div>
            
            <div class="text-center mb-3">
                <a href="{{ $project1_url }}/sso?redirect_url={{ urlencode(url('/sso/authenticate')) }}" 
                   class="btn btn-sso w-100 d-flex align-items-center justify-content-center">
                    <i class="fas fa-shield-alt me-2"></i>
                    <span>Login with Main Account</span>
                </a>
                <p class="text-muted small mt-2 mb-0">Secure Single Sign-On authentication</p>
            </div>
            --}}
            @if(optional($activate_registration)->value == 1)
            <!-- @if ($userBaseUrl !== 'https://trustmark.bahayko.app') -->
                <a href="{{ route('register.create') }}" class="forgot-password d-block text-center">Register</a>
            <!-- @endif -->
            @endif

            <a href="{{ route('login.forgot_password') }}" class="forgot-password d-block text-center">Forgot your password?</a>

            <div class="my-3">
                {{-- <a href="{{ env('GOOGLE_REDIRECT') }}" class="btn btn-light w-100 mb-2" style="border: 1px solid #ddd;">
                    <img src="{{ asset('assets/img/google-icon.svg') }}" alt="Google"
                        style="height:20px;vertical-align:middle;margin-right:8px;">
                    Sign in with Google
                </a> --}}
                {{-- <a href="{{ env('FACEBOOK_REDIRECT') }}" class="btn btn-primary w-100"
                    style="background:#3b5998;border-color:#3b5998;">
                    <img src="{{ asset('assets/img/facebook-icon.svg') }}" alt="Facebook"
                        style="height:20px;vertical-align:middle;margin-right:8px;">
                    Sign in with Facebook
                </a> --}}
            </div>
        </div>
    </div>

    <script src="assets/bootstrap/js/bootstrap.min.js"></script>


    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

    @if (session('success'))
        <script>
            Swal.fire({
                icon: 'success',
                title: 'Success!',
                text: '{{ session('success') }}',
                confirmButtonColor: '#3085d6'
            });
        </script>
    @endif

    @if (session('error'))
        <script>
            @if (session('countdown_seconds'))
                // Handle countdown for email verification
                let countdownSeconds = {{ session('countdown_seconds') }};
                let countdownInterval;
                
                function updateCountdownMessage() {
                    const minutes = Math.floor(countdownSeconds / 60);
                    const seconds = countdownSeconds % 60;
                    
                    let message = `{!! session('error') !!}`;
                    if (countdownSeconds > 0) {
                        message = message.replace(/\d+ minutes?/, `${minutes} minutes`);
                        if (minutes === 0) {
                            message = message.replace(/\d+ minutes?/, `${seconds} seconds`);
                        }
                    }
                    
                    Swal.update({
                        html: message
                    });
                }
                
                Swal.fire({
                    icon: 'warning',
                    title: 'Please Wait',
                    html: `{!! session('error') !!}`,
                    confirmButtonColor: '#d33',
                    allowOutsideClick: false,
                    showConfirmButton: false,
                    timer: countdownSeconds * 1000,
                    timerProgressBar: true,
                    didOpen: () => {
                        // Disable the login form
                        document.querySelector('form button[type="submit"]').disabled = true;
                        
                        countdownInterval = setInterval(() => {
                            countdownSeconds--;
                            updateCountdownMessage();
                            
                            if (countdownSeconds <= 0) {
                                clearInterval(countdownInterval);
                                Swal.fire({
                                    icon: 'info',
                                    title: 'Ready',
                                    text: 'You can now try logging in again to receive a new verification email.',
                                    confirmButtonColor: '#3085d6'
                                }).then(() => {
                                    // Re-enable the login form
                                    document.querySelector('form button[type="submit"]').disabled = false;
                                });
                            }
                        }, 1000);
                    },
                    willClose: () => {
                        clearInterval(countdownInterval);
                        document.querySelector('form button[type="submit"]').disabled = false;
                    }
                });
            @else
                Swal.fire({
                    icon: 'error',
                    title: 'Oops...',
                    html: `{!! session('error') !!}`,
                    confirmButtonColor: '#d33'
                });
            @endif
        </script>
    @endif


    @if ($errors->has('username'))
        <script>
            Swal.fire({
                icon: 'error',
                title: 'Login Failed',
                text: '{{ $errors->first('username') }}',
            });

        </script>
    @endif

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
                document.getElementById('latitude').value  = lat;
                document.getElementById('longitude').value  = long;
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
@endsection
