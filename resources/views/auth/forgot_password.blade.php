@if ($errors->any())
    <div class="alert alert-danger">
        <ul>
            @foreach ($errors->all() as $error)
                <li>{{ $error }}</li>
            @endforeach
        </ul>
    </div>
@endif

@if (session('error'))
    <div class="alert alert-danger" id="error-message">
        {!! session('error') !!}
        @if (session('countdown_seconds'))
            @php
                $totalSeconds = session('countdown_seconds');
                $minutes = floor($totalSeconds / 60);
                $seconds = $totalSeconds % 60;
                $displayTime = $minutes > 0 ? "{$minutes} minute" . ($minutes > 1 ? 's' : '') . " {$seconds} second" . ($seconds !== 1 ? 's' : '') : "{$seconds} second" . ($seconds !== 1 ? 's' : '');
            @endphp
            <div id="countdown-timer" data-seconds="{{ session('countdown_seconds') }}" style="margin-top: 10px;">
                <small><em>Time remaining: <span id="countdown-display">{{ $displayTime }}</span></em></small>
            </div>
            <script>
                console.log('Countdown seconds from session:', {{ session('countdown_seconds') }});
            </script>
        @endif
    </div>
@endif

@if (session('success'))
    <div class="alert alert-success">
        {{ session('success') }}
    </div>
@endif


@extends('layouts.auth')

@section('title', 'Forgot Password')
<div id="wrapper">
    <div class="d-flex flex-column" id="content-wrapper">
        <div id="content" style="background: rgba(228,227,232,0.36);">
@section('navbar')
    <nav class="navbar navbar-expand-lg navbar-white bg-white">
      <div class="container">
        <div class="col">
          <ul class="brand-logo navbar-left gap-2 justify-content-sm-start justify-content-center">
            <li class="nav-item">
              <a class="navbar-brand d-flex gap-3 m-0 p-0" href="https://trustmark.bahayko.app/">
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
            <div class="container-fluid">
                <div class="d-flex justify-content-between align-items-center mb-4" style="margin-top: 15px;">
                    <h3 class="text-dark" style="font-family:sans-serif;font-size:20px;margin:0;"><span
                            style="color: rgba(var(--bs-dark-rgb), var(--bs-text-opacity));font-size: 20px;font-weight: bold;">Reset Password</span>
                    </h3>
                </div>
                <form action="{{ route('login.otp') }}" method="POST" enctype="multipart/form-data"
                    autocomplete="off">
                    @csrf
                    <div class="row" style="margin-bottom: 15px;">
                        <div class="col">
                            <div class="card">
                                <div class="card-header">
                                    <h6 class="text-primary fw-bold m-0" style="font-size: 12px;">Account Password Reset</h6>
                                </div>
                                <div class="card-body">
                                    <div class="row" style="margin-bottom:10px;">
                                        <div class="col">
                                            <div class="mb-3">
                                                <label class="form-label custom-label">Email</label>
                                                <input class="form-control custom-input" type="email" id="email"
                                                    name="email" placeholder="Email" required>
                                                <div id="email-error" class="text-danger mt-1" style="font-size: 13px;">
                                                </div>
                                            </div>
                                        </div>
                                        {{-- <div class="col">
                                            <div class="mb-3">
                                                <label class="form-label custom-label">Password</label>
                                                <input class="form-control custom-input" type="password" id="password"
                                                    name="password" placeholder="Password" required>

                                                <small id="length-error" class="text-danger"
                                                    style="display:none;">Password must be at least 8 characters
                                                    long.</small>
                                                <small id="format-error" class="text-danger"
                                                    style="display:none;">Password must contain letters, special
                                                    character and
                                                    numbers.</small>

                                            </div>
                                        </div>
                                        <div class="col">
                                            <div class="mb-3">
                                                <label class="form-label custom-label">Confirm Password</label>
                                                <input class="form-control custom-input" type="password"
                                                    id="confirm-password" name="password_confirmation"
                                                    placeholder="Confirm Password" required="">

                                                <small id="password-error" class="text-danger"
                                                    style="display:none;">Passwords do not match.</small>
                                                <small id="password-success" class="text-success"
                                                    style="display:none;">Passwords match.</small>
                                            </div>
                                        </div> --}}
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="d-flex gap-2" style="margin-bottom: 160px;">
                        <button class="btn btn-primary" type="submit"
                            style="font-family: sans-serif; font-size: 13px;">
                            Send OTP
                        </button>

                        <a href="{{ route('login') }}" class="btn btn-secondary"
                            style="font-family: sans-serif; font-size: 13px;">
                            Go to Login
                        </a>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>


