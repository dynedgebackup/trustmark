@extends('layouts.app')

@section('content')
    <div class="d-flex justify-content-between align-items-center mb-4" style="margin-top: 15px;">
        <h3 class="page-title">TRUSTMARK</h3>
    </div>

    <ol class="breadcrumb custom-breadcrumb">
        <li class="breadcrumb-item"><a href="{{ route('dashboard') }}"><span>Dashboard</span></a></li>
        <li class="breadcrumb-item"><a href="#"><span>New</span></a></li>
    </ol>
    <div id="wrapper">
        <div class="d-flex flex-column" id="content-wrapper">

            <div id="multple-step-form-n-1" class="container overflow-hidden py-5" style="margin-top: -80px;">
                <div id="progress-bar-button-1" class="multisteps-form" style="margin-bottom: 0;">
                    <div class="row">
                        <div class="col-12 col-lg-10 mx-auto mb-4">
                            <div class="multisteps-form__progress">
                                <div class="btn multisteps-form__progress-btn disabled js-active" title="Corporation">
                                    Business Information</div>
                                <div class="btn multisteps-form__progress-btn disabled {{ session('go_to_details') || session('go_to_documents') || session('go_to_confirmations') ? 'js-active' : '' }}"
                                    title="Details">Business Address</div>
                                <div class="btn multisteps-form__progress-btn disabled {{ session('go_to_documents') || session('go_to_confirmations') ? 'js-active' : '' }}"
                                    title="Documents">Documents</div>
                                <div class="btn multisteps-form__progress-btn disabled {{ session('go_to_confirmations') ? 'js-active' : '' }}"
                                    title="Documents">Confirmation</div>
                            </div>
                        </div>
                    </div>
                </div>

                <style>
                    .text-center {
                        text-align: left !important;
                    }
                    .btn-outline-danger {
                        background: #e74a3b;
                        color: #fff;
                    }
                </style>
                <div class="row" id="multistep-start-row-1" style="margin-top: -80px;">
                    <div class="col-12 col-lg-8 m-auto" id="multistep-start-column-1"
                        style="width: 1095px;padding-right: 0px;padding-left: 0px;">
                        <div class="multisteps-form__form">

                            <div id="corporation"
                                class="bg-white shadow p-4 rounded multisteps-form__panel {{ session('go_to_details') || session('go_to_documents') || session('go_to_confirmations') ? '' : 'js-active' }}"
                                data-animation="scaleIn">
                                <div class="divider-line" style="margin-bottom: 7px;"></div>
                                <h3 class="text-center multisteps-form__title"
                                    style="font-family: sans-serif;font-size: 18px;color: rgb(0,0,0);font-weight: bold;">
                                    Business Information</h3>
                                <div class="divider-line"></div>
                                <div class="card-body">
                                    <form action="{{ route('business.save_corporation') }}" method="POST"
                                        enctype="multipart/form-data" autocomplete="off"
                                        onsubmit="return prepareUrlsForSubmit()">
                                        @csrf
                                        <div id="form-content-1" class="multisteps-form__content">
                                            <input type="hidden" name="business_id" id="business_id"
                                                value="{{ $business_id ?? '' }}">

                                            <div class="row">
                                                <div class="col-12">
                                                    <div class="mb-3">
                                                        <label class="form-label custom-label">Business Type:<span
                                                                class="required-field">*</span></label>
                                                        <select class="form-select custom-input" name="type_id"
                                                            id="type_id" required>
                                                            <option value="" disabled selected>Select Business Type
                                                            </option>
                                                            @foreach ($type_corporation as $type)
                                                                <option value="{{ $type->id }}"
                                                                    data-type='@json($type)'
                                                                    {{ isset($selectedTypeId) && $selectedTypeId == $type->id ? 'selected' : '' }}>
                                                                    {{ $type->name }}
                                                                </option>
                                                            @endforeach
                                                        </select>
                                                    </div>
                                                </div>
                                            </div>

                                            <div class="row">
                                                <div class="col-12 col-sm-6 col-md-6">
                                                    <div class="mb-3">
                                                        <label class="form-label custom-label" id="reg-label">
                                                            Registration Number: <span class="required-field">*</span>
                                                        </label>
                                                        <input class="form-control custom-input" type="text"
                                                            name="reg_num" id="reg_num" placeholder="Registration Number"
                                                            required value="{{ old('reg_num', $business->reg_num) }}"
                                                            maxlength="25">
                                                    </div>
                                                </div>
                                                <div class="col-12 col-sm-6 col-md-6">
                                                    <div class="mb-3">
                                                        <label class="form-label custom-label">Tax Identification Number
                                                            (TIN): <span class="required-field">*</span></label>
                                                        <input class="form-control custom-input" type="text"
                                                            name="tin_num" id="tin_num"
                                                            placeholder="Tax Identification Number (TIN)" required
                                                            maxlength="17" value="{{ $business->tin }}">
                                                        <div id="tin-error" class="text-danger mt-1"
                                                            style="font-size: 13px;"></div>
                                                    </div>
                                                </div>
                                            </div>

                                            <div class="row">
                                                <div class="col-12 col-sm-6 col-md-6">
                                                    <div class="mb-3">
                                                        <label class="form-label custom-label">Business Name: <span
                                                                class="required-field">*</span></label>
                                                        <input class="form-control custom-input" type="text"
                                                            name="business_name" id="business_name"
                                                            placeholder="Business Name" required maxlength="130"
                                                            value="{{ $business->business_name }}">
                                                        <small id="businessNameError" class="text-danger d-none">Business
                                                            name must not exceed 130 characters.</small>

                                                    </div>
                                                </div>
                                                <div class="col-12 col-sm-6 col-md-6">
                                                    <div class="mb-3">
                                                        <label class="form-label custom-label">Trade Name
                                                            (if applicable):</label>
                                                        <input class="form-control custom-input" type="text"
                                                            name="franchise" id="form" placeholder="Trade Name"
                                                            value="{{ $business->franchise }}">
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="row">
                                                <div class="col-12 col-sm-6 col-md-12">
                                                    <div class="mb-3">
                                                        <label class="form-label custom-label">Business Category: <span
                                                                class="required-field">*</span></label>
                                                        <select class="form-select custom-select" id="category"
                                                            name="category" required>
                                                            <option value="">Select Category</option>
                                                            @foreach ($categories as $category)
                                                                <option value="{{ $category->id }}"
                                                                    data-is-others="{{ $category->is_others }}"
                                                                    {{ ($business->category_id ?? '') == $category->id ? 'selected' : '' }}>
                                                                    {{ $category->name }}
                                                                </option>
                                                            @endforeach
                                                        </select>
                                                    </div>
                                                </div>
                                            </div>

                                            <div class="row">
                                                <div class="col-12 col-sm-6 col-md-12" id="desc-container">
                                                    <div class="mb-3">
                                                        <label class="form-label custom-label">Description: <span
                                                                class="required-field">*</span></label>
                                                        <input class="form-control custom-input" type="text"
                                                            name="other_category" id="other_category"
                                                            placeholder="Category Description"
                                                            value="{{ $business->category_other_description }}">
                                                    </div>
                                                </div>
                                            </div>

                                            <br> <br>
                                            <div class="divider-line" style="margin-bottom: 7px;"></div>
                                            <h3 class="text-center multisteps-form__title"
                                                style="font-family: sans-serif;font-size: 18px;color: rgb(0,0,0);font-weight: bold;">
                                                Business | Online Store URL</h3>
                                                <button type="button" class="btn btn-primary btn-sm"
                                                        onclick="addUrlField()"
                                                        style="font-family:sans-serif;font-size:14px;margin-top: -32px;float: right;">
                                                        Add Online Store URL
                                                    </button>
                                            <div class="divider-line"></div>
                                            <br>

                                            <div id="url-fields-wrapper" class="container-fluid">
                                                <!-- Label and Add Button -->
                                                <div class="d-flex justify-content-between align-items-center mb-2">
                                                    <table style="border: 2px solid #ccc;width: 100%;"><tr><th style="border-right: 2px solid #ccc;
                                                    padding: 10px;width: 58%;background: #3e5cb2;"> <label class="form-label custom-label mb-0" style="color: #fff;">
                                                        Business URL: <span class="required-field">*</span>
                                                    </label></th>
                                                    <th style="border-right: 2px solid #ccc;
                                                    padding: 10px;width: 16%;text-align: center;background: #3e5cb2;"> <label class="form-label custom-label mb-0" style="color: #fff;">
                                                        Platform Name 
                                                    </label></th>
                                                     <th style="border-right: 2px solid #ccc;
                                                    padding: 10px;width: 12%;text-align: center;background: #3e5cb2;"> <label class="form-label custom-label mb-0" style="color: #fff;">
                                                        With IRM 
                                                    </label></th>
                                                    <th style="text-align: center;background: #3e5cb2;"><label class="form-label custom-label mb-0" style="color: #fff;">
                                                        Action
                                                    </label></th></tr></table>
                                                    
                                                    
                                                </div>

                                                <!-- Container for all URL input rows -->
                                                <div id="url-inputs-container" class="w-100"></div>

                                                <!-- Hidden input to hold JSON array of URLs -->
                                                <input type="hidden" name="url_platform_json" id="url_platform_json"
                                                    value="">
                                            </div>

                                            <br> <br>
                                            <div class="divider-line" style="margin-bottom: 7px;"></div>
                                            <h3 class="text-center multisteps-form__title"
                                                style="font-family: sans-serif;font-size: 18px;color: rgb(0,0,0);font-weight: bold;">
                                                Authorized Representative</h3>
                                            <div class="divider-line"></div>
                                            <br>

                                            <div class="row">
                                                <div class="col-12 col-sm-6 col-md-4">
                                                    <div class="mb-3">
                                                        <label class="form-label custom-label">First Name <span
                                                                class="required-field">*</span></label>
                                                        <input class="form-control custom-input" type="text"
                                                            name="first_name" id="first_name" placeholder="First Name" required
                                                            value="{{ $business->first_name ?? $user->first_name }}">
                                                    </div>
                                                </div>
                                                <div class="col-12 col-sm-6 col-md-4">
                                                    <div class="mb-3">
                                                        <label class="form-label custom-label">Middle Name</label>
                                                        <input class="form-control custom-input" type="text"
                                                            name="middle_name" id="middle_name" placeholder="Middle Name" 
                                                            value="{{ $business->middle_name ?? $user->middle_name }}" >
                                                    </div>
                                                </div>
                                                <div class="col-12 col-sm-6 col-md-4">
                                                    <div class="mb-3">
                                                        <label class="form-label custom-label">Last Name <span
                                                                class="required-field">*</span></label>
                                                        <input class="form-control custom-input" type="text"
                                                            name="last_name" id="last_name" placeholder="Last Name" 
                                                            value="{{ $business->last_name ?? $user->last_name }}" required>
                                                    </div>
                                                </div>
                                                <div class="col-12 col-sm-6 col-md-4">
                                                    <div class="mb-3">
                                                        <label class="form-label custom-label">Suffix
                                                            </label>
                                                        <select class="form-select custom-input select2" id="suffix"
                                                            name="suffix">
                                                            <option value="">Select Suffix</option>
                                                            @foreach ($suffixs as $suffix)
                                                                <option 
                                                                    value="{{ $suffix->suffix ?? '' }}"
                                                                    data-type='@json($suffix)'
                                                                    {{ ($business->suffix ?? '') == ($suffix->suffix ?? '') ? 'selected' : '' }}>
                                                                    {{ $suffix->suffix ?? '' }}
                                                                </option>
                                                            @endforeach
                                                        </select>
                                                    </div>
                                                </div>
                                                <!-- <div class="col-12 col-sm-6 col-md-4">
                                                    <div class="mb-3">
                                                        <label class="form-label custom-label">Name: <span
                                                                class="required-field">*</span></label>
                                                        <input class="form-control custom-input" type="text"
                                                            name="name" id="name" placeholder="Name" required
                                                            value="{{ $business->pic_name ?? $user->name }}" readonly>
                                                    </div>
                                                </div> -->
                                                <div class="col-12 col-sm-6 col-md-4">
                                                    <div class="mb-3">
                                                        <label class="form-label custom-label">Mobile No.: <span
                                                                class="required-field">*</span></label>
                                                        <div class="input-group">
                                                            <span class="input-group-text custom-input" style="border: 2px solid;">+63</span>
                                                            <input class="form-control custom-input"
                                                                type="tel"
                                                                id="number"
                                                                name="number"
                                                                placeholder="Mobile No."
                                                                required
                                                                maxlength="10"
                                                                inputmode="numeric"
                                                                pattern="[0-9]{10}"
                                                                oninput="updateFullNumber(this)"
                                                                value="{{ preg_replace('/^\+63/', '', $business->pic_ctc_no ?? ($user->ctc_no ?? '')) }}">
                                                        </div>

                                                        <input type="hidden" name="ctc_no" id="full_number"
                                                            value="{{ $business->pic_ctc_no ?? ($user->ctc_no ?? '') }}">
                                                            
                                                    </div>
                                                </div>
                                                <div class="col-12 col-sm-6 col-md-4">
                                                    <div class="mb-3">
                                                        <label class="form-label custom-label">Email: <span
                                                                class="required-field">*</span></label>
                                                        <input class="form-control custom-input" type="email"
                                                            name="email" id="email" placeholder="Email" required
                                                            value="{{ $business->pic_email ?? $user->email }}" readonly>
                                                        <div id="email-error" class="text-danger mt-1"
                                                            style="font-size: 13px;"></div>
                                                    </div>
                                                </div>
                                            </div>

                                            <div class="row" style="margin-bottom:10px;">
                                                <div class="col col-12 col-sm-12 col-md-12">
                                                    <div class="mb-3">
                                                        <label class="form-label custom-label">Government Issued
                                                            ID<span class="required-field">*</span></label>
                                                        <select class="form-select custom-input select2" id="issued_id"
                                                            name="issued_id" required>
                                                            <option value="">Select Government Issued ID</option>
                                                            {{-- @foreach ($requirements as $id => $name)
                                                                <option value="{{ $id }}"
                                                                    {{ ($business->requirement_id ?? '') == $id ? 'selected' : '' }}>
                                                                    {{ $name }}
                                                                </option>
                                                            @endforeach --}}
                                                            @foreach ($requirements as $req)
                                                                <option value="{{ $req->id }}"
                                                                    data-with-expiration="{{ trim($req->with_expiration) }}"
                                                                    {{ old('requirement_id', $business->requirement_id ?? ($user->requirement_id ?? '')) == $req->id ? 'selected' : '' }}>
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

                                                        @php
                                                        $filePath = !empty($business->requirement_upload)
                                                            ? $business->requirement_upload
                                                            : $user->requirement_upload;

                                                        $fileRoute = !empty($business->requirement_upload)
                                                            ? route('business.download_authorized', encrypt($business->id))
                                                            : route('profile.download_authorized', $user->id);

                                                        $filename = $filePath ? basename($filePath) : '';
                                                        $ext = $filePath ? strtolower(pathinfo($filePath, PATHINFO_EXTENSION)) : '';
                                                    @endphp

                                                    @if ($filePath)
                                                        <a href="{{ $fileRoute }}" class="d-flex align-items-center gap-2" target="_blank">
                                                            <i class="fa fa-download"></i>
                                                            <span class="custom-label" title="{{ $filename }}">{{ $filename }}</span>
                                                        </a>

                                                        @if (in_array($ext, ['jpg', 'jpeg', 'png']))
                                                            <div class="mt-2">
                                                                <img src="{{ asset('storage/' . $filePath) }}"
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
                                                        <label class="form-label custom-label">Expiry Date<span
                                                                class="required-field">*</span></label>
                                                        <input class="form-control custom-input" type="date"
                                                            id="expirationDateInputVisible" name="expiration_date_visible"
                                                            placeholder="Date" min="{{ $today }}"
                                                            value="{{ old('requirement_expired', $business->requirement_expired ?? ($user->requirement_expired ?? '')) }}"
                                                            readonly>

                                                        <input type="hidden" name="expired_date"
                                                            id="expirationDateInput"
                                                            value="{{ old('requirement_expired', $business->requirement_expired ?? ($user->requirement_expired ?? '')) }}">
                                                    </div>
                                                </div>
                                            </div>

                                            <div class="d-flex justify-content-between align-items-center mt-4">
                                                <span class="form-label custom-label" style="color: red">
                                                    NOTE: Draft applications will be deleted after {{ $settings->value }}
                                                    business days.
                                                </span>

                                                <button class="btn btn-primary" type="submit" title="Next"
                                                    id="saveAppointmentBtn"
                                                    style="font-family:sans-serif;font-size:14px;">
                                                    Continue
                                                </button>
                                            </div>

                                        </div>
                                    </form>
                                </div>
                            </div>

                            <div id="details"
                                class="bg-white shadow p-4 rounded multisteps-form__panel {{ session('go_to_details') ? 'js-active' : '' }}"
                                data-animation="scaleIn" style="height: 520px;">
                                <form action="{{ route('business.save_detail') }}" method="POST" id="detailsForm"
                                    autocomplete="off">
                                    @csrf
                                    <div class="divider-line" style="margin-bottom: 7px;"></div>
                                    <h3 class="text-center multisteps-form__title"
                                        style="font-family: sans-serif;font-size: 18px;color: rgb(0,0,0);font-weight: bold;">
                                        Business Address</h3>
                                    <div class="divider-line"></div>

                                    <div id="form-content-1" class="multisteps-form__content">

                                        <input type="hidden" name="business_id" id="business_id"
                                            value="{{ $business->id }}">
                                        <div class="row">
                                            <div class="col-md-12">
                                                <div class="mb-3">
                                                    <label class="form-label custom-label">Complete Address: <span
                                                            class="required-field">*</span></label>
                                                    <textarea class="form-control custom-input" name="address" id="address" cols="30" rows="4" required>{{ $business->complete_address }}</textarea>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="row">
                                            <div class="col-12 col-sm-6 col-md-6">
                                                <div class="mb-3">
                                                    <label class="form-label custom-label">Region<span
                                                            class="required-field">*</span></label>
                                                    <select class="form-select custom-select" id="region"
                                                        name="region" required>
                                                        <option value="">Select Region</option>
                                                        @foreach ($regions as $id => $reg_region)
                                                            <option value="{{ $id }}"
                                                                {{ ($business->region_id ?? '') == $id ? 'selected' : '' }}>
                                                                {{ $reg_region }}
                                                            </option>
                                                        @endforeach
                                                    </select>
                                                </div>
                                            </div>
                                            <div class="col-12 col-sm-6 col-md-6">
                                                <div class="mb-3">
                                                    <label class="form-label custom-label">Province<span
                                                            class="required-field">*</span></label>
                                                    <select class="form-select custom-select" id="province"
                                                        name="province" required>
                                                        <option value="">Select Province</option>
                                                        @if (!empty($business->region_id))
                                                            @php
                                                                $provinces = \App\Models\Province::where(
                                                                    'reg_no',
                                                                    $business->region_id,
                                                                )->pluck('prov_desc', 'id');
                                                            @endphp
                                                            @foreach ($provinces as $id => $name)
                                                                <option value="{{ $id }}"
                                                                    {{ old('province', $business->province_id ?? '') == $id ? 'selected' : '' }}>
                                                                    {{ $name }}
                                                                </option>
                                                            @endforeach
                                                        @endif
                                                    </select>
                                                </div>
                                            </div>
                                            <div class="col-12 col-sm-6 col-md-6">
                                                <div class="mb-3">
                                                    <label class="form-label custom-label">Municipality<span
                                                            class="required-field">*</span></label>
                                                    <select class="form-select custom-select" id="municipality"
                                                        name="municipality" required>
                                                        <option value="">Select Municipality</option>
                                                        @if (!empty($business->municipality_id))
                                                            @php
                                                                $municipalities = \App\Models\Municipality::where(
                                                                    'prov_no',
                                                                    $business->province_id,
                                                                )->pluck('mun_desc', 'id');
                                                            @endphp
                                                            @foreach ($municipalities as $id => $name)
                                                                <option value="{{ $id }}"
                                                                    {{ old('municipality', $business->municipality_id ?? '') == $id ? 'selected' : '' }}>
                                                                    {{ $name }}
                                                                </option>
                                                            @endforeach
                                                        @endif
                                                    </select>
                                                </div>
                                            </div>
                                            <div class="col-12 col-sm-6 col-md-6">
                                                <div class="mb-3">
                                                    <label class="form-label custom-label">Barangay<span
                                                            class="required-field">*</span></label>
                                                    <select class="form-select custom-select" id="barangay"
                                                        name="barangay" required>
                                                        <option value="">Select Barangay</option>
                                                        @if (!empty($business->barangay_id))
                                                            @php
                                                                $barangays = DB::table('barangays AS bgf')
                                                                    ->join('regions AS pr', 'pr.id', '=', 'bgf.reg_no')
                                                                    ->join(
                                                                        'provinces AS pp',
                                                                        'pp.id',
                                                                        '=',
                                                                        'bgf.prov_no',
                                                                    )
                                                                    ->join(
                                                                        'municipalities AS pm',
                                                                        'pm.id',
                                                                        '=',
                                                                        'bgf.mun_no',
                                                                    )
                                                                    ->where('bgf.is_active', 1)
                                                                    ->where('bgf.mun_no', $business->municipality_id)
                                                                    ->pluck('bgf.brgy_name', 'bgf.id');
                                                            @endphp

                                                            @foreach ($barangays as $id => $name)
                                                                <option value="{{ $id }}"
                                                                    {{ old('barangay', $business->barangay_id ?? '') == $id ? 'selected' : '' }}>
                                                                    {{ $name }}
                                                                </option>
                                                            @endforeach
                                                        @endif
                                                    </select>
                                                </div>
                                            </div>
                                        </div>

                                        <div class="d-flex justify-content-between align-items-center mt-4"
                                            id="next-prev-buttons-3">
                                            <span class="form-label custom-label" style="color: red">
                                                NOTE: Draft applications will be deleted after {{ $settings->value }}
                                                business days.
                                            </span>

                                            <div>
                                                @php
                                                    use Hashids\Hashids;
                                                    $hashids = new Hashids(config('app.key'), 10);
                                                    $ids = $hashids->encode((int) $business->id);
                                                @endphp
                                                <button class="btn btn-danger me-2" type="button" title="Previous"
                                                    style="font-family:sans-serif;font-size:14px;"
                                                    onclick="window.location.href='{{ route('business.create', ['business_id' => $ids]) }}'">
                                                    Previous
                                                </button>
                                                <button class="btn btn-primary" type="submit" title="Next"
                                                    id="saveDetailsBtn" style="font-family:sans-serif;font-size:14px;">
                                                    Continue
                                                </button>
                                            </div>
                                        </div>

                                    </div>
                                </form>
                            </div>

                            <div id="document"
                                class="bg-white shadow p-4 rounded multisteps-form__panel {{ session('go_to_documents') ? 'js-active' : '' }}">
                                <form action="{{ route('business.save_document') }}" method="POST" id="documentsForm"
                                    enctype="multipart/form-data" autocomplete="off">
                                    @csrf
                                    <div class="divider-line" style="margin-bottom: 7px;"></div>
                                    <h3 class="text-center multisteps-form__title"
                                        style="font-family: sans-serif;font-size: 18px;color: rgb(0,0,0);font-weight: bold;">
                                        Documents</h3>
                                    <div class="divider-line"></div>

                                    <div id="form-content-1" class="multisteps-form__content">

                                        <input type="hidden" name="business_id" id="business_id"
                                            value="{{ old('business_id', $business->id ?? '') }}">

                                        <div class="row">
                                            <div class="col-12 col-sm-6 col-md-6">
                                                <div class="mb-3">
                                                    <label class="form-label custom-label">Business Registration
                                                        (SEC/DTI/CDA): <span class="required-field">*</span></label>
                                                </div>
                                            </div>
                                            <div class="col-12 col-sm-6 col-md-6">
                                                <div class="mb-3">
                                                    @php
                                                        $hasFile = !empty($business->docs_business_reg);
                                                        $filename = basename($business->docs_business_reg);
                                                        $shortName =
                                                            strlen($filename) > 15
                                                                ? substr($filename, 0, 15) . '...'
                                                                : $filename;
                                                    @endphp

                                                    @if ($hasFile)
                                                        <a href="{{ route('business.download_business_registration', $business->id) }}"
                                                            class="d-flex align-items-center gap-2" target="_blank">
                                                            <i class="custom-icon fa fa-download"></i>
                                                            <span class="custom-label"
                                                                title="{{ $filename }}">{{ $shortName }}</span>
                                                        </a>
                                                    @endif

                                                    <input class="form-control custom-input" type="file"
                                                        id="business_reg" name="business_reg"
                                                        accept=".jpg,.jpeg,.png,.pdf"
                                                        title="Please upload file type like .jpg, .jpeg, .png, .pdf. Maximum file size untill 10 MB"
                                                        @if (!$hasFile) required @endif>
                                                    <small id="error-business_reg" class="text-danger"
                                                        style="display: none; font-size: 13px;"></small>
                                                </div>
                                            </div>
                                        </div>

                                        <div class="row">
                                            <div class="col-12 col-sm-6 col-md-6">
                                                <div class="mb-3">
                                                    <label class="form-label custom-label">BIR 2303: <span
                                                            class="required-field">*</span></label>
                                                </div>
                                            </div>
                                            <div class="col-12 col-sm-6 col-md-6">
                                                <div class="mb-3">
                                                    @php
                                                        $hasFile = !empty($business->docs_bir_2303);
                                                        $filename = basename($business->docs_bir_2303);
                                                        $shortName =
                                                            strlen($filename) > 15
                                                                ? substr($filename, 0, 15) . '...'
                                                                : $filename;
                                                    @endphp

                                                    @if ($hasFile)
                                                        <a href="{{ route('business.download_bir_2303', $business->id) }}"
                                                            class="d-flex align-items-center gap-2" target="_blank">
                                                            <i class="custom-icon fa fa-download"></i>
                                                            <span class="custom-label"
                                                                title="{{ $filename }}">{{ $shortName }}</span>
                                                        </a>
                                                    @endif

                                                    <input class="form-control custom-input" type="file"
                                                        id="bir_2303" name="bir_2303" accept=".jpg,.jpeg,.png,.pdf"
                                                        title="Please upload file type like .jpg, .jpeg, .png, .pdf. Maximum file size untill 10 MB"
                                                        @if (!$hasFile) required @endif>
                                                    <small id="error-bir_2303" class="text-danger"
                                                        style="display: none; font-size: 13px;"></small>
                                                </div>
                                            </div>
                                        </div>

                                        <div class="row">
                                            <div class="col-12 col-sm-6 col-md-6">
                                                <div class="mb-3">
                                                    <label class="form-label custom-label">Internal Redress Mechanism:
                                                        <span class="required-field">*</span></label>
                                                    <br>
                                                    <label class="form-label custom-label">
                                                        To assist in establishing an Internal Redress Mechanism,<br>
                                                        a guideline template is available for download
                                                        <a href="{{ route('internal.redress.download') }}?v={{ time() }}"
                                                            target="_blank"
                                                            style="color: blue; text-decoration: underline;">
                                                            here
                                                        </a>
                                                    </label>
                                                </div>
                                            </div>
                                            <div class="col-12 col-sm-6 col-md-6">
                                                <div class="mb-3">
                                                    @php
                                                        $hasFile = !empty($business->docs_internal_redress);
                                                        $filename = basename($business->docs_internal_redress);
                                                        $shortName =
                                                            strlen($filename) > 15
                                                                ? substr($filename, 0, 15) . '...'
                                                                : $filename;
                                                    @endphp

                                                    @if ($hasFile)
                                                        <a href="{{ route('business.download_internal_redress', $business->id) }}"
                                                            class="d-flex align-items-center gap-2" target="_blank">
                                                            <i class="custom-icon fa fa-download"></i>
                                                            <span class="custom-label"
                                                                title="{{ $filename }}">{{ $shortName }}</span>
                                                        </a>
                                                    @endif
                                                    @if($settingsIrm->value == 1)
                                                    <input class="form-control custom-input" type="file"
                                                        id="internal_redress" name="internal_redress"
                                                        accept=".jpg,.jpeg,.png,.pdf" style="width: 67%;"
                                                        title="Please upload file type like .jpg, .jpeg, .png, .pdf. Maximum file size untill 10 MB"
                                                        @if (!$hasFile) required @endif>
                                                         
                                                        <button type="button" class="btn btn-primary" id="btnManageIRM"
                                                        style=" padding: 7px 8px; font-size: 12px; float: inline-end;margin-top: -32px;margin-right: 73px;">
                                                        Manage IRM
                                                       </button>
                                                       @if (!empty($business->id))
                                                       <a id="downloadBtn" href="{{ empty($business_irm) ? 'javascript:void(0);' : route('business.certificate', $business->id) }}"
                                                        target="_blank"
                                                        class="btn btn-primary {{ empty($business_irm) ? 'disabled' : '' }}"
                                                        style="padding: 6px 8px; font-size: 12px; float: inline-end; margin-top: -32px;
                                                                {{ empty($business_irm) ? 'pointer-events: none; opacity: 0.6;' : '' }}">
                                                        Download
                                                        </a>
                                                        @else
                                                            <button type="button" class="btn btn-secondary" disabled
                                                                    style="padding: 7px 8px; font-size: 12px; float: inline-end; margin-top: -32px;">
                                                                Download
                                                            </button>
                                                        @endif
                                                       @else
                                                       <input class="form-control custom-input" type="file"
                                                        id="internal_redress" name="internal_redress"
                                                        accept=".jpg,.jpeg,.png,.pdf" style="width: 100%;"
                                                        title="Please upload file type like .jpg, .jpeg, .png, .pdf. Maximum file size untill 10 MB"
                                                        @if (!$hasFile) required @endif>
                                                       @endif
                                                    <small id="error-internal_redress" class="text-danger"
                                                        style="display: none; font-size: 13px;"></small>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="row">
                                            <div class="col-md-6">
                                                <div class="mb-3">
                                                    <label class="form-label custom-label">Are you Barangay Micro Business
                                                        Enterprise(BMBE) registered?

                                                        <span class="required-field">*</span></label>
                                                    <br>
                                                    <label class="form-label custom-label">
                                                        If yes, please upload your BMBE Certificate.
                                                    </label>
                                                </div>
                                            </div>
                                            <div class="col-md-6">
                                                <div class="mb-3">
                                                    <label class="custom-label">
                                                        <input type="radio" name="is_bmbe" value="1"
                                                            {{ old('is_bmbe', $business->is_bmbe) == 1 ? 'checked' : '' }}>
                                                        Yes
                                                    </label>

                                                    <label class="custom-label" style="margin-left:15px;">
                                                        <input type="radio" name="is_bmbe" value="0"
                                                            {{ old('is_bmbe', $business->is_bmbe) == 0 ? 'checked' : '' }}>
                                                        No
                                                    </label>
                                                    @php
                                                        $hasFilebmbe_doc = !empty($business->bmbe_doc);
                                                        $filename = basename($business->bmbe_doc);
                                                        $shortName =
                                                            strlen($filename) > 15
                                                                ? substr($filename, 0, 15) . '...'
                                                                : $filename;
                                                    @endphp
                                                    @if ($hasFilebmbe_doc)
                                                        <a href="{{ route('business.download_bmbe_doc', $business->id) }}"
                                                            class="d-flex align-items-center gap-2" target="_blank"
                                                            style="margin-top: -20px;padding-left: 119px;">
                                                            <i class="custom-icon fa fa-download"></i>
                                                            <span class="custom-label"
                                                                title="{{ $filename }}">{{ $shortName }}</span>
                                                        </a>
                                                    @endif
                                                    <input class="form-control custom-input" type="file"
                                                        id="bmbe_doc" name="bmbe_doc" accept=".jpg,.jpeg,.png,.pdf"
                                                        title="Please upload file type like .jpg, .jpeg, .png, .pdf. Maximum file size untill 10 MB"
                                                        @if (!$hasFile) required1 @endif>
                                                    <small id="error-bmbe_doc" class="text-danger"
                                                        style="display: none; font-size: 13px;"></small>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="row" style="padding-bottom: 12px;">
                                            <div class="col-md-6">
                                                <div class="mb-3">
                                                   
                                                    <label class="form-label custom-label">
                                                        Business Category (based on Asset Size):
                                                        <span class="required-field">*</span></label>
                                                    </label>
                                                </div>
                                            </div>
                                            <div class="col-md-6">
                                                <div class="mb-3">
                                                <select class="form-select custom-input" name="busn_category_id" id="busn_category_id" required>
                                                    <option value="">Select Business Category</option>
                                                    @foreach ($business_category as $businesscategory)
                                                        <option 
                                                            value="{{ $businesscategory->busn_category_id ?? '' }}"
                                                            data-type='@json($businesscategory)'
                                                            @if(isset($business->busn_category_id))
                                                                {{ $business->busn_category_id == ($businesscategory->busn_category_id ?? '') ? 'selected' : '' }}
                                                            @elseif(($businesscategory->is_default ?? 0) == 1)
                                                                selected
                                                            @endif
                                                        >
                                                            {{ $businesscategory->busn_category_name ?? '' }}
                                                        </option>
                                                    @endforeach
                                                </select>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="row">
                                            <div class="col-md-6">
                                                <div class="mb-3">
                                                   
                                                    <label class="form-label custom-label">
                                                        Proof of Total Asset Valuation:
                                                        <span class="required-field">*</span></label>
                                                    </label>
                                                </div>
                                            </div>
                                            <div class="col-md-6">
                                                <div class="mb-3">
                                                   
                                                    @php
                                                        $hasFile = !empty($business->busn_valuation_doc);
                                                        $filename = basename($business->busn_valuation_doc);
                                                        $shortName =
                                                            strlen($filename) > 15
                                                                ? substr($filename, 0, 15) . '...'
                                                                : $filename;
                                                    @endphp
                                                    @if ($hasFile)
                                                        <a href="{{ route('business.download_busn_valuation_doc', $business->id) }}"
                                                            class="d-flex align-items-center gap-2" target="_blank"
                                                            style="margin-top: -20px;">
                                                            <i class="custom-icon fa fa-download"></i>
                                                            <span class="custom-label"
                                                                title="{{ $filename }}">{{ $shortName }}</span>
                                                        </a>
                                                    @endif
                                                    <input class="form-control custom-input" type="file"
                                                        id="busn_valuation_doc" name="busn_valuation_doc" accept=".jpg,.jpeg,.png,.pdf"
                                                        title="Please upload file type like .jpg, .jpeg, .png, .pdf. Maximum file size untill 10 MB"
                                                        @if (!$hasFile) required1 @endif>
                                                    <small id="error-busn_valuation_doc" class="text-danger"
                                                        style="display: none; font-size: 13px;"></small>
                                                </div>
                                            </div>
                                        </div>
                                        <br>
                                        <div class="divider-line" style="margin-bottom: 7px;"></div>
                                        <h3 class="text-center multisteps-form__title"
                                            style="font-family: sans-serif;font-size: 18px;color: rgb(0,0,0);font-weight: bold;">
                                            Additional Permits (For Regulated Products)</h3>
                                            <button type="button" class="btn btn-primary" id="addDocumentBtn"
                                            style=" padding: 4px 8px; font-size: 12px; float: inline-end;margin-top: -32px;">
                                            Add Document
                                        </button>
                                        <div class="divider-line"></div>
                                        
                                        <p style="font-size: 10px;margin-bottom: 2px !important;color: #bb2121;">
                                                    Please upload file type like .jpg, .jpeg, .png, .pdf. Maximum file size
                                                    is 10mb</p>
                                        <br>
                                        
                                        <div class="row document-row mb-2" id="document-row" style="background: #4e73df;">
                                            <div class="col-md-6" style="    border: 2px solid #ccc;padding: 6px;">
                                                <label class="form-label custom-label mb-0" style="color: #fff;">
                                                    Document Name
                                                </label>
                                            </div>
                                            <div class="col-md-5" style="    border: 2px solid #ccc;padding: 6px;border-left: none;border-right: none;">
                                                <label class="form-label custom-label mb-0" style="color: #fff;">
                                                    Attachment
                                                </label>
                                                
                                            </div>
                                            <div class="col-md-1" style="    border: 2px solid #ccc;padding: 6px;">
                                                <label class="form-label custom-label mb-0" style="color: #fff;">
                                                    Action
                                                </label>
                                                
                                            </div>
                                        </div>
                                        <div id="document-container">
                                            @foreach ($AdditionalDocuments as $doc)
                                                <div class="row document-row mb-2">
                                                    <div class="col-md-6" style="padding-top: 20px;">
                                                        <input type="text" class="form-control custom-input"
                                                            name="document_name[]" value="{{ $doc->name }}"
                                                            placeholder="Document Name" disabled />
                                                    </div>
                                                    <div class="col-md-5">
                                                        @if ($doc)
                                                            <a href="{{ route('business.download_AdditionalDocuments', $doc->id) }}"
                                                                target="_blank"
                                                                class="custom-label d-flex align-items-center gap-2"
                                                                title="{{ $doc->attachment }}">
                                                                <i class="custom-icon fa fa-download"></i>
                                                                <span>{{ Str::limit(basename($doc->attachment), 20) }}</span>
                                                            </a>
                                                        @endif

                                                        <input type="file" class="form-control custom-input"
                                                            name="attachment[]" accept=".jpg,.jpeg,.png,.pdf" disabled />

                                                    </div>
                                                    <div class="col-md-1 d-flex align-items-center"
                                                        style="padding-top: 12px;">
                                                        <span class="delete-btnUpdate text-danger fs-4"
                                                            style="cursor: pointer;" data-id="{{ $doc->id }}">
                                                            <i class="fa fa-trash"></i>
                                                        </span>
                                                    </div>
                                                </div>
                                            @endforeach
                                        </div>


                                        <div class="divider-line"></div>
                                        <div class="d-flex justify-content-between align-items-center mt-4"
                                            id="next-prev-buttons-3">
                                            <span class="form-label custom-label" style="color: red">
                                                NOTE: Draft applications will be deleted after {{ $settings->value }}
                                                business days.
                                            </span>

                                            <div>
                                                <button class="btn btn-danger js-btn-prev me-2" type="button"
                                                    title="Prev" style="font-family:sans-serif;font-size:14px;">
                                                    Previous
                                                </button>
                                                <button class="btn btn-primary" type="submit" title="Next"
                                                    id="saveDocumentsBtn" style="font-family:sans-serif;font-size:14px;">
                                                    Continue
                                                </button>
                                            </div>
                                        </div>

                                    </div>
                                </form>
                            </div>

                            <div id="confirmation"
                                class="bg-white shadow p-4 rounded multisteps-form__panel {{ session('go_to_confirmations') ? 'js-active' : '' }}"
                                data-animation="scaleIn">
                                <h3 class="text-center multisteps-form__title"
                                    style="font-family: sans-serif;font-size: 18px;color: rgb(0,0,0);font-weight: bold;">
                                    Confirmation</h3>
                                <div class="divider-line"></div>


                                <div id="form-content-1" class="multisteps-form__content">
                                    
                                    <h4 class="text-center multisteps-form__title"
                                        style="font-family: sans-serif;font-size: 15px;color: rgb(0,0,0);font-weight: bold;">
                                        Business Information</h4>
                                    <div class="divider-line"></div>

                                    <div class="row">
                                        @php
                                            $corpType = $business->corporationType->id ?? null;
                                            $regLabel = 'DTI / SEC / CDA Registration Number';

                                            if ($corpType == 1) {
                                                $regLabel = 'DTI Registration Number';
                                            } elseif ($corpType == 2) {
                                                $regLabel = 'SEC Registration Number';
                                            } elseif ($corpType == 4) {
                                                $regLabel = 'CDA Registration Number';
                                            }
                                        @endphp
                                        <div class="col-12 col-sm-3 col-md-3">
                                            <label class="form-label custom-label">Business Type :&nbsp;</label>
                                        </div>
                                        <div class="col-12 col-sm-9 col-md-9">
                                            <span
                                                class="custom-label text-break">{{ $business->corporationType->name ?? 'N/A' }}</span>
                                        </div>
                                        <div class="col-12 col-sm-3 col-md-3">
                                            <label class="form-label custom-label">{{ $regLabel }}:&nbsp;</label>
                                        </div>
                                        <div class="col-12 col-sm-9 col-md-9">
                                            <span class="custom-label text-break">{{ $business->reg_num ?? 'N/A' }}</span>
                                        </div>
                                        <div class="col-12 col-sm-3 col-md-3">
                                            <label class="form-label custom-label">Tax Identification Number
                                                (TIN):&nbsp;</label>
                                        </div>
                                        <div class="col-12 col-sm-9 col-md-9">
                                            <span class="custom-label text-break">{{ $business->tin ?? 'N/A' }}</span>
                                        </div>
                                        <div class="col-12 col-sm-3 col-md-3">
                                            <label class="form-label custom-label">Business Name:&nbsp;</label>
                                        </div>
                                        <div class="col-12 col-sm-9 col-md-9">
                                            <span
                                                class="custom-label text-break">{{ $business->business_name ?? 'N/A' }}</span>
                                        </div>
                                        <div class="col-12 col-sm-3 col-md-3">
                                            <label class="form-label custom-label">Trade Name (if
                                                applicable):&nbsp;</label>
                                        </div>
                                        <div class="col-12 col-sm-9 col-md-9">
                                            <span
                                                class="custom-label text-break">{{ $business->franchise ?? 'N/A' }}</span>
                                        </div>
                                        <div class="col-12 col-sm-3 col-md-3">
                                            <label class="form-label custom-label">Business Category:&nbsp;</label>
                                        </div>
                                        <div class="col-12 col-sm-9 col-md-9">
                                            <span
                                                class="custom-label text-break">{{ $business->category->name ?? 'N/A' }}</span>
                                        </div>
                                        <div class="col-12 col-sm-3 col-md-3">
                                            <label class="form-label custom-label">Description:&nbsp;</label>
                                        </div>
                                        <div class="col-12 col-sm-9 col-md-9">
                                            @if ($business->category_other_description != null)
                                                <label class="form-label custom-label">Business Category :</label>
                                                <span
                                                    class="custom-label text-break">{{ $business->category->name ?? 'N/A' }}</span>
                                            @endif
                                        </div>

                                    </div>
                                </div>

                                <br>
                                <div class="divider-line"></div>
                                <h4 class="text-center multisteps-form__title"
                                    style="font-family: sans-serif;font-size: 15px;color: rgb(0,0,0);font-weight: bold;">
                                    Business URL | Website | Social Media Platform Link</h4>
                                <div class="divider-line"></div>
                                <div class="row">
                                    <div class="col-md-12">
                                        <div class="mb-3">
                                            <!-- <label class="form-label custom-label">Business URL/Website/Social Media
                                                                        Platform Link:&nbsp;</label> -->
                                            @if (!empty($business->url_platform) && is_array($business->url_platform))
                                                @foreach ($business->url_platform as $url)
                                                    @if (!empty($url))
                                                        <a href="{{ $url }}" class="custom-label"
                                                            target="_blank" rel="noopener noreferrer"
                                                            title="{{ $url }}">
                                                            {{ \Illuminate\Support\Str::limit($url, 150) }}
                                                        </a><br>
                                                    @endif
                                                @endforeach
                                            @else
                                                <span class="custom-label">N/A</span>
                                            @endif
                                        </div>
                                    </div>
                                </div>

                                <br>
                                <div class="divider-line"></div>
                                <h4 class="text-center multisteps-form__title"
                                    style="font-family: sans-serif;font-size: 15px;color: rgb(0,0,0);font-weight: bold;">
                                    Authorized Representative</h4>
                                <div class="divider-line"></div>
                                <div class="row">
                                    <div class="col-12 col-sm-3 col-md-3">
                                        <label class="form-label custom-label">Name:&nbsp;</label>
                                    </div>
                                    <div class="col-12 col-sm-9 col-md-9">
                                        <span class="custom-label text-break">{{ $business->pic_name ?? 'N/A' }}</span>
                                    </div>
                                    <div class="col-12 col-sm-3 col-md-3">
                                        <label class="form-label custom-label">Mobile No.:&nbsp;</label>
                                    </div>
                                    <div class="col-12 col-sm-9 col-md-9">
                                        <span class="custom-label text-break">{{ $business->pic_ctc_no ?? 'N/A' }}</span>
                                    </div>
                                    <div class="col-12 col-sm-3 col-md-3">
                                        <label class="form-label custom-label">Email:&nbsp;</label>
                                    </div>
                                    <div class="col-12 col-sm-9 col-md-9">
                                        <span class="custom-label text-break">{{ $business->pic_email ?? 'N/A' }}</span>
                                    </div>
                                    <div class="col-12 col-sm-3 col-md-3">
                                        <label class="form-label custom-label">Government Issued ID:&nbsp;</label>
                                    </div>
                                    <div class="col-12 col-sm-9 col-md-9">
                                        <span
                                            class="custom-label text-break">{{ $business->requirement->description ?? 'N/A' }}</span>
                                    </div>
                                    <div class="col-12 col-sm-3 col-md-3">
                                        <label class="form-label custom-label">Attachment:&nbsp;</label>
                                    </div>
                                    @php
                                        $filePath = !empty($business->requirement_upload)
                                            ? $business->requirement_upload
                                            : (!empty($user->requirement_upload) ? $user->requirement_upload : null);

                                        $fileRoute = !empty($business->requirement_upload)
                                            ? route('business.download_authorized', encrypt($business->id))
                                            : (!empty($user->requirement_upload) ? route('profile.download_authorized', $user->id) : null);

                                        $filename = $filePath ? basename($filePath) : '';
                                    @endphp

                                    @if ($filePath && $fileRoute)
                                        <div class="col-12 col-sm-9 col-md-9">
                                            <span class="custom-label text-break">
                                                <a href="{{ $fileRoute }}" class="d-flex align-items-center gap-2" target="_blank">
                                                    <i class="fa fa-download"></i>
                                                    <span class="custom-label" title="{{ $filename }}">{{ $filename }}</span>
                                                </a>
                                            </span>
                                        </div>
                                    @endif

                                    <div class="col-12 col-sm-3 col-md-3">
                                        <label class="form-label custom-label">Expiry Date:&nbsp;</label>
                                    </div>
                                    <div class="col-12 col-sm-9 col-md-9">
                                        <span
                                            class="custom-label text-break">{{ $business->requirement_expired ? formatDatePH($business->requirement_expired) : 'N/A' }}</span>
                                    </div>


                                </div>

                                <br>
                                <div class="divider-line"></div>
                                <h4 class="text-center multisteps-form__title"
                                    style="font-family: sans-serif;font-size: 15px;color: rgb(0,0,0);font-weight: bold;">
                                    Business Address</h4>
                                <div class="divider-line"></div>

                                <div class="row">
                                    <div class="col-12 col-sm-3 col-md-3">
                                        <label class="form-label custom-label">Complete Address:&nbsp;</label>
                                    </div>
                                    <div class="col-12 col-sm-9 col-md-9">
                                        <span
                                            class="custom-label text-break">{{ $business->complete_address ?? 'N/A' }}</span>
                                    </div>
                                    <div class="col-12 col-sm-3 col-md-3">
                                        <label class="form-label custom-label">Region:&nbsp;</label>
                                    </div>
                                    <div class="col-12 col-sm-9 col-md-9">
                                        <span
                                            class="custom-label text-break">{{ $business->region->reg_region ?? 'N/A' }}</span>
                                    </div>
                                    <div class="col-12 col-sm-3 col-md-3">
                                        <label class="form-label custom-label">Province:&nbsp;</label>
                                    </div>
                                    <div class="col-12 col-sm-9 col-md-9">
                                        <span
                                            class="custom-label text-break">{{ $business->province->prov_desc ?? 'N/A' }}</span>
                                    </div>
                                    <div class="col-12 col-sm-3 col-md-3">
                                        <label class="form-label custom-label">Municipality:&nbsp;</label>
                                    </div>
                                    <div class="col-12 col-sm-9 col-md-9">
                                        <span
                                            class="custom-label text-break">{{ $business->municipality->mun_desc ?? 'N/A' }}</span>
                                    </div>
                                    <div class="col-12 col-sm-3 col-md-3">
                                        <label class="form-label custom-label">Barangay:&nbsp;</label>
                                    </div>
                                    <div class="col-12 col-sm-9 col-md-9">
                                        <span
                                            class="custom-label text-break">{{ $business->barangay->brgy_name ?? 'N/A' }}</span>
                                    </div>

                                </div>

                                <br>
                                <div class="divider-line"></div>
                                <h4 class="text-center multisteps-form__title"
                                    style="font-family: sans-serif;font-size: 15px;color: rgb(0,0,0);font-weight: bold;">
                                    Documents</h4>
                                <div class="divider-line"></div>

                                @php
                                    $documents = [
                                        'Business Registration (SEC/DTI/CDA)' => [
                                            'field' => 'docs_business_reg',
                                            'type' => 'registration',
                                        ],
                                        'Bir 2303' => ['field' => 'docs_bir_2303', 'type' => 'bir'],
                                        'Internal Redress Mechanism' => [
                                            'field' => 'docs_internal_redress',
                                            'type' => 'redress',
                                        ],
                                    ];
                                @endphp

                                @foreach ($documents as $label => $info)
                                    @php $file = $business->{$info['field']}; @endphp
                                    @if (!empty($file))
                                        <div class="row mb-2 align-items-center">
                                            <div class="col-md-3">
                                                <label class="form-label custom-label mb-0">{{ $label }}:</label>
                                            </div>
                                            <div class="col-md-9">
                                                <a href="{{ route('business.download_business_document', ['id' => $business->id, 'type' => $info['type']]) }}"
                                                    class="custom-label d-flex align-items-center gap-2" target="_blank"
                                                    title="{{ basename($file) }}">
                                                    <i class="custom-icon fa fa-download"></i>
                                                    <span>{{ Str::limit(basename($file), 20) }}</span>
                                                </a>
                                            </div>

                                        </div>
                                    @endif
                                @endforeach
                                <div class="row mb-2 align-items-center">
                                    <div class="col-md-3">
                                        <label class="form-label custom-label mb-0">Are you Barangay Micro Business
                                            Enterprise(BMBE) registered?</label>
                                    </div>
                                    <div class="col-md-9">
                                        @php
                                            $hasFile = !empty($business->bmbe_doc);
                                            $filename = basename($business->bmbe_doc);
                                            $shortName =
                                                strlen($filename) > 15 ? substr($filename, 0, 15) . '...' : $filename;
                                        @endphp
                                        @if ($hasFile)
                                            <a href="{{ route('business.download_bmbe_doc', $business->id) }}?v={{ time() }}"
                                                class="d-flex align-items-center gap-2" style="" target="_blank">
                                                <i class="custom-icon fa fa-download"></i>
                                                <span class="custom-label"
                                                    title="{{ $filename }}">{{ $shortName }}</span>
                                            </a>
                                        @endif
                                    </div>
                                </div>
                                <div class="row mb-2 align-items-center">
                                    <div class="col-md-3">
                                        <label class="form-label custom-label mb-0">Business Category (based on Asset Size):</label>
                                    </div>
                                    <div class="col-md-9">
                                    <span class="custom-label text-break">
                                    {{ $businessCatName->busn_category_name ?? '' }}</span>
                                    </div>
                                </div>
                                <div class="row mb-2 align-items-center">
                                    <div class="col-md-3">
                                        <label class="form-label custom-label mb-0">Proof of Total Asset Valuation:</label>
                                    </div>
                                    <div class="col-md-9">
                                        @php
                                            $hasFile = !empty($business->busn_valuation_doc);
                                            $filename = basename($business->busn_valuation_doc);
                                            $shortName =
                                                strlen($filename) > 15 ? substr($filename, 0, 15) . '...' : $filename;
                                        @endphp
                                        @if ($hasFile)
                                            <a href="{{ route('business.download_busn_valuation_doc', $business->id) }}?v={{ time() }}"
                                                class="d-flex align-items-center gap-2" style="" target="_blank">
                                                <i class="custom-icon fa fa-download"></i>
                                                <span class="custom-label"
                                                    title="{{ $filename }}">{{ $shortName }}</span>
                                            </a>
                                        @endif
                                    </div>
                                </div>
                                <br>
                                <div class="divider-line"></div>
                                <h4 class="text-center multisteps-form__title"
                                    style="font-family: sans-serif;font-size: 15px;color: rgb(0,0,0);font-weight: bold;">
                                    Additional Permits</h4>
                                <div class="divider-line"></div>

                                @foreach ($AdditionalDocuments as $doc)
                                    <div class="row mb-2 align-items-center">
                                        <div class="col-md-3">
                                            <label class="form-label custom-label mb-0">{{ $doc->name }}</label>
                                        </div>
                                        <div class="col-md-9">
                                            <a href="{{ route('business.download_AdditionalDocuments', $doc->id) }}"
                                                class="custom-label d-flex align-items-center gap-2" target="_blank"
                                                title="{{ $doc->attachment }}">
                                                <i class="custom-icon fa fa-download"></i>
                                                <span>{{ Str::limit(basename($doc->attachment), 20) }}</span>
                                            </a>
                                        </div>
                                    </div>
                                @endforeach

                                <div class="d-flex justify-content-between align-items-center mt-4"
                                    id="next-prev-buttons-3">
                                    <span class="form-label custom-label" style="color: red">
                                        NOTE: Draft applications will be deleted after {{ $settings->value }} business
                                        days.
                                    </span>

                                    <div class="d-flex align-items-center">
                                        <button class="btn btn-danger js-btn-prev me-2" type="button" title="Previous"
                                            style="font-family:sans-serif;font-size:14px;">
                                            Previous
                                        </button>

                                        <form action="{{ route('business.submit_form') }}" method="POST"
                                            autocomplete="off" id="businessForm">
                                            @csrf
                                            <div id="form-content-1" class="multisteps-form__content">
                                                <input type="hidden" name="business_id" id="business_id"
                                                    value="{{ $business_id ?? '' }}">
                                            </div>
                                            <button class="btn btn-primary" type="submit" id="finalSubmit"
                                                style="font-family:sans-serif;font-size:14px;">
                                                Submit
                                            </button>
                                        </form>
                                    </div>
                                </div>

                            </div>
                        </div>

                    </div>
                </div>
            </div>
        </div>

        <!-- Confirmation Modal -->
        <div class="modal fade" id="confirmSubmitModal" tabindex="-1" aria-labelledby="confirmSubmitModalLabel"
            aria-hidden="true">
            <div class="modal-dialog modal-dialog-centered modal-xl">
                <div class="modal-content">
                    <div class="modal-header">
                        <div class="w-100">
                            <h5 class="modal-title custom-label" id="requirementModalLabel"
                                style="font-family: sans-serif; font-size: 18px; color: rgb(0,0,0); font-weight: bold;">
                                Applicant Undertaking
                            </h5>
                        </div>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body">
                        <p class="custom-label">The applicant undertakes that upon the grant of the E-Commerce
                            Philippine Trustmark (Trustmark), it shall faithfully observe and comply with all applicable
                            laws, rules and regulations governing e-commerce and consumer protection in the Republic of
                            the Philippines.
                        </p>

                        <p class="custom-label">
                            It further commits to uphold the principles of fair trade, transparency, data privacy, and
                            ethical business practices as required by relevant laws, rules and regulations, and to
                            maintain the integrity of the Trustmark at all times.
                        </p>

                        <p class="custom-label">
                            Should there be any changes to the business operations that may affect its continued
                            eligibility for the Trustmark, the applicant shall promptly update its registration and take
                            appropriate steps to remain in full compliance with Philippine trade laws and the issuances
                            set forth by the DTI.
                        </p>

                        <p class="custom-label">
                            This Undertaking is made voluntarily and with full knowledge that any violation may result
                            in the revocation of the Trustmark and other applicable sanctions.
                        </p>

                    </div>
                    <div class="modal-footer d-flex justify-content-between align-items-center">
                        <div class="form-check d-flex align-items-center mb-0">
                            <input type="checkbox" class="form-check-input me-2" id="confirmCheckbox"
                                style="margin-top:0;">
                            <label class="form-check-label custom-label mb-0" for="confirmCheckbox"
                                style="margin-bottom:0;">
                                I confirm the information is correct.
                            </label>
                        </div>

                        <div>
                            <button type="button" class="btn btn-danger" data-bs-dismiss="modal"
                                style="font-family:sans-serif;font-size:14px;">
                                Cancel
                            </button>
                            <button type="button" class="btn btn-primary" id="confirmSubmitBtn" disabled
                                style="font-family:sans-serif;font-size:14px;">
                                Confirm
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    </div>
    <style>
        .section-title {
        font-weight: bold;
        margin-top: 20px;
    }
    .options-row {
        display: flex;
        flex-wrap: wrap;
        gap: 30px;
        margin-top: 5px;
    }
    .option-group {
        display: flex;
        align-items: center;
    }
    .option-group input[type="radio"] {
        margin-right: 5px;
    }
    .option-group input[type="text"] {
        border: none;
        border-bottom: 1px solid #000;
        width: 150px;
        margin-left: 5px;
        outline: none;
    }
    </style>
    <div class="modal fade" id="manageIrmModal" tabindex="-1" aria-labelledby="manageIrmModalLabel" aria-hidden="true" data-bs-keyboard="false" data-bs-backdrop="static">
    <div class="modal-dialog modal-lg">
        <div class="modal-content" style="width: 116% !important;">
        <div class="modal-header">
            <h5 class="modal-title" id="manageIrmModalLabel">Manage Internal Redress Mechanism</h5>
            <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
        </div>
        <div class="modal-body">
        <p><b>1. How to File a Complaint</b></p>
            <p>Customer may file a complaint and submit the Complaint Form(Annex A) through any following official channels(as applicable)</p>
            <form id="manageIrmForm">
            @csrf
                <div class="row">
                    <div class="col-6">
                        <div class="mb-3">
                            <label class="form-label custom-label" style="margin-bottom: 0.2rem !important;">Business Phone No.:<span
                                    class="required-field">*</span></label>
                                    <input type="hidden" name="busn_idIrm"id="busn_idIrm" value="{{ $business->id }}">
                                    <input class="form-control custom-input"
                                        type="text"
                                        name="irm_busn_phone_no"
                                        id="irm_busn_phone_no"
                                        placeholder="Business Phone No."
                                        required
                                        maxlength="25"
                                        oninput="validatePhone(this)">
                                    <small id="phoneError" style="color:red; display:none;">Please enter numbers only.</small>
                        </div>
                    </div>
                    <div class="col-6">
                        <div class="mb-3">
                            <label class="form-label custom-label" style="margin-bottom: 0.2rem !important;">Business Email:<span
                                    class="required-field">*</span></label>
                                    <input class="form-control custom-input"
                                    type="email"
                                    name="irm_busn_email"
                                    id="irm_busn_email"
                                    placeholder="Business Email"
                                    required
                                    oninput="validateEmail(this)">
                                <small id="emailError" style="color:red; display:none;">Please enter a valid email address.</small>
                        </div>
                    </div>
                </div>
                <!--  Social Media Table -->
                <table style=" width: 100%;
                border-collapse: collapse;
                border: 1px solid #000;
                font-family: Arial, sans-serif;" id="socialMediaTable">
                    <thead style="background-color: #0072c6;
                        color: white;
                        text-align: left;
                        padding: 8px;
                        font-weight: bold;
                        font-size: 14px;">
                        <tr>
                        <th style="border-right: 1px solid;padding-left: 5px;">Social Media Page</th>
                        <th class="add-btnSocialPage" style=" width: 40px;text-align: center;cursor: pointer;padding: 5px;">+</th>
                        </tr>
                    </thead>
                    <tbody>
                       <tr>
                            <td style="padding:5px;">
                                <input type="text" name="social_media_page[]" placeholder="Enter page URL or name" class="form-control custom-input">
                            </td>
                            <td style="padding:5px;">
                                <button type="button" class="btn btn-outline-danger btn-sm delete-btnsocialMedia" style="padding: 7px;">
                                <i class="fa-solid fa-trash"></i>
                                </button>
                            </td>
                        </tr>
                    </tbody>
                </table>
                <!--  Online Platform Table -->
                <table style="width:100%; border-collapse:collapse; border:1px solid #000; font-family:Arial, sans-serif; margin-top:20px;" id="OnlinePlatformTable">
                <thead style="background-color:#0072c6; color:white; text-align:left; padding:8px; font-weight:bold; font-size:14px;">
                    <tr>
                    <th style="border-right:1px solid; padding-left:5px;">Online Platform Chat (e.g., Lazada/Shopee)</th>
                    <th class="add-OnlinePlatPage" style="width:40px; text-align:center; cursor:pointer; padding:5px;">+</th>
                    </tr>
                </thead>
                <tbody>
                    <tr>
                    <td style="padding:5px;">
                        <input type="text" name="online_platform[]" placeholder="Enter page URL or name" class="form-control custom-input">
                    </td>
                    <td style="padding:5px;">
                        <button type="button" class="btn btn-outline-danger btn-sm delete-btnOnlinePlat" style="padding: 7px;">
                        <i class="fa-solid fa-trash"></i>
                        </button>
                    </td>
                    </tr>
                </tbody>
                </table>
               <!-- Messaging Platform -->
                <table style="width:100%; border-collapse:collapse; border:1px solid #000; font-family:Arial, sans-serif; margin-top:20px;" id="messagingPlatformTable">
                <thead style="background-color:#0072c6; color:white; text-align:left; padding:8px; font-weight:bold; font-size:14px;">
                    <tr>
                    <th style="border-right:1px solid; padding-left:5px;">Messaging Platform (e.g., WhatsApp/Viber)</th>
                    <th class="add-btn-messaging" style="width:40px; text-align:center; cursor:pointer; padding:5px;">+</th>
                    </tr>
                </thead>
                <tbody>
                    <tr>
                    <td style="padding:5px;"><input type="text" name="messaging_apps[]" placeholder="Enter account or number" class="form-control custom-input"></td>
                    <td style="padding:5px;"><button type="button" class="btn btn-outline-danger btn-sm delete-btnmessaging" style="padding: 7px;"><i class="fa-solid fa-trash"></i></button></td>
                    </tr>
                </tbody>
                </table>
                <div class="form-section">

                    <div class="section-title">2. Acknowledgment of Complaint</div>
                    <p>All complaints will be acknowledged within</p>
                    <div class="options-row">
                        <label class="option-group"><input type="radio" name="complaint_hour" value="1" checked>24-hours</label>
                        <label class="option-group"><input type="radio" name="complaint_hour" value="2">48-hours</label>
                        <label class="option-group"><input type="radio" name="complaint_hour" value="3">72-hours</label>
                        <label class="option-group">
                            <input type="radio" name="complaint_hour" value="4">Others (please specify)
                            <input type="text" name="complaint_others" id="complaint_others">
                        </label>
                    </div>

                    <div class="section-title">3. Investigation and Resolution</div>
                    <p>All complaints will be resolved within</p>
                    <div class="options-row">
                        <label class="option-group"><input type="radio" name="reso_hours" value="1" checked>24-hours</label>
                        <label class="option-group"><input type="radio" name="reso_hours" value="2">3-days</label>
                        <label class="option-group"><input type="radio" name="reso_hours" value="3">7-days</label>
                        <label class="option-group">
                            <input type="radio" name="reso_hours" value="4">Others (please specify)
                            <input type="text" name="reso_others" id="reso_others">
                        </label>
                    </div>

                </div>
                <!-- Resolution -->
                <table style="width:100%; border-collapse:collapse; border:1px solid #000; font-family:Arial, sans-serif; margin-top:20px;" id="websiteTable">
                <thead style="background-color:#0072c6; color:white; text-align:left; padding:8px; font-weight:bold; font-size:14px;">
                    <tr>
                    <th style="border-right:1px solid; padding-left:5px;">Resolution may include, but are not limited to:</th>
                    <th class="add-btn-website" style="width:40px; text-align:center; cursor:pointer; padding:5px;">+</th>
                    </tr>
                </thead>
                <tbody>
                    <tr>
                    <td style="padding:5px;"><input type="text" name="reso_not_limited_to[]" placeholder="Enter  URL" class="form-control custom-input" value="Product Repair"></td>
                    <td style="padding:5px;"><button type="button" class="btn btn-outline-danger btn-sm delete-website" style="padding: 7px;"><i class="fa-solid fa-trash"></i></button></td>
                    </tr>
                    <tr>
                    <td style="padding:5px;"><input type="text" name="reso_not_limited_to[]" placeholder="Enter  URL" class="form-control custom-input" value="Product Replacement"></td>
                    <td style="padding:5px;"><button type="button" class="btn btn-outline-danger btn-sm delete-website" style="padding: 7px;"><i class="fa-solid fa-trash"></i></button></td>
                    </tr>
                    <tr>
                    <td style="padding:5px;"><input type="text" name="reso_not_limited_to[]" placeholder="Enter  URL" class="form-control custom-input" value="Product/Service Refund"></td>
                    <td style="padding:5px;"><button type="button" class="btn btn-outline-danger btn-sm delete-website" style="padding: 7px;"><i class="fa-solid fa-trash"></i></button></td>
                    </tr>
                    <tr>
                    <td style="padding:5px;"><input type="text" name="reso_not_limited_to[]" placeholder="Enter  URL" class="form-control custom-input" value="Clarification of Service Terms"></td>
                    <td style="padding:5px;"><button type="button" class="btn btn-outline-danger btn-sm delete-website" style="padding: 7px;"><i class="fa-solid fa-trash"></i></button></td>
                    </tr>
                    <tr>
                    <td style="padding:5px;"><input type="text" name="reso_not_limited_to[]" placeholder="Enter  URL" class="form-control custom-input" value="Other actions"></td>
                    <td style="padding:5px;"><button type="button" class="btn btn-outline-danger btn-sm delete-website" style="padding: 7px;"><i class="fa-solid fa-trash"></i></button></td>
                    </tr>
                </tbody>
                </table>

                

                <div class="row" style="margin-top: 10px !important;">
                    <div class="col-6 col-sm-6 col-md-6" id="desc-container">
                        <div class="mb-3">
                            <label class="form-label custom-label" style="margin-bottom: 0.2rem !important;">Authorized Business Representative <span
                                    class="required-field">*</span></label>
                            <input class="form-control custom-input" type="text"
                                name="authorized_rep" id="authorized_rep"
                                placeholder="Authorized Business Representative"
                                value="{{$business->pic_name}}">
                        </div>
                    </div>
                    <div class="col-6 col-sm-6 col-md-6" id="desc-container">
                        <div class="mb-3">
                            <label class="form-label custom-label" style="margin-bottom: 0.2rem !important;">Designation <span
                                    class="required-field">*</span></label>
                            <input class="form-control custom-input" type="text"
                                name="authorized_rep_position" id="authorized_rep_position"
                                placeholder="Designation"
                                value="">
                        </div>
                    </div>
                    <div class="col-12 col-sm-6 col-md-12" id="desc-container">
                        <div class="mb-3">
                            <label class="form-label custom-label" style="margin-bottom: 0.2rem !important;">Business Name<span
                                    class="required-field">*</span></label>
                            <input class="form-control custom-input" type="text"
                                name="busn_name" id="busn_name"
                                placeholder="Business Name"
                                value="{{$business->business_name}}">
                        </div>
                    </div>
                </div>
           </form>
        </div>
       <div class="modal-footer">
            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
            @if (!empty($business->id))
            <a id="downloadBtn2" href="{{ empty($business_irm) ? 'javascript:void(0);' : route('business.certificate', $business->id) }}"
            target="_blank"
            class="btn btn-primary {{ empty($business_irm) ? 'disabled' : '' }}"
            style="padding: 8px 10px;padding: 8px 10px;background: #09325d;
                    {{ empty($business_irm) ? 'pointer-events: none; opacity: 0.6;' : '' }}">
            Download
            </a>
            @else
            <button type="button" class="btn btn-secondary" disabled
                    style="padding: 8px 10px;background: #09325d;">
                Download
            </button>
            @endif
            <button type="button" class="btn btn-primary" id="saveManageIrm" style="border: none;background: #09325d;">Save Changes</button>
        </div>
        </div>
    </div>
