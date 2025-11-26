@extends('layouts.app')

@section('content')
    @include('components.alerts')

    <div class="d-flex justify-content-between align-items-center mb-4" style="margin-top: 15px;">
        <h3 class="page-title">ADMIN</h3>
    </div>

    <ol class="breadcrumb custom-breadcrumb">
        <li class="breadcrumb-item"><a href="{{ route('dashboard') }}"><span>Dashboard</span></a></li>
        <li class="breadcrumb-item"><a href="#"><span>Create</span></a></li>
    </ol>

    <div id="wrapper">
        <div class="d-flex flex-column" id="content-wrapper">
            <form action="{{ route('user.store') }}" method="POST" enctype="multipart/form-data" autocomplete="off">
                @csrf
                <div class="row" style="margin-bottom: 15px;">
                    <div class="col">
                        <div class="card">
                            <div class="card-header">
                                <h6 class="text-primary fw-bold m-0" style="font-size: 12px;">Account Login</h6>
                            </div>
                            <div class="card-body">
                                <div class="row" style="margin-bottom:10px;">
                                    <div class="col">
                                        <div class="mb-3">
                                            <label class="form-label custom-label">First Name<span
                                                    class="required-field">*</span></label>
                                            <input class="form-control custom-input" type="text" name="first_name"
                                                placeholder="First Name" required>
                                        </div>
                                    </div>
                                    <div class="col">
                                        <div class="mb-3">
                                            <label class="form-label custom-label">Middle Name</label>
                                            <input class="form-control custom-input" type="text" name="middle_name"
                                                placeholder="Middle Name">
                                        </div>
                                    </div>
                                    <div class="col">
                                        <div class="mb-3">
                                            <label class="form-label custom-label">Last Name<span
                                                    class="required-field">*</span></label>
                                            <input class="form-control custom-input" type="text" name="last_name"
                                                placeholder="Last Name" required>
                                        </div>
                                    </div>
                                </div>

                                <div class="row" style="margin-bottom:10px;">
                                    <div class="col">
                                        <div class="mb-3">
                                            <label class="form-label custom-label">Suffix</label>
                                            <input class="form-control custom-input" type="text" name="suffix"
                                                placeholder="Suffix">
                                        </div>
                                    </div>
                                    <div class="col">
                                        <div class="mb-3">
                                            <label class="form-label custom-label">Mobile No.</label>
                                            <div class="input-group">
                                                <span class="input-group-text custom-input">+63</span>
                                                <input class="form-control custom-input" type="tel" id="number"
                                                    placeholder="9123456789" maxlength="10" inputmode="numeric" pattern="[0-9]{10}"
                                                        oninput="this.value = this.value.replace(/[^0-9]/g, '')" required>
                                            </div>
                                            <input type="hidden" name="ctc_num" id="full_number">
                                        </div>
                                    </div>
                                    <div class="col">
                                        <div class="mb-3">
                                            <label class="form-label custom-label">Email<span
                                                    class="required-field">*</span></label>
                                            <input class="form-control custom-input" type="email" id="email"
                                                name="email" placeholder="Email" required>
                                            <div id="email-error" class="text-danger mt-1" style="font-size: 13px;">
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <div class="row" style="margin-bottom:10px;">
                                    <div class="col">
                                        <div class="mb-3">
                                            <label class="form-label custom-label">Password</label>
                                            <input class="form-control custom-input" type="password" id="password"
                                                name="password" placeholder="Password" required>

                                            <small id="length-error" class="text-danger" style="display:none;">Password
                                                must
                                                be at least 8 characters
                                                long.</small>
                                            <small id="format-error" class="text-danger" style="display:none;">Password
                                                must
                                                contain letters, special
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
                                                style="display:none;">Passwords
                                                do not match.</small>
                                            <small id="password-success" class="text-success"
                                                style="display:none;">Passwords match.</small>
                                        </div>
                                    </div>
                                    <div class="col"></div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <button class="btn btn-primary" type="submit"
                    style="font-family: sans-serif;font-size: 13px;margin-bottom: 15px;">Save</button>
            </form>
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
            const hasSpecial = /[^a-zA-Z0-9]/.test(str);
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

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const form = document.querySelector('form');
            const input = document.getElementById('number');
            const fullInput = document.getElementById('full_number');

            input.addEventListener('input', function() {
                const userInput = input.value.trim();
                fullInput.value = userInput ? '+63' + userInput : '';
            });

            form.addEventListener('submit', function(e) {
                const userInput = input.value.trim();
                if (!/^\d{10}$/.test(userInput)) {
                    e.preventDefault();
                    alert("Please enter exactly 10 digits for the mobile number.");
                }
            });
        });
    </script>

    {{-- error handling for email --}}
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const emailField = document.getElementById('email');
            const errorDiv = document.getElementById('email-error');

            if (emailField) {
                emailField.addEventListener('input', function() {
                    const emailPattern = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;

                    if (!emailPattern.test(emailField.value)) {
                        errorDiv.textContent = 'Please enter a valid email address.';
                    } else {
                        errorDiv.textContent = '';
                    }
                });
            }
        });
    </script>
@endsection