<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>


<script>
    const password = document.getElementById('password');
    const confirmPassword = document.getElementById('confirm-password');
    const errorText = document.getElementById('password-error');
    const successText = document.getElementById('password-success');
    const formatError = document.getElementById('format-error');
    const lengthError = document.getElementById('length-error');

    // Check if password contains letters, numbers, and special characters
    function containsLetterNumberAndSpecialChar(str) {
        const hasLetter = /[a-zA-Z]/.test(str);
        const hasNumber = /[0-9]/.test(str);
        const hasSpecial = /[!@#$%^&*(),.?":{}|<>]/.test(str); // You can customize this list
        return hasLetter && hasNumber && hasSpecial;
    }

    // Validate password
    function validatePasswordMatch() {
        const passValue = password.value;
        const confirmValue = confirmPassword.value;

        // Reset styles
        password.classList.remove('is-invalid', 'is-valid');
        confirmPassword.classList.remove('is-invalid', 'is-valid');

        // Length check
        if (passValue.length < 8) {
            lengthError.style.display = 'block';
            formatError.style.display = 'none';
            errorText.style.display = 'none';
            successText.style.display = 'none';
            password.classList.add('is-invalid');
            return;
        } else {
            lengthError.style.display = 'none';
        }

        // Format check
        if (!containsLetterNumberAndSpecialChar(passValue)) {
            formatError.style.display = 'block';
            errorText.style.display = 'none';
            successText.style.display = 'none';
            password.classList.add('is-invalid');
            return;
        } else {
            formatError.style.display = 'none';
        }

        // If confirm password is empty, don't show match errors yet
        if (confirmValue === '') {
            errorText.style.display = 'none';
            successText.style.display = 'none';
            return;
        }

        // Match check
        if (passValue === confirmValue) {
            errorText.style.display = 'none';
            successText.style.display = 'block';
            password.classList.add('is-valid');
            confirmPassword.classList.add('is-valid');
        } else {
            errorText.style.display = 'block';
            successText.style.display = 'none';
            confirmPassword.classList.add('is-invalid');
        }
    }

    password.addEventListener('input', validatePasswordMatch);
    confirmPassword.addEventListener('input', validatePasswordMatch);

    // Handle countdown for OTP sending
    const countdownTimer = document.getElementById('countdown-timer');
    if (countdownTimer) {
        const initialSeconds = parseInt(countdownTimer.dataset.seconds);
        const countdownDisplay = document.getElementById('countdown-display');
        const form = document.querySelector('form[action="{{ route('login.otp') }}"]');
        const submitButton = form ? form.querySelector('button[type="submit"]') : null;
        
        console.log('Countdown timer found. Initial seconds:', initialSeconds);
        console.log('Countdown display element:', countdownDisplay);
        console.log('Submit button:', submitButton);
        
        let countdownSeconds = initialSeconds;
        
        // Disable form initially if countdown is active
        if (submitButton && countdownSeconds > 0) {
            submitButton.disabled = true;
            submitButton.textContent = 'Please wait...';
        }
        
        function updateCountdown() {
            console.log('Updating countdown. Seconds remaining:', countdownSeconds);
            
            if (countdownSeconds <= 0) {
                // Countdown finished
                console.log('Countdown finished');
                countdownTimer.style.display = 'none';
                document.getElementById('error-message').className = 'alert alert-info';
                document.getElementById('error-message').innerHTML = 'You can now request a new OTP.';
                
                if (submitButton) {
                    submitButton.disabled = false;
                    submitButton.textContent = 'Send OTP';
                }
                return;
            }
            
            const minutes = Math.floor(countdownSeconds / 60);
            const seconds = countdownSeconds % 60;
            
            let displayText = '';
            if (minutes > 0) {
                displayText = `${minutes} minute${minutes > 1 ? 's' : ''} ${seconds} second${seconds !== 1 ? 's' : ''}`;
            } else {
                displayText = `${seconds} second${seconds !== 1 ? 's' : ''}`;
            }
            
            if (countdownDisplay) {
                countdownDisplay.textContent = displayText;
            }
            
            countdownSeconds--;
            setTimeout(updateCountdown, 1000);
        }
        
        // Start the countdown
        updateCountdown();
    } else {
        console.log('No countdown timer found');
    }
</script>


<style>
    .custom-navbar-logo {
        width: 120px;
        height: 64px;
        /* margin-left: -60px; */
        border-radius: 8px;
    }
</style>