</div>
    <style>
        button:not(:disabled),
        [type=button]:not(:disabled),
        [type=reset]:not(:disabled),
        [type=submit]:not(:disabled) {
            cursor: pointer;
            border: none;
            font-size: 19px;
        }

        .btn:hover {
            color: none !important;
            background-color: none !important;
            border-color: none !important;
        }
    </style>
    <script>
        $(document).ready(function() {
            $(document).on("click", "#btnManageIRM", function() {
                let businessId = $(this).attr("attr-id");
                $("#manageIrmModal").modal("show");
                $("#manageIrmModal").data("business-id", businessId);
            });
        });
    </script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

    @if (session('success'))
        <script>
            Swal.fire({
                icon: 'success',
                title: 'Success',
                text: '{{ session('success') }}',
                confirmButtonColor: '#3085d6',
                confirmButtonText: 'OK'
            }).then(() => {
                window.location.href = "{{ session('redirect', url()->current()) }}";
            });
        </script>
    @endif
    <script>

        document.addEventListener('DOMContentLoaded', function() {
            const form = document.getElementById('businessForm');
            const finalSubmitBtn = document.getElementById('finalSubmit');
            const confirmCheckbox = document.getElementById('confirmCheckbox');
            const confirmSubmitBtn = document.getElementById('confirmSubmitBtn');

            // Prevent default submit to show modal
            finalSubmitBtn.addEventListener('click', function(e) {
                e.preventDefault();
                // Reset checkbox and disable submit button
                confirmCheckbox.checked = false;
                confirmSubmitBtn.disabled = true;
                // Remove spinner if present
                confirmSubmitBtn.innerHTML = 'Confirm';
                // Show modal
                const confirmModal = new bootstrap.Modal(document.getElementById('confirmSubmitModal'));
                confirmModal.show();
            });

            // Enable/disable confirm button based on checkbox
            confirmCheckbox.addEventListener('change', function() {
                confirmSubmitBtn.disabled = !this.checked;
            });

            // Submit form when confirmation button is clicked, show loading
            confirmSubmitBtn.addEventListener('click', function() {
                confirmSubmitBtn.disabled = true;
                confirmSubmitBtn.innerHTML =
                    `<span class="spinner-border spinner-border-sm me-2" role="status" aria-hidden="true"></span>Submitting...`;
                form.submit();
            });
        });
    </script>
