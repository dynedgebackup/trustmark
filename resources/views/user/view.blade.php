@extends('layouts.app')

@section('content')
    <div class="d-flex justify-content-between align-items-center mb-4" style="margin-top: 15px;">
        <h3 class="page-title">PROFILE</h3>
    </div>

    <ol class="breadcrumb custom-breadcrumb">
        <li class="breadcrumb-item"><a href="{{ route('dashboard') }}"><span>Dashboard</span></a></li>
        <li class="breadcrumb-item"><a href="#"><span>Profile</span></a></li>
        <li class="breadcrumb-item"><a href="#"><span>View</span></a></li>
    </ol>

    <div id="wrapper">
        <div class="d-flex flex-column" id="content-wrapper">
            <form action="{{ route('profile.admin_update', $user->id) }}" method="POST" enctype="multipart/form-data">
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
                                                    placeholder="Mobile No." required maxlength="10"
                                                    value="{{ preg_replace('/^\+63/', '', $user->ctc_no ?? '') }}">
                                            </div>

                                            <input type="hidden" name="ctc_no" id="full_number"
                                                value="{{ $user->ctc_no ?? '' }}">
                                        </div>
                                    </div>
                                    <div class="col">
                                        <div class="mb-3">
                                            <label class="form-label custom-label">Email <span
                                                    class="required-field">*</span></label>
                                            <input class="form-control custom-input" type="email" id="email"
                                                name="email" placeholder="Email" required value="{{ $user->email }}">
                                        </div>
                                    </div>
                                    <div class="col-md-12" style="">
                                        <div class="row">
                                            <div class="col-md-1" style="">
                                                <div class="form-group">
                                                    
                                                    <div class="form-icon-user">

                                                    <input type="checkbox" 
                                                        name="is_primary" 
                                                        id="is_primary" 
                                                        class="form-check-input code" 
                                                        value="1" 
                                                        {{ old('is_primary', $user->is_primary) ? 'checked' : '' }}>
                                                    <label for="is_primary" class="form-check-label" style="color: #000;font-size: 12px;font-weight: bold;">Primary</label>
                                                    
                                                    </div>
                                                    <span class="validate-err" id="err_is_primary"></span>
                                                </div>
                                            </div>
                                            <div class="col-md-1" style="">
                                                <div class="form-group">
                                                    
                                                    <div class="form-icon-user">

                                                    <input type="checkbox" 
                                                        name="is_admin" 
                                                        id="is_admin" 
                                                        class="form-check-input code" 
                                                        value="1" 
                                                        {{ old('is_admin', $user_admins->is_admin ?? 0) ? 'checked' : '' }}>
                                                    <label for="is_admin" class="form-check-label" style="color: #000;font-size: 12px;font-weight: bold;">Admin</label>
                                                    
                                                    </div>
                                                    <span class="validate-err" id="err_is_admin"></span>
                                                </div>
                                            </div>
                                            
                                            <div class="col-md-2" style="">
                                                <div class="form-group">
                                                    
                                                    <div class="form-icon-user">

                                                    <input type="checkbox" 
                                                        name="is_evaluator" 
                                                        id="is_evaluator" 
                                                        class="form-check-input code" 
                                                        value="1" 
                                                        {{ old('is_evaluator', $user_admins->is_evaluator ?? 0) ? 'checked' : '' }}>
                                                    <label for="is_evaluator" class="form-check-label" style="color: #000;font-size: 12px;font-weight: bold;">Evaluator</label>
                                                    
                                                    </div>
                                                    <span class="validate-err" id="err_is_evaluator"></span>
                                                </div>
                                            </div>
                                            <div class="col-md-12" style="float: inline-end;">
                                            <button class="btn btn-primary" type="submit"
                                    style="font-family: sans-serif;font-size: 13px;margin-bottom: 15px;float: inline-end;">Update</button>
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
                                                placeholder="Confirm Password">

                                            <small id="password-error" class="text-danger"
                                                style="display:none;">Passwords
                                                do not match.</small>
                                            <small id="password-success" class="text-success"
                                                style="display:none;">Passwords match.</small>
                                        </div>
                                    </div>
                                    <div class="col-md-12" style="float: inline-end;">
                                            <button class="btn btn-primary" type="submit"
                                    style="font-family: sans-serif;font-size: 13px;margin-bottom: 15px;float: inline-end;">Update</button>
                                            </div>
                                </div>
                            </div>
                        </div>
                        <br>
                            <div class="card">
                                <div class="card-header">
                                    <h6 class="text-primary fw-bold m-0" style="font-size: 12px;">Permission</h6>
                                </div>
                                
                                <div class="card-body">
                                <div class="row">
                                   <div class="col-md-10 mb-3">
                                    <center>
                                            <a href="#" id="checkAll">[ Check All ]</a> |
                                            <a href="#" id="uncheckAll">[ Uncheck All ]</a>
                                    </center>
                                    </div>
                                    <div class="col-md-2 mb-3" style="text-align: end;">
                                    <button id="savePermissions" class="btn btn-primary" >Update</button>
                                    </div>
                                </div>
                                

                                <div class="container">
                                    <div class="row">
                                    @foreach ($modules as $groupId => $items)
                                        @php
                                            $groupName = $items->first()->group_name;
                                            $validModules = $items->whereNotNull('module_id');
                                            $hasModules = $validModules->count() > 0;

                                            // all modules checked
                                            $allChecked = $hasModules
                                                ? $validModules->every(fn ($m) => in_array($m->module_id, $assignedModuleIds))
                                                : false;

                                            // at least one module checked
                                            $anyChecked = $hasModules
                                                ? $validModules->contains(fn ($m) => in_array($m->module_id, $assignedModuleIds))
                                                : false;

                                            // group checkbox logic
                                            $groupChecked = $hasModules
                                                ? ($allChecked || $anyChecked)
                                                : in_array($groupId, $assignedGroupIdsWithoutModules);
                                        @endphp


                                        <div class="col-md-4 mb-3">
                                            <div class="card">
                                                <div class="card-header d-flex justify-content-between align-items-center">
                                                    <div class="d-flex align-items-center">
                                                    <input type="checkbox"
                                                        class="group-checkbox allcode me-2"
                                                        data-group-id="{{ $groupId }}"
                                                        {{ $groupChecked ? 'checked' : '' }}>
                                                    
                                                            <strong>{{ $groupName }} </strong>
                                                           
                                                        
                                                    </div>
                                                    @if($hasModules)
                                                        <span class="toggle-icon">▼</span>
                                                    @endif
                                                </div>

                                                {{-- MODULES --}}
                                                @if($hasModules)
                                                    <div class="card-bodyMenu" style="display:none; padding-left:13px;">
                                                        @foreach ($validModules as $module)
                                                            <div class="form-check">
                                                                <input type="checkbox"
                                                                    class="form-check-input module-checkbox allcode"
                                                                    data-group-id="{{ $groupId }}"
                                                                    name="modules[]"
                                                                    value="{{ $module->module_id }}"
                                                                    {{ in_array($module->module_id, $assignedModuleIds) ? 'checked' : '' }}>
                                                                <label class="form-check-label">
                                                                    {{ $module->module_name }}
                                                                </label>
                                                            </div>
                                                        @endforeach
                                                    </div>
                                                @else
                                                   
                                                @endif

                                            </div>
                                        </div>
                                    @endforeach




                                    </div>
                                </div>



                                </div>
                            </div>
                    </div>
                </div>

               
            </form>
        </div>
    </div>

    <script>
    $('#savePermissions').on('click', function () {
        const userId = {{ $user->id }}; 

        let permissions = [];
        let groupsWithModules = {};
        $('.module-checkbox:checked').each(function () {
            const groupId = $(this).data('group-id');
            const moduleId = $(this).val();

            permissions.push({
                user_id: userId,
                menu_group_id: groupId,
                menu_module_id: moduleId
            });

            groupsWithModules[groupId] = true;
        });
        $('.group-checkbox:checked').each(function () {
            const groupId = $(this).data('group-id');
            if (!groupsWithModules[groupId]) {
                permissions.push({
                    user_id: userId,
                    menu_group_id: groupId,
                    menu_module_id: 0 
                });
            }
        });

    console.log('Permissions to send:', permissions); 
    $.ajax({
        url: '{{ route("permissions.save") }}',
        type: 'POST',
        data: {
            _token: '{{ csrf_token() }}',
            permissions: permissions
        },
        success: function (response) {
            // Swal.fire({
            //     icon: 'success',
            //     title: 'Success!',
            //     text: 'Permissions saved successfully.',
            //     confirmButtonColor: '#3085d6'
            // });
        },
        error: function () {
            Swal.fire({
                icon: 'error',
                title: 'Oops...',
                text: 'Something went wrong while saving permissions.',
                confirmButtonColor: '#d33'
            });
        }
    });
});
</script>


