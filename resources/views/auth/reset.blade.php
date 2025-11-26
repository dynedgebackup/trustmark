@if ($errors->any())
    <div class="alert alert-danger">
        <ul>
            @foreach ($errors->all() as $error)
                <li>{{ $error }}</li>
            @endforeach
        </ul>
    </div>
@endif


@extends('layouts.auth')

@section('title', 'Forgot Password')
<div id="wrapper">
    <div class="d-flex flex-column" id="content-wrapper">
        <div id="content" style="background: rgba(228,227,232,0.36);">
            <nav class="navbar navbar-expand shadow mb-4 topbar" style="background: #09325d;height: 64px;">
                <div class="container-fluid"><button class="btn btn-link d-md-none rounded-circle me-3"
                        id="sidebarToggleTop" type="button"><i class="fas fa-bars"></i></button><img
                        class="custom-navbar-logo" src="{{ asset('assets/img/DTI-BP-white.png') }}">
                </div>
            </nav>
            <div class="container-fluid">
                <div class="d-flex justify-content-between align-items-center mb-4" style="margin-top: 15px;">
                    <h3 class="text-dark" style="font-family:sans-serif;font-size:20px;margin:0;"><span
                            style="color: rgba(var(--bs-dark-rgb), var(--bs-text-opacity));font-size: 20px;font-weight: bold;">Reset Password</span>
                    </h3>
                </div>
                <form action="{{ route('password.reset') }}" method="POST" enctype="multipart/form-data"
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
                                                <input type="hidden" name="email" value="{{ $user->email }}">
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
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="d-flex gap-2" style="margin-bottom: 160px;">
                        <button class="btn btn-primary" type="submit"
                            style="font-family: sans-serif; font-size: 13px;">
                            Reset Password
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
</script>


<style>
    .custom-navbar-logo {
        width: 120px;
        height: 64px;
        /* margin-left: -60px; */
        border-radius: 8px;
    }
</style>