@endsection



<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>

<input type="hidden" id="BASE_URL" value="{{ url('/') }}">

<script>
    const BASE_URL = "{{ url('/') }}"; // Laravel helper sets correct subdirectory


    let provinceSelect;
    let municipalitySelect;
    let barangaySelect;

    document.addEventListener('DOMContentLoaded', function() {
        new TomSelect('#type_id', {
                placeholder: "Select Business Type",
                allowEmptyOption: true,
                create: false,
                sortField: {
                    field: "text",
                    direction: "asc"
                },
                maxOptions: null,
                plugins: ['clear_button']
            });
            new TomSelect('#category', {
                placeholder: "Select Category",
                allowEmptyOption: true,
                create: false,
                sortField: {
                    field: "text",
                    direction: "asc"
                },
                maxOptions: null,
                plugins: ['clear_button']
            });
            new TomSelect('#issued_id', {
                placeholder: "Select Government Issued ID",
                allowEmptyOption: true,
                create: false,
                sortField: {
                    field: "text",
                    direction: "asc"
                },
                maxOptions: null,
                plugins: ['clear_button']
            });
            new TomSelect('#suffix', {
                placeholder: "Select suffix",
                allowEmptyOption: true,
                create: false,
                sortField: {
                    field: "text",
                    direction: "asc"
                },
                maxOptions: null,
                plugins: ['clear_button']
            });
            // new TomSelect('#busn_category_id', {
            //     placeholder: "Select Business Category",
            //     allowEmptyOption: true,
            //     create: false,
            //     sortField: {
            //         field: "text",
            //         direction: "asc"
            //     },
            //     maxOptions: null,
            //     plugins: ['clear_button']
            // });
        new TomSelect('#region');
   
        // Initialize TomSelect for province with scrollable dropdown
        provinceSelect = new TomSelect('#province', {
            placeholder: 'Select Province',
            maxOptions: false, // important! show all
            preload: true,
            loadThrottle: 0,
            plugins: ['clear_button']
        });

        // Initialize TomSelect for municipality with scrollable dropdown
        municipalitySelect = new TomSelect('#municipality', {
            placeholder: 'Select Municipality',
            maxOptions: false, // important! show all
            preload: true,
            loadThrottle: 0,
            plugins: ['clear_button']
        });

        // Initialize TomSelect for municipality with scrollable dropdown
        barangaySelect = new TomSelect('#barangay', {
            placeholder: 'Select Barangay',
            maxOptions: false, // important! show all
            preload: true,
            loadThrottle: 0,
            plugins: ['clear_button']
        });

        // new TomSelect('#category', {
        //     placeholder: "Select Category",
        //     // allowEmptyOption: true
        // });

        // Load provinces when region changes
        document.getElementById('region').addEventListener('change', function() {
            const regionId = this.value;
            provinceSelect.clear();
            provinceSelect.clearOptions();
            provinceSelect.addOption({
                value: '',
                text: 'Select Province'
            });
            provinceSelect.setValue('');

            if (regionId) {
                fetch(BASE_URL + '/get-province/' + regionId)
                    .then(response => response.json())
                    .then(data => {
                        Object.entries(data).forEach(([id, prov_desc]) => {
                            provinceSelect.addOption({
                                value: id,
                                text: prov_desc
                            });
                        });
                        provinceSelect.refreshOptions(false);
                    });
            }
        });

        // Load municipalities when province changes
        document.getElementById('province').addEventListener('change', function() {
            const provinceId = this.value;
            municipalitySelect.clear();
            municipalitySelect.clearOptions();
            municipalitySelect.addOption({
                value: '',
                text: 'Select Municipality'
            });
            municipalitySelect.setValue('');

            if (provinceId) {
                fetch(BASE_URL + '/get-municipalities/' + provinceId)
                    .then(response => response.json())
                    .then(data => {
                        Object.entries(data).forEach(([id, name]) => {
                            municipalitySelect.addOption({
                                value: id,
                                text: name
                            });
                        });
                        municipalitySelect.refreshOptions(false);
                    });
            }
        });

        // Load barangay when region, province, municipality changes
        document.getElementById('municipality').addEventListener('change', function() {
            const regionId = document.getElementById('region').value;
            const provinceId = document.getElementById('province').value;
            const municipalityId = this.value;
            barangaySelect.clear();
            barangaySelect.clearOptions();
            barangaySelect.addOption({
                value: '',
                text: 'Select Barangay'
            });
            barangaySelect.setValue('');

            if (regionId && provinceId && municipalityId) {
                fetch(`${BASE_URL}/get-barangays/${regionId}/${provinceId}/${municipalityId}`)
                    .then(response => response.json())
                    .then(data => {
                        Object.entries(data).forEach(([id, name]) => {
                            barangaySelect.addOption({
                                value: id,
                                text: name
                            });
                        });
                        barangaySelect.refreshOptions(false);
                    });
            }
        });
    });