<script>
$(document).ready(function () {
    $('.card-header').on('click', function (e) {
        if ($(e.target).is('input[type="checkbox"]')) return;

        const $cardBody = $(this).next('.card-bodyMenu');
        const $icon = $(this).find('.toggle-icon');

        if (!$cardBody.length) return; 

        $cardBody.slideToggle();
        $icon.text($icon.text() === '▼' ? '▲' : '▼');
    });

    /* Group checkbox */
    $('.group-checkbox').on('change', function () {
        const $card = $(this).closest('.card');
        const $modules = $card.find('.module-checkbox');

        // only toggle modules if exist
        if ($modules.length) {
            $modules.prop('checked', this.checked);
        }
    });

    /* Module checkbox → group checkbox (ALL) */
    // $('.module-checkbox').on('change', function () {
    //     const $card = $(this).closest('.card');
    //     const $modules = $card.find('.module-checkbox');
    //     const $group = $card.find('.group-checkbox');

    //     const allChecked = $modules.length === $modules.filter(':checked').length;
    //     $group.prop('checked', allChecked);
    // });

    /* Check All */
    $('#checkAll').click(function (e) {
        e.preventDefault();
        $('.allcode').prop('checked', true);
        $('.card-bodyMenu').slideDown();
        $('.toggle-icon').text('▲');
    });

    /* Uncheck All */
    $('#uncheckAll').click(function (e) {
        e.preventDefault();
        $('.allcode').prop('checked', false);
        $('.card-bodyMenu').slideUp();
        $('.toggle-icon').text('▼');
    });

});
</script>




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
@endsection
