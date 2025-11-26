@extends('layouts.app')

@section('content')
    <div class="d-flex justify-content-between align-items-center mb-4" style="margin-top: 15px;">
        <h3 class="page-title">PROFILE</h3>
    </div>

    <ol class="breadcrumb custom-breadcrumb">
        <li class="breadcrumb-item"><a href="{{ route('dashboard') }}"><span>Dashboard</span></a></li>
        <li class="breadcrumb-item"><a href="/#"><span>Profile</span></a></li>
    </ol>

    <div id="wrapper">
        <div class="d-flex flex-column" id="content-wrapper">
            @if (Auth::user()->role == 1)
                <form action="{{ route('profile.applicant_update', $user->id) }}" method="POST"
                    enctype="multipart/form-data">
                    @csrf
                    @method('PUT')
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
                                                    placeholder="First Name" required value="{{ $user->first_name }}">
                                            </div>
                                        </div>
                                        <div class="col">
                                            <div class="mb-3">
                                                <label class="form-label custom-label">Middle Name</label>
                                                <input class="form-control custom-input" type="text" name="middle_name"
                                                    placeholder="Middle Name" value="{{ $user->middle_name }}">
                                            </div>
                                        </div>
                                        <div class="col">
                                            <div class="mb-3">
                                                <label class="form-label custom-label">Last Name<span
                                                        class="required-field">*</span></label>
                                                <input class="form-control custom-input" type="text" name="last_name"
                                                    placeholder="Last Name" required value="{{ $user->last_name }}">
                                            </div>
                                        </div>
                                    </div>

                                    <div class="row" style="margin-bottom:10px;">
                                        <div class="col">
                                            <div class="mb-3">
                                                <label class="form-label custom-label">Suffix</label>
                                                <input class="form-control custom-input" type="text" name="suffix"
                                                    placeholder="Suffix" value="{{ $user->suffix }}">
                                            </div>
                                        </div>
                                        <div class="col">
                                            <div class="mb-3">
                                                <label class="form-label custom-label">Mobile No. <span
                                                        class="required-field">*</span></label>
                                                <div class="input-group">
                                                    <span class="input-group-text custom-input">+63</span>
                                                    <input class="form-control custom-input" type="tel" id="number"
                                                        placeholder="Mobile No." required maxlength="10" inputmode="numeric"
                                                        pattern="[0-9]{10}"
                                                        oninput="this.value = this.value.replace(/[^0-9]/g, '')"
                                                        value="{{ preg_replace('/^\+63/', '', $user->ctc_no ?? '') }}">
                                                </div>

                                                <input type="hidden" name="ctc_no" id="full_number"
                                                    value="{{ $user->ctc_no ?? '' }}">
                                            </div>
                                        </div>
                                        <div class="col">
                                            <div class="mb-3">
                                                <label class="form-label custom-label">Email<span
                                                        class="required-field">*</span></label>
                                                <input class="form-control custom-input" type="email" id="email"
                                                    name="email" placeholder="Email" required value="{{ $user->email }}">
                                                <div id="email-error" class="text-danger mt-1" style="font-size: 13px;">
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <br>

                            <div class="col">
                                <div class="card">
                                    <div class="card-header">
                                        <h6 class="text-primary fw-bold m-0" style="font-size: 12px;">Authorized
                                            Repserentative</h6>
                                    </div>
                                    <div class="card-body">
                                        <div class="row" style="margin-bottom:10px;">
                                            <div class="col">
                                                <div class="mb-3">
                                                    <label class="form-label custom-label">Government Issued ID<span
                                                            class="required-field">*</span></label>
                                                    <select class="form-select custom-input select2" id="issued_id"
                                                        name="issued_id" required>
                                                        <option value="">Select Government Issued ID</option>
                                                        @foreach ($requirements as $req)
                                                            <option value="{{ $req->id }}"
                                                                data-with-expiration="{{ trim($req->with_expiration) }}"
                                                                {{ old('requirement_id', $user->requirement_id ?? '') == $req->id ? 'selected' : '' }}>
                                                                {{ $req->description }}
                                                            </option>
                                                        @endforeach
                                                    </select>
                                                </div>
                                            </div>
                                            <div class="col">
                                                <div class="mb-3">
                                                    <label class="form-label custom-label">Attachment<span
                                                            class="required-field">*</span></label>

                                                    <div class="mb-3">
                                                        <input class="form-control custom-input" type="file"
                                                            id="req_upload" name="req_upload"
                                                            accept=".jpg,.jpeg,.png,.pdf"
                                                            title="Please upload .jpg, .jpeg, .png, or .pdf. Max size 10 MB">
                                                    </div>

                                                    @if (!empty($user->requirement_upload))
                                                        @php
                                                            $filename = basename($user->requirement_upload);
                                                        @endphp

                                                        <a href="{{ route('profile.download_authorized', $user->id) }}"
                                                            class="d-flex align-items-center gap-2">
                                                            <i class="fa fa-download"></i>
                                                            <span class="custom-label"
                                                                title="{{ $filename }}">{{ $filename }}</span>
                                                        </a>

                                                        @if (in_array(pathinfo($user->requirement_upload, PATHINFO_EXTENSION), ['jpg', 'jpeg', 'png']))
                                                            <div class="mt-2">
                                                                <img src="{{ asset('storage/' . $user->requirement_upload) }}"
                                                                    alt="Uploaded Image"
                                                                    style="max-width: 200px; border: 1px solid #ccc;">
                                                            </div>
                                                        @endif
                                                    @endif
                                                </div>
                                            </div>
                                            <div class="col">
                                                @php
                                                    $today = \Carbon\Carbon::today()->format('Y-m-d');
                                                @endphp
                                                <div class="mb-3">
                                                    <label class="form-label custom-label">Expiration Date<span
                                                            class="required-field">*</span></label>
                                                    <input class="form-control custom-input" type="date"
                                                        id="expirationDateInputVisible" name="expiration_date_visible"
                                                        placeholder="Date" min="{{ $today }}"
                                                        value="{{ old('requirement_expired', $user->requirement_expired ?? '') }}"
                                                        readonly>

                                                    <input type="hidden" name="expired_date" id="expirationDateInput"
                                                        value="{{ old('requirement_expired', $user->requirement_expired ?? '') }}">
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <br>

                            <div class="card">
                                <div class="card-header">
                                    <h6 class="text-primary fw-bold m-0" style="font-size: 12px;">Change Password</h6>
                                </div>
                                <div class="card-body">
                                    <div class="row" style="margin-bottom:10px;">
                                        <div class="col">
                                            <div class="mb-3">
                                                <label class="form-label custom-label">Password</label>
                                                <input class="form-control custom-input" type="password" id="password"
                                                    name="password" placeholder="Password">

                                                <small id="length-error" class="text-danger"
                                                    style="display:none;">Password
                                                    must
                                                    be at least 8 characters
                                                    long.</small>
                                                <small id="format-error" class="text-danger"
                                                    style="display:none;">Password
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
                                                    placeholder="Confirm Password">

                                                <small id="password-error" class="text-danger"
                                                    style="display:none;">Passwords
                                                    do not match.</small>
                                                <small id="password-success" class="text-success"
                                                    style="display:none;">Passwords match.</small>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <button class="btn btn-primary" type="submit"
                        style="font-family: sans-serif;font-size: 13px;margin-bottom: 15px;">Update</button>
                </form>
            @else
                <form action="{{ route('profile.admin_update', $user->id) }}"  method="POST"
                    enctype="multipart/form-data">
                    @csrf
                    @method('PUT')
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
                                                    placeholder="First Name" required value="{{ $user->first_name }}">
                                            </div>
                                        </div>
                                        <div class="col">
                                            <div class="mb-3">
                                                <label class="form-label custom-label">Middle Name</label>
                                                <input class="form-control custom-input" type="text" name="middle_name"
                                                    placeholder="Middle Name" value="{{ $user->middle_name }}">
                                            </div>
                                        </div>
                                        <div class="col">
                                            <div class="mb-3">
                                                <label class="form-label custom-label">Last Name<span
                                                        class="required-field">*</span></label>
                                                <input class="form-control custom-input" type="text" name="last_name"
                                                    placeholder="Last Name" required value="{{ $user->last_name }}">
                                            </div>
                                        </div>
                                    </div>

                                    <div class="row" style="margin-bottom:10px;">
                                        <div class="col">
                                            <div class="mb-3">
                                                <label class="form-label custom-label">Suffix</label>
                                                <input class="form-control custom-input" type="text" name="suffix"
                                                    placeholder="Suffix" value="{{ $user->suffix }}">
                                            </div>
                                        </div>
                                        <div class="col">
                                            <div class="mb-3">
                                                <label class="form-label custom-label">Mobile No. <span
                                                        class="required-field">*</span></label>
                                                <div class="input-group">
                                                    <span class="input-group-text custom-input">+63</span>
                                                    <input class="form-control custom-input" type="tel" id="number"
                                                        placeholder="Mobile No." required maxlength="10" inputmode="numeric"
                                                        pattern="[0-9]{10}"
                                                        oninput="this.value = this.value.replace(/[^0-9]/g, '')"
                                                        value="{{ preg_replace('/^\+63/', '', $user->ctc_no ?? '') }}">
                                                </div>

                                                <input type="hidden" name="ctc_no" id="full_number"
                                                    value="{{ $user->ctc_no ?? '' }}">
                                            </div>
                                        </div>
                                        <div class="col">
                                            <div class="mb-3">
                                                <label class="form-label custom-label">Email<span
                                                        class="required-field">*</span></label>
                                                <input class="form-control custom-input" type="email" id="email"
                                                    name="email" placeholder="Email" required value="{{ $user->email }}">
                                                <div id="email-error" class="text-danger mt-1" style="font-size: 13px;">
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="card">
                            <div class="card-header">
                                <h6 class="text-primary fw-bold m-0" style="font-size: 12px;">E-Signature</h6>
                            </div>
                            <div class="card-body">
                                <div class="row" style="margin-bottom:10px;">
                                    <div class="col">
                                        <div class="row">
                                            <div class="col-sm-4">
                                            @if ($user->profile_photos)
                                            <img id="signature-preview"
                                                src="{{ asset('storage/' . $user->profile_photos) }}"
                                                alt="Signature Preview"
                                                style="max-width:350px; height:150px; border: 1px solid #000; display:block; margin-bottom:10px;">
                                            @else
                                            <img id="signature-preview"
                                                src="data:image/png;base64,iVBORw0KGgoAAAANSUhEUgAAAWgAAAFoCAIAAADY5qtbAAAAA3NCSVQICAjb4U/gAAAAGXRFWHRTb2Z0d2FyZQBBZG9iZSBJ
                                                bWFnZVJlYWR5ccllPAAAACBJREFUeNrswYEAAAAAw6D5U1/gBlUBAAAAAAAAAAAAAAAAAADwGQABAwABHgABuxIAAAAASUVORK5CYII="
                                                alt="Signature Preview"
                                                style="max-width:350px; height:150px; border: 1px solid #000; display:block; margin-bottom:10px;" />
                                            @endif
                                            
                                                <label class="btn btn-primary btn-sm">
                                                   Choose Signature
                                                   <input type="file" name="profile_photos" accept=".png" hidden onchange="previewAndPrepareTransparent(this)">
                                                   <input type="hidden" name="profile_photos_base64" id="profile_photos_base64">
                                                </label>
                                            </div>
                                            <div class="col-sm-4">
                                                <div class="mt-2 text-muted" style="font-size: 13px;color: #d64646 !important;">
                                                    Note: E-signature dimensions must be <strong>600px x 140px</strong>.
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <script>
                            function colorDistance(r1, g1, b1, r2, g2, b2) {
                                return Math.sqrt((r1 - r2) ** 2 + (g1 - g2) ** 2 + (b1 - b2) ** 2);
                            }

                            function previewAndPrepareTransparent(input) {
                                const file = input.files[0];
                                if (!file) return;

                                const reader = new FileReader();
                                reader.onload = function(e) {
                                    const img = new Image();
                                    img.onload = function () {
                                        const canvas = document.createElement('canvas');
                                        canvas.width = img.width;
                                        canvas.height = img.height;
                                        const ctx = canvas.getContext('2d');
                                        ctx.drawImage(img, 0, 0);

                                        const imageData = ctx.getImageData(0, 0, canvas.width, canvas.height);
                                        const data = imageData.data;
                                        const bgR = data[0], bgG = data[1], bgB = data[2];
                                        const tolerance = 40;

                                        for (let i = 0; i < data.length; i += 4) {
                                            const r = data[i];
                                            const g = data[i + 1];
                                            const b = data[i + 2];

                                            if (colorDistance(r, g, b, bgR, bgG, bgB) < tolerance) {
                                                data[i + 3] = 0;
                                            }
                                        }

                                        ctx.putImageData(imageData, 0, 0);

                                        const transparentDataUrl = canvas.toDataURL("image/png");
                                        document.getElementById("signature-preview").src = transparentDataUrl;
                                        document.getElementById("profile_photos_base64").value = transparentDataUrl;
                                    };
                                    img.src = e.target.result;
                                };
                                reader.readAsDataURL(file);
                            }
                            </script>

                            <br>
                            <div class="card">
                                <div class="card-header">
                                    <h6 class="text-primary fw-bold m-0" style="font-size: 12px;">Change Password</h6>
                                </div>
                                <div class="card-body">
                                    <div class="row" style="margin-bottom:10px;">
                                        <div class="col">
                                            <div class="mb-3">
                                                <label class="form-label custom-label">Password</label>
                                                <input class="form-control custom-input" type="password" id="password"
                                                    name="password" placeholder="Password">

                                                <small id="length-error" class="text-danger"
                                                    style="display:none;">Password
                                                    must
                                                    be at least 8 characters
                                                    long.</small>
                                                <small id="format-error" class="text-danger"
                                                    style="display:none;">Password
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
                                                    placeholder="Confirm Password">

                                                <small id="password-error" class="text-danger"
                                                    style="display:none;">Passwords
                                                    do not match.</small>
                                                <small id="password-success" class="text-success"
                                                    style="display:none;">Passwords match.</small>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <button class="btn btn-primary" type="submit"
                        style="font-family: sans-serif;font-size: 13px;margin-bottom: 15px;">Update</button>
                </form>
            @endif
        </div>
    </div>




    {{-- save prefix for mobile no --}}
    <script>
        document.addEventListener('DOMContentLoaded', () => {
            const numberInput = document.getElementById('number');
            const fullNumberInput = document.getElementById('full_number');
            const form = document.getElementById('resetForm');

            // Initialize hidden input on page load
            fullNumberInput.value = '+63' + numberInput.value.replace(/\D/g, '');

            // Update hidden input live when user types
            numberInput.addEventListener('input', () => {
                let localNumber = numberInput.value.replace(/\D/g, '');
                fullNumberInput.value = '+63' + localNumber;
                // For debugging, you can console.log here to verify
                // console.log('Updated hidden:', fullNumberInput.value);
            });

            // Final update before form submission (extra safety)
            form.addEventListener('submit', () => {
                let localNumber = numberInput.value.replace(/\D/g, '');
                fullNumberInput.value = '+63' + localNumber;
            });
        });
    </script>

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

    {{-- <script>
        $(document).ready(function() {
            $('#issued_id').select2({
                placeholder: "Select Government Issued ID",
                allowClear: true,
                width: '100%'
            });
        });
    </script> --}}

    {{-- government issued id expiration date --}}
    <script>
        $(document).ready(function() {
            const $issuedId = $('#issued_id');
            const $expirationVisible = $('#expirationDateInputVisible');
            const $expirationHidden = $('#expirationDateInput');

            function updateExpirationField() {
                const selected = $issuedId.find(':selected');
                const withExpiration = selected.attr('data-with-expiration');

                console.log('Selected with_expiration:', withExpiration); // Debug

                if (withExpiration === '1') {
                    $expirationVisible.prop('readonly', false);
                    // Only clear if empty or on new selection, else keep existing value
                    if (!$expirationVisible.val()) {
                        $expirationVisible.val('');
                        $expirationHidden.val('');
                    }
                } else {
                    $expirationVisible.prop('readonly', true);
                    $expirationVisible.val(''); // Clear when expiration is not applicable
                    $expirationHidden.val('');
                }
            }

            $issuedId.on('change', function() {
                updateExpirationField();
            });

            $expirationVisible.on('input', function() {
                $expirationHidden.val(this.value);
            });

            // Run once on page load to set readonly based on selected option,
            // but do NOT clear the value if it exists.
            updateExpirationField();
        });
    </script>

    <style>
        input[readonly] {
            background-color: #e9ecef;
            pointer-events: none;
        }
    </style>
@endsection