</script>

<script>
    // grey button for corporation
    // document.addEventListener('DOMContentLoaded', function() {
    //     const saveBtn = document.getElementById('saveAppointmentBtn');
    //     const form = document.querySelector('#corporation form');

    //     const requiredInputs = form.querySelectorAll(
    //         'input[required], select[required], textarea[required]'
    //     );

    //     function validateForm() {
    //         let allFilled = true;

    //         requiredInputs.forEach(input => {
    //             if (input.type === 'file') {
    //                 if (!input.files || input.files.length === 0) {
    //                     allFilled = false;
    //                 }
    //             } else if (input.type === 'checkbox' || input.type === 'radio') {
    //                 if (!input.checked) {
    //                     allFilled = false;
    //                 }
    //             } else {
    //                 if (!input.value || !input.value.trim()) {
    //                     allFilled = false;
    //                 }
    //             }
    //         });

    //         saveBtn.disabled = !allFilled;
    //     }

    //     requiredInputs.forEach(input => {
    //         input.addEventListener('input', validateForm);
    //         input.addEventListener('change', validateForm);
    //     });

    //     // Initial validation
    //     validateForm();
    // });

    // grey button for details
    document.addEventListener('DOMContentLoaded', function() {
        const saveBtn = document.getElementById('saveDetailsBtn');
        const form = document.getElementById('detailsForm');

        const requiredFields = form.querySelectorAll('input[required], textarea[required], select[required]');

        function validateForm() {
            let allValid = true;

            requiredFields.forEach(field => {
                if (!field.value.trim()) {
                    allValid = false;
                }
            });

            saveBtn.disabled = !allValid;
        }

        requiredFields.forEach(field => {
            field.addEventListener('input', validateForm);
            field.addEventListener('change', validateForm); // for dropdowns
        });

        validateForm(); // Check initially
    });

    // grey button for documents
    document.addEventListener('DOMContentLoaded', function() {
        const form = document.getElementById('documentsForm');
        const submitBtn = document.getElementById('saveDocumentsBtn');

        function checkVisibleInputs() {
            // Get all visible inputs with required attribute
            const requiredInputs = form.querySelectorAll('input[required]:not([type=hidden]):not([disabled])');
            let allValid = true;

            requiredInputs.forEach(input => {
                // Only check inputs that are visible (not hidden by CSS)
                if (input.offsetParent !== null) {
                    if (!input.checkValidity()) {
                        allValid = false;
                    }
                }
            });

            submitBtn.disabled = !allValid;
        }

        // Run validation check on input change
        form.addEventListener('input', checkVisibleInputs);
        form.addEventListener('change', checkVisibleInputs);

        // Initial check on page load
        checkVisibleInputs();
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

{{-- for tin format --}}
<script>
    document.addEventListener('DOMContentLoaded', function() {
        const tinInput = document.getElementById('tin_num');
        const tinError = document.getElementById('tin-error');
        let timeout = null;

        tinInput.addEventListener('input', function() {
            // Remove non-digit characters
            let value = tinInput.value.replace(/\D/g, '');
            if (value.length > 14) value = value.slice(0, 14);

            // Format as xxx-xxx-xxx-xxxxx
            let formatted = '';
            if (value.length > 0) formatted += value.slice(0, 3);
            if (value.length > 3) formatted += '-' + value.slice(3, 6);
            if (value.length > 6) formatted += '-' + value.slice(6, 9);
            if (value.length > 9) formatted += '-' + value.slice(9, 14);

            tinInput.value = formatted;

            // Basic format validation
            if (tinInput.value.length < 17) {
                tinError.textContent = 'TIN must be 14 digits (formatted as xxx-xxx-xxx-xxxxx).';
                return;
            } else {
                tinError.textContent = '';
            }

            // Delay AJAX call by 500ms (debounce)
            clearTimeout(timeout);
            // timeout = setTimeout(() => {
            //     const csrfToken = document.querySelector('meta[name="csrf-token"]').getAttribute('content');
            //     const businessId = document.querySelector('input[name="business_id"]')?.value || '';

            //     fetch('{{ route('check.tin') }}', {
            //         method: 'POST',
            //         headers: {
            //             'Content-Type': 'application/json',
            //             'X-CSRF-TOKEN': csrfToken
            //         },
            //         body: JSON.stringify({
            //             tin: tinInput.value,
            //             business_id: businessId
            //         })
            //     })
            //     .then(response => response.json())
            //     .then(data => {
            //         if (data.exists) {
            //             tinError.textContent = 'This TIN is already registered.';
            //         } else {
            //             tinError.textContent = '';
            //         }
            //     })
            //     .catch(err => {
            //         tinError.textContent = 'Error checking TIN.';
            //         console.error(err);
            //     });
            // }, 500);
        });
    });
</script>



{{-- save prefix for mobile no --}}
<script>
    document.addEventListener('DOMContentLoaded', () => {
        // Helper to get selected value
        function getSelectedValue(selectId) {
            const el = document.getElementById(selectId);
            return el ? el.value : '';
        }

        // Initialize TomSelect for all dropdowns
        const regionSelect = new TomSelect('#region', {
            placeholder: 'Select Region'
        });
        provinceSelect = new TomSelect('#province', {
            placeholder: 'Select Province'
        });
        municipalitySelect = new TomSelect('#municipality', {
            placeholder: 'Select Municipality'
        });
        barangaySelect = new TomSelect('#barangay', {
            placeholder: 'Select Barangay'
        });

        // Load provinces when region changes
        document.getElementById('region').addEventListener('change', function() {
            const regionId = this.value;
            provinceSelect.clear();
            provinceSelect.clearOptions();
            provinceSelect.addOption({
                value: '',
                text: 'Select Province'
            });
            provinceSelect.setValue('');
            municipalitySelect.clearOptions();
            municipalitySelect.addOption({
                value: '',
                text: 'Select Municipality'
            });
            municipalitySelect.setValue('');
            barangaySelect.clearOptions();
            barangaySelect.addOption({
                value: '',
                text: 'Select Barangay'
            });
            barangaySelect.setValue('');
            if (regionId) {
                fetch(BASE_URL + '/get-province/' + regionId)
                    .then(response => response.json())
                    .then(data => {
                        Object.entries(data).forEach(([id, prov_desc]) => {
                            provinceSelect.addOption({
                                value: id,
                                text: prov_desc
                            });
                        });
                        provinceSelect.refreshOptions(false);
                    });
            }
        });

        // Load municipalities when province changes
        document.getElementById('province').addEventListener('change', function() {
            const provinceId = this.value;
            municipalitySelect.clearOptions();
            municipalitySelect.addOption({
                value: '',
                text: 'Select Municipality'
            });
            municipalitySelect.setValue('');
            barangaySelect.clearOptions();
            barangaySelect.addOption({
                value: '',
                text: 'Select Barangay'
            });
            barangaySelect.setValue('');
            if (provinceId) {
                fetch(BASE_URL + '/get-municipalities/' + provinceId)
                    .then(response => response.json())
                    .then(data => {
                        Object.entries(data).forEach(([id, name]) => {
                            municipalitySelect.addOption({
                                value: id,
                                text: name
                            });
                        });
                        municipalitySelect.refreshOptions(false);
                    });
            }
        });

        // Load barangays when municipality changes
        document.getElementById('municipality').addEventListener('change', function() {
            const regionId = getSelectedValue('region');
            const provinceId = getSelectedValue('province');
            const municipalityId = this.value;
            barangaySelect.clear();
            barangaySelect.clearOptions();
            barangaySelect.addOption({
                value: '',
                text: 'Select Barangay'
            });
            barangaySelect.setValue('');
            if (regionId && provinceId && municipalityId) {
                fetch(`${BASE_URL}/get-barangays/${regionId}/${provinceId}/${municipalityId}`)
                    .then(response => response.json())
                    .then(data => {
                        Object.entries(data).forEach(([id, name]) => {
                            barangaySelect.addOption({
                                value: id,
                                text: name
                            });
                        });
                        barangaySelect.refreshOptions(false);
                    });
            }
        });

        // Set initial values if present
        const initialProvince = document.getElementById('province').getAttribute('data-selected');
        const initialMunicipality = document.getElementById('municipality').getAttribute('data-selected');
        const initialBarangay = document.getElementById('barangay').getAttribute('data-selected');
        if (initialProvince) provinceSelect.setValue(initialProvince, true);
        if (initialMunicipality) municipalitySelect.setValue(initialMunicipality, true);
        if (initialBarangay) barangaySelect.setValue(initialBarangay, true);
    });

    function updateLabel(typeId) {
        if (typeId === '1') {
            regLabel.innerHTML = 'DTI Registration Number: <span class="required-field">*</span>';
        } else if (typeId === '2') {
            regLabel.innerHTML = 'SEC Registration Number: <span class="required-field">*</span>';
        } else if (typeId === '4') {
            regLabel.innerHTML = 'CDA Registration Number: <span class="required-field">*</span>';
        } else {
            regLabel.innerHTML = 'Registration Number: <span class="required-field">*</span>';
        }
    }

    // Update on page load
    if (typeSelect.value) {
        updateLabel(typeSelect.value);
    }

    // Update on dropdown change
    typeSelect.addEventListener('change', function() {
    updateLabel(this.value);
    });
    });
</script>

{{-- Checking file upload --}}
<script>
    document.addEventListener('DOMContentLoaded', function() {
        const allowedTypes = ['image/jpeg', 'image/png', 'application/pdf'];
        const maxSize = 10 * 1024 * 1024; // 10MB in bytes

        const fileInputs = ['business_reg', 'bir_2303', 'internal_redress','bmbe_doc', 'busn_valuation_doc'];

        fileInputs.forEach(function(inputId) {
            const input = document.getElementById(inputId);
            const errorDisplay = document.getElementById(`error-${inputId}`);

            input.addEventListener('change', function() {
                const file = this.files[0];
                errorDisplay.style.display = 'none';

                if (!file) return;

                if (!allowedTypes.includes(file.type)) {
                    errorDisplay.textContent =
                        'Only JPG, JPEG, PNG, and PDF files are allowed.';
                    errorDisplay.style.display = 'block';
                    this.value = '';
                    return;
                }

                if (file.size > maxSize) {
                    errorDisplay.textContent = 'File size must not exceed 10MB.';
                    errorDisplay.style.display = 'block';
                    this.value = '';
                    return;
                }

                errorDisplay.style.display = 'none'; // valid
            });
        });
    });
</script>

{{-- script for url --}}
<script>
    // Parse existing URLs from backend (passed as JSON encoded)
    let existingUrls = @json(old('url_platform_json', $business->url_platform ?? []));

    // If old input isn't JSON string but array, ensure it's array:
    if (!Array.isArray(existingUrls)) {
        existingUrls = [];
    }

    let urlFieldCount = 0;

    function validateUrl(field) {
        const value = field.value.trim();
        const errorDiv = document.getElementById(`${field.id}-error`);
        if (!value) {
            errorDiv.textContent = '';
            return true;
        }
        const urlPattern = /^(https?:\/\/)[^\s/$.?#].[^\s]*$/i;
        if (!urlPattern.test(value)) {
            errorDiv.textContent = 'Please enter a valid URL starting with http:// or https://';
            return false;
        } else {
            errorDiv.textContent = '';
            return true;
        }
    }

    function createUrlField(url = '') {
        urlFieldCount++;

        const container = document.getElementById("url-inputs-container");

        const fieldWrapper = document.createElement("div");
        fieldWrapper.className = "row align-items-center mb-2";
        fieldWrapper.style.borderBottom = "1px solid #ccc";
        fieldWrapper.style.marginLeft = "3px";
        fieldWrapper.style.marginRight = "2px";
        fieldWrapper.id = `url_field_wrapper_${urlFieldCount}`;
        const inputCol = document.createElement("div");
        inputCol.className = "col-12 col-sm-7 mb-2 mb-sm-0";
        inputCol.style.padding = "0px";
        const input = document.createElement("input");
        input.type = "text";
        input.name = `url_platform_${urlFieldCount}`;
        input.id = `url_platform_${urlFieldCount}`;
        input.placeholder = "Enter platform URL";
        input.className = "form-control custom-input";
        input.value = url;
        input.required = urlFieldCount === 1;

        input.addEventListener('blur', () => fetchPlatformDetails(input)); // onLostFocus
        input.addEventListener('input', () => validateUrl(input));
        if (input.value.trim() !== '') {
            fetchPlatformDetails(input);
        }
        inputCol.appendChild(input);
        const errorDiv = document.createElement("div");
        errorDiv.id = `${input.id}-error`;
        errorDiv.className = "text-danger small mt-1";
        inputCol.appendChild(errorDiv);
        const platformCol = document.createElement("div");
        platformCol.className = "col-6 col-sm-2";
        platformCol.style.textAlign = "center";
        platformCol.innerHTML = `
            <strong class="d-sm-none">Platform:</strong>
            <span id="platform_name_${urlFieldCount}" class="ms-sm-1" style="font-size: 12px;color: #000;"></span>
        `;
        const irmCol = document.createElement("div");
        irmCol.className = "col-6 col-sm-1";
        irmCol.innerHTML = `
            <strong class="d-sm-none">With IRM:</strong>
            <span id="with_irm_${urlFieldCount}" class="ms-sm-1" style="font-size: 12px;color: #000;padding-left: 24px;"></span>
        `;
        const btnCol = document.createElement("div");
        btnCol.className = "col-12 col-sm-2 d-flex justify-content-center";
        btnCol.style.paddingLeft = "41px";
        const visitBtn = document.createElement("button");
        visitBtn.type = "button";
        visitBtn.className = "btn btn-primary btn-sm me-2";
        visitBtn.innerHTML = `<i class="fa fa-globe text-white"></i>`;
        visitBtn.onclick = () => visitUrl(input.id);

        const deleteBtn = document.createElement("button");
        deleteBtn.type = "button";
        deleteBtn.className = "btn btn-outline-danger btn-sm";
        deleteBtn.innerHTML = `<i class="fa fa-trash"></i>`;
        deleteBtn.onclick = () => {
            fieldWrapper.remove();
            updateRequiredField(); 
        };

        btnCol.appendChild(visitBtn);
        btnCol.appendChild(deleteBtn);

        fieldWrapper.appendChild(inputCol);
        fieldWrapper.appendChild(platformCol);
        fieldWrapper.appendChild(irmCol);
        fieldWrapper.appendChild(btnCol);

        container.appendChild(fieldWrapper);
    }
    function fetchPlatformDetails(input) {
        let url = input.value.trim();
        if (!url) return;
        if (!/^https?:\/\//i.test(url)) {
            url = "https://" + url; 
        }
        try {
            const parsed = new URL(url);
            url = parsed.hostname.toLowerCase().replace(/^www\./, ""); // remove "www."
        } catch (e) {
            console.warn("Invalid URL format:", url);
            return;
        }
        $.ajax({
            url: "{{ route('platform.details') }}", 
            type: "POST",
            data: {
                base_url: url,
                _token: $('meta[name="csrf-token"]').attr("content"),
            },
            beforeSend: function () {
                const id = input.id.split("_").pop();
                $(`#platform_name_${id}`).text("Checking...");
                $(`#with_irm_${id}`).text("");
            },
            success: function (response) {
                const id = input.id.split("_").pop();
                $(`#platform_name_${id}`).text(response.platform_name || "");
                $(`#with_irm_${id}`).text(response.with_irm || "");
            },
            error: function (xhr) {
                console.error("Error fetching platform details:", xhr.responseText);
            },
        });
    }



    function addUrlField() {
        createUrlField('');
    }

    function visitUrl(inputId) {
        const input = document.getElementById(inputId);
        const url = input.value.trim();

        if (!url) {
            alert("Please enter a URL.");
            return;
        }

        if (!validateUrl(input)) {
            alert("Please enter a valid URL starting with http:// or https://");
            return;
        }

        window.open(url, '_blank');
    }

    function prepareUrlsForSubmit() {
        let urls = [];
        let valid = true;

        for (let i = 1; i <= urlFieldCount; i++) {
            const input = document.getElementById(`url_platform_${i}`);
            if (input) {
                if (!validateUrl(input)) {
                    valid = false;
                }
                const val = input.value.trim();
                if (val) {
                    urls.push(val);
                }
            }
        }

        if (!valid) {
            alert("Please fix the URL errors before submitting.");
            return false;
        }

        if (urls.length === 0) {
            alert("Please enter at least one URL.");
            return false;
        }

        document.getElementById('url_platform_json').value = JSON.stringify(urls);

        // Remove name attributes to prevent duplicate data
        for (let i = 1; i <= urlFieldCount; i++) {
            const input = document.getElementById(`url_platform_${i}`);
            if (input) {
                input.removeAttribute('name');
            }
        }

        return true;
    }

    // On page load, render all existing URLs as inputs
    window.onload = function() {
        if (existingUrls.length === 0) {
            // no existing URL, create one empty field
            createUrlField('');
        } else {
            existingUrls.forEach(url => {
                createUrlField(url);
            });
        }
    };
</script>

{{-- check duplicate url --}}
<script>
    function prepareUrlsForSubmit() {
        const urlInputs = document.querySelectorAll('#url-inputs-container input[type="text"]');
        const urls = [];
        let hasDuplicate = false;
        let urlSet = new Set();
        urlInputs.forEach(input => {
            const url = input.value.trim();
            if (url) {
                if (urlSet.has(url)) {
                    hasDuplicate = true;
                }
                urlSet.add(url);
                urls.push(url);
            }
        });
        if (hasDuplicate) {
            alert('Duplicate URLs are not allowed. Please remove duplicates.');
            return false;
        }
        document.getElementById('url_platform_json').value = JSON.stringify(urls);
        return true;
    }
</script>

{{-- category description --}}
<script>
    document.addEventListener('DOMContentLoaded', function() {
        const categorySelect = document.getElementById('category');
        const otherCategoryInput = document.getElementById('other_category');

        function updateOtherCategoryField() {
            const selectedOption = categorySelect.options[categorySelect.selectedIndex];
            const isOthers = selectedOption.getAttribute('data-is-others');
            if (isOthers === '1') {
                otherCategoryInput.required = true;
                otherCategoryInput.readOnly = false;
                otherCategoryInput.classList.remove('readonly');
            } else {
                otherCategoryInput.required = false;
                otherCategoryInput.readOnly = true;
                otherCategoryInput.value = '';
                otherCategoryInput.classList.add('readonly');
            }
        }
        categorySelect.addEventListener('change', updateOtherCategoryField);
        updateOtherCategoryField();
    });
</script>

{{-- search dropdown for category --}}
<script>
    document.addEventListener('DOMContentLoaded', function() {
        if (window.TomSelect) {
            new TomSelect('#category', {
                placeholder: "Select Category",
                allowEmptyOption: true,
                create: false,
                sortField: {
                    field: "text",
                    direction: "asc"
                },
                maxOptions: null,
                plugins: ['clear_button']
            });
        }
    });
</script>

{{-- government issued id expiration date --}}
<script>
    $(document).ready(function() {
        const $issuedId = $('#issued_id');
        const $expirationVisible = $('#expirationDateInputVisible');
        const $expirationHidden = $('#expirationDateInput');

        function updateExpirationField() {
            const selected = $issuedId.find(':selected');
            const withExpiration = selected.attr('data-with-expiration');

            if (withExpiration === '1') {
                $expirationVisible.prop('readonly', false).trigger('input'); // Trigger validation
                if (!$expirationVisible.val()) {
                    $expirationVisible.val('');
                    $expirationHidden.val('');
                }
            } else {
                $expirationVisible.prop('readonly', true);
                $expirationVisible.val('');
                $expirationHidden.val('');
            }

            if (typeof validateCompleteForm === 'function') {
                validateCompleteForm(); // Make sure to re-check after update
            }
        }

        $issuedId.on('change', function() {
            updateExpirationField();
        });

        $expirationVisible.on('input', function() {
            $expirationHidden.val(this.value);
        });

        updateExpirationField(); // Initial setup
    });
</script>


<style>
    input[readonly] {
        background-color: #e9ecef;
        pointer-events: none;
    }

    #document {
        display: block;
        height: 1000px;
        overflow-y: auto;
        overflow-x: hidden;
    }

    #corporation {
        max-height: 75vh;
        overflow-y: auto;
    }

    html,
    body {
        margin: 0;
        overflow-y: auto;
        overflow-x: hidden;
    }
</style>
<script>
    $(document).ready(function() {
        function getNewRow() {
            return `
            <div class="row document-row mb-2">
                <div class="col-md-6">
                    <input type="text" class="form-control custom-input doc-name" name="document_name[]" placeholder="Document Name" />
                </div>
                <div class="col-md-5">
                    <input type="file" class="form-control custom-input doc-file" name="attachment[]" accept=".jpg,.jpeg,.png,.pdf"/>
                </div>
                <div class="col-md-1 d-flex align-items-center">
                    <span class="delete-btn text-danger fs-4" style="cursor: pointer;"><i class="fa fa-trash"></i></span>
                </div>
            </div>
        `;
        }
        $('#addDocumentBtn').on('click', function() {
            if (hasIncompleteRow()) {
                alert("Please complete existing document rows before adding a new one.");
                return;
            }
            $('#document-container').append(getNewRow());
            checkButtonState();
        });
        $('#addDocumentBtn').click();

        function hasIncompleteRow() {
            let incomplete = false;
            $('.document-row').each(function() {
                let docNameVal = ($(this).find('.doc-name').val() || "").trim();
                let docFileVal = ($(this).find('.doc-file').val() || "").trim();

                if ((docNameVal && !docFileVal) || (docFileVal && !docNameVal)) {
                    incomplete = true;
                    return false;
                }
            });
            return incomplete;
        }

        function checkButtonState() {
            $('#saveDocumentsBtn').prop('disabled', hasIncompleteRow());
        }
        $(document).on('input change', '.doc-name, .doc-file', checkButtonState);
        $(document).on('click', '.delete-btn', function() {
            $(this).closest('.document-row').remove();
            checkButtonState();
        });
    });
</script>


<script>
    $(document).ready(function() {
        $('.delete-btnUpdate').click(function() {
            const $this = $(this);
            const docId = $this.data('id');

            if (!docId) {
                Swal.fire("Error", "Document ID not found", "error");
                return;
            }

            Swal.fire({
                title: 'Are you sure?',
                text: "You won't be able to revert this!",
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#3085d6',
                cancelButtonColor: '#d33',
                confirmButtonText: 'Yes, delete it!'
            }).then((result) => {
                if (result.isConfirmed) {
                    $.ajax({
                        url: BASE_URL + '/documents/' + docId,
                        type: 'DELETE',
                        data: {
                            _token: '{{ csrf_token() }}'
                        },
                        success: function(response) {
                            $this.closest('.document-row').remove();

                            Swal.fire(
                                'Deleted!',
                                'Document has been deleted.',
                                'success'
                            );
                        },
                        error: function(xhr) {
                            Swal.fire(
                                'Error!',
                                'Something went wrong while deleting.',
                                'error'
                            );
                        }
                    });
                }
            });
        });
    });
</script>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        if (window.TomSelect) {
            new TomSelect('#region', {
                placeholder: 'Select Region'
            });
            new TomSelect('#province', {
                placeholder: 'Select Province'
            });
            new TomSelect('#municipality', {
                placeholder: 'Select Municipality'
            });
            new TomSelect('#barangay', {
                placeholder: 'Select Barangay'
            });
        }
    });
</script>
<script>
$(document).ready(function () {
    const $docField = $("#bmbe_doc");                
    const $busn_category_id = $("#busn_category_id"); 
    const $busn_valuation_doc = $("#busn_valuation_doc"); 
    const $saveBtn = $("#saveDocumentsBtn");
    const currentValue = $busn_category_id.val();
    const hasExistingFile = @json($hasFilebmbe_doc); 

    function toggleBmbeDoc() {
        const selected = $("input[name='is_bmbe']:checked").val();

        if (selected == "1") {
            // Enable all fields
            $docField.prop("disabled", false).removeAttr("readonly");
            $busn_valuation_doc.prop("disabled", true)
                               .prop("required", false)
                               .attr("readonly", true)
                               .val("");

            $busn_category_id.prop("disabled", true)
                             .prop("required", false)
                             .attr("readonly", true)
                             .val("");
            

            // Set required only if no existing file
            if (!hasExistingFile) {
                $docField.prop("required", true);
            } else {
                $docField.prop("required", false);
            }

            toggleSaveBtn();
        } else {
            // Disable and clear all fields when BMBE = No
            $docField.prop("disabled", true)
                     .prop("required", false)
                     .attr("readonly", true)
                     .val("");

            $busn_valuation_doc.prop("disabled", false).prop("required", true).removeAttr("readonly");
            $busn_category_id.prop("disabled", false).prop("required", true).removeAttr("readonly");
            $busn_category_id.val(currentValue);
            // $saveBtn.prop("disabled", false);
        }
    }

    function toggleSaveBtn() {
        const selected = $("input[name='is_bmbe']:checked").val();

        // Only check when is_bmbe == 1 and no existing file
        if (selected == "1" && !hasExistingFile) {
            const docEmpty = !$docField[0].files.length;
            const valEmpty = !$busn_valuation_doc[0].files.length;
            const categoryEmpty = !$busn_category_id.val();

            if (docEmpty || valEmpty || categoryEmpty) {
                $saveBtn.prop("disabled", true);
                return;
            }
        }
        $saveBtn.prop("disabled", false);
    }
    toggleBmbeDoc();
    $("input[name='is_bmbe']").on("change", function () {
        toggleBmbeDoc();
    });
    $docField.on("change", toggleSaveBtn);
    $busn_valuation_doc.on("change", toggleSaveBtn);
    $busn_category_id.on("input change", toggleSaveBtn);
});
</script>
<script>
function updateFullNumber(input) {
    input.value = input.value.replace(/[^0-9]/g, '');
    document.getElementById('full_number').value = '+63' + input.value;
}
</script>
<!-- ✅ Scripts -->
<script>
    document.addEventListener('DOMContentLoaded', function() {
            function setupDynamicTable(tableId, addBtnClass, deleteBtnClass, inputName) {
            const table = document.querySelector(`#${tableId}`);
            const tbody = table.querySelector('tbody');
            const addBtn = table.querySelector(`.${addBtnClass}`);

            // Add new row
            addBtn.addEventListener('click', () => {
                const newRow = document.createElement('tr');

                const inputCell = document.createElement('td');
                inputCell.style.padding = "5px";
                inputCell.innerHTML = `<input type="text" name="${inputName}[]" placeholder="Enter page URL or name" class="form-control custom-input">`;

                const deleteCell = document.createElement('td');
                deleteCell.style.padding = "5px";
                deleteCell.innerHTML = `<button type="button" class="btn btn-outline-danger btn-sm ${deleteBtnClass}" title="Delete" style="padding: 7px;"><i class="fa-solid fa-trash"></i></button>`;

                newRow.appendChild(inputCell);
                newRow.appendChild(deleteCell);
                tbody.appendChild(newRow);
            });

            // Delete handler (event delegation)
            tbody.addEventListener('click', function(event) {
                const btn = event.target.closest(`.${deleteBtnClass}`);
                if (!btn) return;
                btn.closest('tr').remove();
            });
            }
            setupDynamicTable('socialMediaTable', 'add-btnSocialPage', 'delete-btnsocialMedia', 'social_media_page');
            setupDynamicTable('OnlinePlatformTable', 'add-OnlinePlatPage', 'delete-btnOnlinePlat', 'online_platform');
            setupDynamicTable('messagingPlatformTable', 'add-btn-messaging', 'delete-btnmessaging', 'messaging_apps');
            setupDynamicTable('websiteTable', 'add-btn-website', 'delete-website', 'reso_not_limited_to');
            // --- Submit all form data
            document.getElementById('saveManageIrm').addEventListener('click', function() {
                const form = document.getElementById('manageIrmForm');
                const requiredFields = form.querySelectorAll('[required]');
                let isValid = true;
                let firstInvalidField = null;
                requiredFields.forEach(field => {
                    if (!field.value.trim()) {
                        isValid = false;
                        field.classList.add('is-invalid');
                        if (!firstInvalidField) firstInvalidField = field;
                    } else {
                        field.classList.remove('is-invalid');
                    }
                });

                if (!isValid) {
                    if (firstInvalidField) firstInvalidField.focus(); 
                    return;
                }
                const formData = new FormData(form);
                const obj = {};

                formData.forEach((value, key) => {
                    if (key.endsWith('[]')) {
                        const cleanKey = key.replace('[]', '');
                        if (!obj[cleanKey]) obj[cleanKey] = [];
                        if (value.trim() !== '') obj[cleanKey].push(value);
                    } else {
                        obj[key] = value;
                    }
                });

                fetch("{{ route('irm.save') }}", {
                    method: "POST",
                    headers: {
                        "Content-Type": "application/json",
                        "X-CSRF-TOKEN": "{{ csrf_token() }}"
                    },
                    body: JSON.stringify(obj)
                })
                .then(res => res.json())
                .then(data => {
                    if (data.success) {
                        Swal.fire({
                            title: "Success!",
                            text: "Data saved successfully!",
                            icon: "success",
                            confirmButtonText: "OK",
                            timer: 2000,
                            timerProgressBar: true
                        }).then(() => {
                            const downloadBtn = document.getElementById('downloadBtn');
                            const downloadBtn2 = document.getElementById('downloadBtn2');

                            @if(!empty($business->id))
                                const certificateUrl = "{{ route('business.certificate', $business->id) }}";
                            @else
                                const certificateUrl = null;
                            @endif

                            if (downloadBtn && certificateUrl) {
                                downloadBtn.classList.remove('disabled');
                                downloadBtn.style.pointerEvents = 'auto';
                                downloadBtn.style.opacity = '1';
                                downloadBtn.href = certificateUrl;
                            }

                            if (downloadBtn2 && certificateUrl) {
                                downloadBtn2.classList.remove('disabled');
                                downloadBtn2.style.pointerEvents = 'auto';
                                downloadBtn2.style.opacity = '1';
                                downloadBtn2.href = certificateUrl;
                            }
                        });
                    } else {
                        Swal.fire({
                            title: "Error",
                            text: data.message || "Something went wrong while saving.",
                            icon: "error",
                            confirmButtonText: "OK"
                        });
                    }
                })
                .catch(err => console.error(err));
            });

    });
</script>
<script>
function validatePhone(input) {
    const errorMsg = document.getElementById('phoneError');
    input.value = input.value.replace(/[^0-9]/g, ''); // remove non-digits
    if (input.value === '') {
        errorMsg.style.display = 'none';
        input.style.borderColor = '';
    } else if (!/^[0-9]+$/.test(input.value)) {
        errorMsg.style.display = 'block';
        input.style.borderColor = 'red';
    } else {
        errorMsg.style.display = 'none';
        input.style.borderColor = 'green';
    }
}

function validateEmail(input) {
    const email = input.value.trim();
    const errorMsg = document.getElementById('emailError');
    const pattern = /^[a-zA-Z0-9._%+-]+@[a-zA-Z0-9.-]+\.[a-zA-Z]{2,}$/;

    if (email === '') {
        errorMsg.style.display = 'none';
        input.style.borderColor = '';
    } else if (!pattern.test(email)) {
        errorMsg.style.display = 'block';
        input.style.borderColor = 'red';
    } else {
        errorMsg.style.display = 'none';
        input.style.borderColor = 'green';
    }
}
</script>