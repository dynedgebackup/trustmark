@extends('layouts.app')

@section('content')
    <div class="d-flex justify-content-between align-items-center mb-4" style="margin-top: 15px;">
        <h3 class="page-title">EDIT BUSINESS</h3>
    </div>

    <ol class="breadcrumb custom-breadcrumb">
        <li class="breadcrumb-item"><a href="{{ route('dashboard') }}"><span>Dashboard</span></a></li>
        <li class="breadcrumb-item"><a href="{{ route('business.index') }}t"><span>Business List</span></a></li>
        <li class="breadcrumb-item"><a href="#"><span>Edit</span></a></li>
    </ol>

    <div id="wrapper">
        <div class="d-flex flex-column" id="content-wrapper">

            <form action="{{ route('business.update', $business->id) }}" method="POST" enctype="multipart/form-data"
                autocomplete="off" onsubmit="return prepareUrlsForSubmit()" id="businessForm">
                @csrf
                @method('PUT')
                <div class="card">
                    <div class="card-body">

                        <ul class="nav nav-tabs custom-tab-list" role="tablist">
                            <li class="nav-item" role="presentation">
                                <a class="nav-link active custom-tab-link" role="tab" data-bs-toggle="tab"
                                    href="#tab-remarks">Remark</a>
                            </li>
                            <li class="nav-item" role="presentation">
                                <a class="nav-link custom-tab-link" role="tab" data-bs-toggle="tab"
                                    href="#tab-corporations">Business Registration</a>
                            </li>
                            <li class="nav-item" role="presentation">
                                <a class="nav-link custom-tab-link" role="tab" data-bs-toggle="tab"
                                    href="#tab-detail">Business Address</a>
                            </li>
                            <li class="nav-item" role="presentation">
                                <a class="nav-link custom-tab-link" role="tab" data-bs-toggle="tab"
                                    href="#tab-document">Documents</a>
                                {{-- </li>
                            <li class="nav-item" role="presentation">
                                <a class="nav-link custom-tab-link" role="tab" data-bs-toggle="tab"
                                    href="#tab-payment">Payment</a>
                            </li> --}}

                        </ul>


                        <div class="tab-content">
                            <div class="tab-pane active" role="tabpanel" id="tab-remarks">
                                <div class="row">
                                    <div class="col-md-12">
                                        <div class="mb-3">
                                            <label class="form-label">Remarks :&nbsp;</label>
                                            <textarea class="form-control custom-input" 
                                                name="admin_remarks" 
                                                id="admin_remarks" 
                                                placeholder="remarks" 
                                                 
                                                rows="3">{{ $business->admin_remarks }}</textarea>
                                            <!-- <span>{{ $business->admin_remarks ?? 'N/A' }}</span> -->
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <div class="tab-pane" role="tabpanel" id="tab-corporations">
                                <div id="form-content-1" class="multisteps-form__content">
                                    <input type="hidden" name="business_id" id="business_id"
                                        value="{{ $business_id ?? '' }}">

                                    <div class="row">
                                        <div class="col-12">
                                            <div class="mb-3">
                                                <label class="form-label custom-label">Business Type:<span
                                                        class="required-field">*</span></label>
                                                <select class="form-select custom-input" name="type_id" id="type_id"
                                                    required>
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
                                        <div class="col-md-6">
                                            <div class="mb-3">
                                                <label class="form-label custom-label" id="reg-label">
                                                    Registration Number: <span class="required-field">*</span>
                                                </label>
                                                <input class="form-control custom-input" type="text" name="reg_num"
                                                    id="reg_num" placeholder="Registration Number" required
                                                    value="{{ $business->reg_num }}" maxlength="25">
                                            </div>
                                        </div>
                                        <div class="col-md-6">
                                            <div class="mb-3">
                                                <label class="form-label custom-label">Tax Identification Number
                                                    (TIN): <span class="required-field">*</span></label>
                                                <input class="form-control custom-input" type="text" name="tin_num"
                                                    id="tin_num" placeholder="Tax Identification Number (TIN)" required
                                                    maxlength="17" value="{{ $business->tin }}">
                                                <div id="tin-error" class="text-danger mt-1" style="font-size: 13px;">
                                                </div>
                                            </div>
                                        </div>
                                    </div>

                                    <div class="row">
                                        <div class="col-md-6">
                                            <div class="mb-3">
                                                <label class="form-label custom-label">Business Name: <span
                                                        class="required-field">*</span></label>
                                                <input class="form-control custom-input" type="text"
                                                    name="business_name" id="business_name" placeholder="Business Name"
                                                    required value="{{ $business->business_name }}">
                                            </div>
                                        </div>
                                        <div class="col-md-6">
                                            <div class="mb-3">
                                                <label class="form-label custom-label">Trade Name
                                                    (if applicable):</label>
                                                <input class="form-control custom-input" type="text" name="franchise"
                                                    id="form" placeholder="Trade Name"
                                                    value="{{ $business->franchise }}">
                                            </div>
                                        </div>
                                    </div>
                                    <div class="row">
                                        <div class="col-12 col-sm-6 col-md-12">
                                            <div class="mb-3">
                                                <label class="form-label custom-label">Business Category: <span
                                                        class="required-field">*</span></label>
                                                <select class="form-select custom-select" id="category" name="category"
                                                    required>
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
                                    <h3 class="text-center multisteps-form__title"
                                        style="font-family: sans-serif;font-size: 18px;color: rgb(0,0,0);font-weight: bold;">
                                        Business | Online Store URL</h3>
                                    <div class="divider-line"></div>
                                    <br>

                                    <div id="url-fields-wrapper" class="container-fluid">
                                        <!-- Label and Add Button -->
                                        <div class="d-flex justify-content-between align-items-center mb-2">
                                            <label class="form-label custom-label mb-0">
                                                Business URL: <span class="required-field">*</span>
                                            </label>
                                            <button type="button" class="btn btn-primary btn-sm" onclick="addUrlField()"
                                                style="background-color: #29b7cb; font-family:sans-serif;font-size:14px;">
                                                Add Online Store URL
                                            </button>
                                        </div>

                                        <!-- Container for all URL input rows -->
                                        <div id="url-inputs-container" class="w-100"></div>

                                        <!-- Hidden input to hold JSON array of URLs -->
                                        <input type="hidden" name="url_platform_json" id="url_platform_json"
                                            value="{{ json_encode($business->url_platform) }}">
                                    </div>

                                    <br> <br>
                                    <h3 class="text-center multisteps-form__title"
                                        style="font-family: sans-serif;font-size: 18px;color: rgb(0,0,0);font-weight: bold;">
                                        Authorized Representative</h3>
                                    <div class="divider-line"></div>
                                    <br>

                                    <div class="row">
                                        <div class="col-md-4">
                                            <div class="mb-3">
                                                <label class="form-label custom-label">Name: <span
                                                        class="required-field">*</span></label>
                                                <input class="form-control custom-input" type="text" name="name"
                                                    id="name" placeholder="Name" required
                                                    value="{{ $business->pic_name }}" readonly>
                                            </div>
                                        </div>
                                        <div class="col-md-4">
                                            <div class="mb-3">
                                                <label class="form-label custom-label">Mobile No.: <span
                                                        class="required-field">*</span></label>
                                                <div class="input-group">
                                                    <span class="input-group-text custom-input">+63</span>
                                                    <input class="form-control custom-input" type="tel"
                                                        id="number" placeholder="Mobile No." required maxlength="10"
                                                        inputmode="numeric" pattern="[0-9]{10}"
                                                        oninput="this.value = this.value.replace(/[^0-9]/g, '')"
                                                        value="{{ preg_replace('/^\+63/', '', $business->pic_ctc_no ?? '') }}">
                                                </div>

                                                <input type="hidden" name="ctc_no" id="full_number"
                                                    value="{{ $business->pic_ctc_no ?? '' }}">

                                            </div>
                                        </div>
                                        <div class="col-md-4">
                                            <div class="mb-3">
                                                <label class="form-label custom-label">Email: <span
                                                        class="required-field">*</span></label>
                                                <input class="form-control custom-input" type="email" name="email"
                                                    id="email" placeholder="Email" required
                                                    value="{{ $business->pic_email }}" readonly>
                                                <div id="email-error" class="text-danger mt-1" style="font-size: 13px;">
                                                </div>
                                            </div>
                                        </div>
                                    </div>

                                    <div class="row" style="margin-bottom:10px;">
                                        <div class="col">
                                            <div class="mb-3">
                                                <label class="form-label custom-label">Government Issued ID<span
                                                        class="required-field">*</span></label>
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
                                                            {{ old('requirement_id', $business->requirement_id ?? '') == $req->id ? 'selected' : '' }}>
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
                                                        id="req_upload" name="req_upload" accept=".jpg,.jpeg,.png,.pdf"
                                                        title="Please upload .jpg, .jpeg, .png, or .pdf. Max size 10 MB">
                                                </div>

                                                @if (!empty($business->requirement_upload))
                                                    @php
                                                        $filename = basename($business->requirement_upload);
                                                    @endphp

                                                    <a href="{{ route('business.download_authorized', encrypt($business->id)) }}"
                                                        class="d-flex align-items-center gap-2">
                                                        <i class="fa fa-download"></i>
                                                        <span class="custom-label"
                                                            title="{{ $filename }}">{{ $filename }}</span>
                                                    </a>

                                                    @if (in_array(pathinfo($business->requirement_upload, PATHINFO_EXTENSION), ['jpg', 'jpeg', 'png']))
                                                        <div class="mt-2">
                                                            <img src="{{ asset('storage/' . $business->requirement_upload) }}"
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
                                                    value="{{ old('requirement_expired', $business->requirement_expired ?? '') }}"
                                                    readonly>

                                                <input type="hidden" name="expired_date" id="expirationDateInput"
                                                    value="{{ old('requirement_expired', $business->requirement_expired ?? '') }}">
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <div class="tab-pane" role="tabpanel" id="tab-detail">
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
                                    <div class="col-md-6">
                                        <div class="mb-3">
                                            <label class="form-label custom-label">Region<span
                                                    class="required-field">*</span></label>
                                            <select name="region" id="region" class="form-select custom-select"
                                                required>
                                                <option value="">Select Region</option>
                                                @foreach ($regions as $id => $name)
                                                    <option value="{{ $id }}"
                                                        {{ ($business->region_id ?? '') == $id ? 'selected' : '' }}>
                                                        {{ $name }}
                                                    </option>
                                                @endforeach
                                            </select>
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="mb-3">
                                            <label class="form-label custom-label">Province2<span
                                                    class="required-field">*</span></label>
                                            <select class="form-select custom-select" id="province" name="province"
                                                required>
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
                                </div>
                                <div class="row">
                                    <div class="col-md-6">
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
                                    <div class="col-md-6">
                                        <div class="mb-3">
                                            <label class="form-label custom-label">Barangay<span
                                                    class="required-field">*</span></label>
                                            <select class="form-select custom-select" id="barangay" name="barangay"
                                                required>
                                                <option value="">Select Barangay</option>
                                                @if (!empty($business->barangay_id))
                                                    @php
                                                        $barangays = DB::table('barangays AS bgf')
                                                            ->join('regions AS pr', 'pr.id', '=', 'bgf.reg_no')
                                                            ->join('provinces AS pp', 'pp.id', '=', 'bgf.prov_no')
                                                            ->join('municipalities AS pm', 'pm.id', '=', 'bgf.mun_no')
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
                            </div>

                            <div class="tab-pane" role="tabpanel" id="tab-document">
                                <div class="row">
                                    <div class="col-md-6">
                                        <div class="mb-3">
                                            <label class="form-label custom-label">Business Registration (SEC/DTI/CDA):
                                                <span class="required-field">*</span></label>
                                        </div>
                                    </div>
                                    <div class="col-md-6">
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
                                                    class="d-flex align-items-center gap-2">
                                                    <i class="custom-icon fa fa-download"></i>
                                                    <span class="custom-label"
                                                        title="{{ $filename }}">{{ $shortName }}</span>
                                                </a>
                                            @endif

                                            <input class="form-control custom-input" type="file" id="business_reg"
                                                name="business_reg" accept=".jpg,.jpeg,.png,.pdf"
                                                title="Please upload file type like .jpg, .jpeg, .png, .pdf. Maximum file size untill 10 MB"
                                                @if (!$hasFile) required @endif>
                                            <small id="error-business_reg" class="text-danger"
                                                style="display: none; font-size: 13px;"></small>
                                        </div>
                                    </div>
                                </div>

                                <div class="row">
                                    <div class="col-md-6">
                                        <div class="mb-3">
                                            <label class="form-label custom-label">BIR 2303: <span
                                                    class="required-field">*</span></label>
                                        </div>
                                    </div>
                                    <div class="col-md-6">
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
                                                    class="d-flex align-items-center gap-2">
                                                    <i class="custom-icon fa fa-download"></i>
                                                    <span class="custom-label"
                                                        title="{{ $filename }}">{{ $shortName }}</span>
                                                </a>
                                            @endif

                                            <input class="form-control custom-input" type="file" id="bir_2303"
                                                name="bir_2303" accept=".jpg,.jpeg,.png,.pdf"
                                                title="Please upload file type like .jpg, .jpeg, .png, .pdf. Maximum file size untill 10 MB"
                                                @if (!$hasFile) required @endif>
                                            <small id="error-bir_2303" class="text-danger"
                                                style="display: none; font-size: 13px;"></small>
                                        </div>
                                    </div>
                                </div>

                                <div class="row">
                                    <div class="col-md-6">
                                        <div class="mb-3">
                                            <label class="form-label custom-label">Internal Redress Mechanism:
                                                <span class="required-field">*</span></label>
                                            <br>
                                            <label class="form-label custom-label">
                                                To assist in establishing an Internal Redress Mechanism,<br>
                                                a guideline template is available for download
                                                <a href="{{ route('internal.redress.download') }}?v={{ time() }}"
                                                    target="_blank" style="color: blue; text-decoration: underline;">
                                                    here
                                                </a>
                                            </label>
                                        </div>
                                    </div>
                                    <div class="col-md-6">
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
                                                    class="d-flex align-items-center gap-2">
                                                    <i class="custom-icon fa fa-download"></i>
                                                    <span class="custom-label"
                                                        title="{{ $filename }}">{{ $shortName }}</span>
                                                </a>
                                            @endif

                                            <input class="form-control custom-input" type="file" id="internal_redress"
                                                name="internal_redress" accept=".jpg,.jpeg,.png,.pdf"
                                                title="Please upload file type like .jpg, .jpeg, .png, .pdf. Maximum file size untill 10 MB"
                                                @if (!$hasFile) required @endif>
                                            <small id="error-internal_redress" class="text-danger"
                                                style="display: none; font-size: 13px;"></small>
                                        </div>
                                    </div>
                                </div>

                                <div class="row">
                                    <div class="col-md-6">
                                        <div class="mb-3">
                                            <label class="form-label custom-label">Are you Barangay Micro Business Enterprise(BMBE) registered?
                                               
                                                <span class="required-field">*</span></label>
                                            <br>
                                            <label class="form-label custom-label">
                                            If yes, please upload your BMBE Certificate.
                                            </label>
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="mb-3">
                                            <label>
                                                <input type="radio" name="is_bmbe" value="1" 
                                                    {{ old('is_bmbe', $business->is_bmbe) == 1 ? 'checked' : '' }}>
                                                Yes
                                            </label>

                                            <label style="margin-left:15px;">
                                                <input type="radio" name="is_bmbe" value="0" 
                                                    {{ old('is_bmbe', $business->is_bmbe) == 0 ? 'checked' : '' }}>
                                                No
                                            </label>
                                            @php
                                                $hasFile = !empty($business->bmbe_doc);
                                                $filename = basename($business->bmbe_doc);
                                                $shortName =
                                                    strlen($filename) > 15
                                                        ? substr($filename, 0, 15) . '...'
                                                        : $filename;
                                            @endphp
                                            @if ($hasFile)
                                                <a href="{{ route('business.download_bmbe_doc', $business->id) }}"
                                                    class="d-flex align-items-center gap-2" style="margin-top: -20px;padding-left: 119px;">
                                                    <i class="custom-icon fa fa-download"></i>
                                                    <span class="custom-label"
                                                        title="{{ $filename }}">{{ $shortName }}</span>
                                                </a>
                                            @endif
                                            <input class="form-control custom-input" type="file" id="bmbe_doc"
                                                name="bmbe_doc" accept=".jpg,.jpeg,.png,.pdf"
                                                title="Please upload file type like .jpg, .jpeg, .png, .pdf. Maximum file size untill 10 MB">
                                            <small id="error-bmbe_doc" class="text-danger"
                                                style="display: none; font-size: 13px;"></small>
                                        </div>
                                    </div>
                                </div>
                                <div class="divider-line"></div>
                                <h3 class="text-center multisteps-form__title"
                                        style="font-family: sans-serif;font-size: 18px;color: rgb(0,0,0);font-weight: bold;">
                                        Additional Permits (Optional)</h3>
                                        <div class="divider-line"></div>
                                        <button type="button" class="btn btn-primary" id="addDocumentBtn" style=" padding: 4px 8px; font-size: 12px; float: inline-end;">
                                            Add Document
                                        </button>
                                        <br>
                                        <!-- <style>
                                            #document-container {
                                                max-height: 80px; 
                                                overflow-y: auto;
                                                overflow-x: hidden;
                                            }

                                        </style> -->
                                        <div class="row document-row mb-2" id="document-row">
                                            <div class="col-md-7">
                                                <strong>Document Name </strong>
                                            </div>
                                            <div class="col-md-5">
                                            <strong>Attachment</strong>
                                            <p style="font-size: 10px;margin-bottom: 2px !important;color: #bb2121;">Please upload file type like .jpg, .jpeg, .png, .pdf. Maximum file size is 10mb</p>
                                            </div>
                                        </div>
                                        <div id="document-container">
                                        @foreach($AdditionalDocuments as $doc)
                                            <div class="row document-row mb-2">
                                                <div class="col-md-6" style="padding-top: 20px;">
                                                    <input type="text" class="form-control custom-input" name="document_name[]" value="{{ $doc->name }}" placeholder="Document Name" disabled/>
                                                </div>
                                                <div class="col-md-5">
                                                @if ($hasFile)
                                                        <a href="{{ route('business.download_AdditionalDocuments', $doc->id) }}" target="_blank"
                                                            class="custom-label d-flex align-items-center gap-2"
                                                            title="{{ $doc->attachment}}">
                                                            <i class="custom-icon fa fa-download"></i>
                                                            <span>{{ Str::limit(basename($doc->attachment), 20) }}</span>
                                                        </a>
                                                    @endif
                                                
                                                    <input type="file" class="form-control custom-input" name="attachment[]" accept=".jpg,.jpeg,.png,.pdf" disabled/>
                                                    
                                                </div>
                                                <div class="col-md-1 d-flex align-items-center" style="padding-top: 12px;">
                                                <span class="delete-btn text-danger fs-4" style="cursor: pointer;" data-id="{{ $doc->id }}">
                                                    <i class="fa fa-trash"></i>
                                                </span>
                                                </div>
                                            </div>
                                        @endforeach
                                        </div>
                            </div>
                        </div>

                    </div>
                </div>
                <div class="d-flex justify-content-end mt-4">
                    <button class="btn btn-primary" type="button" title="Next" id="Update"
                        style="font-family:sans-serif;font-size:14px;margin-right:5px;">
                        Update
                    </button>
                    <button class="btn btn-primary js-btn-next" type="submit" title="Next" id="saveAppointmentBtn"
                        style="font-family:sans-serif;font-size:14px;" disabled>
                        Submit
                    </button>
                </div>
                <br><br>
            </form>
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
    <script>
        $(document).ready(function () {
        $("#Update").on("click", function () {
            let form = $("#businessForm")[0];
            if (!form.checkValidity()) {
                form.reportValidity(); 
                return;
            }

            Swal.fire({
                title: 'Are you sure?',
                text: "Do you want to update this form?",
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#3085d6',
                cancelButtonColor: '#d33',
                confirmButtonText: 'Yes, Update',
                cancelButtonText: 'No'
            }).then((result) => {
                if (result.isConfirmed) {
                    let formData = $("#businessForm").serialize();

                    $.ajax({
                        url: "{{ route('business.updateEditOnly', $business->id) }}",
                        type: "POST",
                        data: formData,
                        success: function (response) {
                            Swal.fire(
                                'Updated!',
                                'Your form has been updated successfully.',
                                'success'
                            );
                            $("#saveAppointmentBtn").prop("disabled", false);
                        },
                        error: function (xhr) {
                            Swal.fire(
                                'Error!',
                                'Something went wrong. Please try again.',
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
        const BASE_URL = "{{ url('/') }}"; // Laravel helper sets correct subdirectory


        let provinceSelect;
        let municipalitySelect;
        let barangaySelect;

        document.addEventListener('DOMContentLoaded', function() {
            new TomSelect('#region');

            // Initialize TomSelect for province with scrollable dropdown
            provinceSelect = new TomSelect('#province', {
                placeholder: 'Select Province',
                maxOptions: false, // important! show all
                preload: true,
                loadThrottle: 0
            });

            // Initialize TomSelect for municipality with scrollable dropdown
            municipalitySelect = new TomSelect('#municipality', {
                placeholder: 'Select Municipality',
                maxOptions: false, // important! show all
                preload: true,
                loadThrottle: 0
            });

            // Initialize TomSelect for municipality with scrollable dropdown
            barangaySelect = new TomSelect('#barangay', {
                placeholder: 'Select Barangay',
                maxOptions: false, // important! show all
                preload: true,
                loadThrottle: 0
            });

            new TomSelect('#category', {
                placeholder: "Select Category",
                // allowEmptyOption: true
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
                            provinceSelect.setValue('');
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
                //     const csrfToken = document.querySelector('meta[name="csrf-token"]')
                //         .getAttribute('content');
                //     const businessId = document.querySelector('input[name="business_id"]')?.value ||
                //         '';

                //     fetch('{{ route('check.tin') }}', {
                //             method: 'POST',
                //             headers: {
                //                 'Content-Type': 'application/json',
                //                 'X-CSRF-TOKEN': csrfToken
                //             },
                //             body: JSON.stringify({
                //                 tin: tinInput.value,
                //                 business_id: businessId
                //             })
                //         })
                //         .then(response => response.json())
                //         .then(data => {
                //             if (data.exists) {
                //                 tinError.textContent = 'This TIN is already registered.';
                //             } else {
                //                 tinError.textContent = '';
                //             }
                //         })
                //         .catch(err => {
                //             tinError.textContent = 'Error checking TIN.';
                //             console.error(err);
                //         });
                // }, 500);
            });
        });
    </script>

    {{-- Checking file upload --}}
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const allowedTypes = ['image/jpeg', 'image/png', 'application/pdf'];
            const maxSize = 10 * 1024 * 1024; // 10MB in bytes

            const fileInputs = ['business_reg', 'bir_2303', 'internal_redress'];

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

    {{-- update Registration number name --}}
    <script>
        document.addEventListener('DOMContentLoaded', () => {
            const typeSelect = document.getElementById('type_id');
            const regLabel = document.getElementById('reg-label');

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

        function updateUrlPlatformJson() {
            const urls = [];
            for (let i = 1; i <= urlFieldCount; i++) {
                const input = document.getElementById(`url_platform_${i}`);
                if (input && input.value.trim()) {
                    urls.push(input.value.trim());
                }
            }
            document.getElementById('url_platform_json').value = JSON.stringify(urls);
        }

        function createUrlField(url = '') {
            urlFieldCount++;

            const container = document.getElementById("url-inputs-container");

            const fieldWrapper = document.createElement("div");
            fieldWrapper.className = "row mb-2";
            fieldWrapper.id = `url_field_wrapper_${urlFieldCount}`;

            const inputCol = document.createElement("div");
            inputCol.className = "col-12 col-sm-12 col-md-12 d-flex align-items-center";

            const input = document.createElement("input");
            input.type = "text";
            input.name = `url_platform_${urlFieldCount}`;
            input.id = `url_platform_${urlFieldCount}`;
            input.placeholder = "Enter platform URL";
            input.className = "form-control custom-input";
            input.value = url;
            input.required = urlFieldCount === 1; // require only first field?
            input.addEventListener('input', () => {
                validateUrl(input);
                updateUrlPlatformJson();
            });

            const visitBtn = document.createElement("button");
            visitBtn.type = "button";
            visitBtn.className = "btn btn-success btn-sm ms-2";
            visitBtn.style.backgroundColor = "#29b7cb";
            visitBtn.style.border = "none";
            visitBtn.innerHTML = `<i class="fa fa-globe text-white"></i>`;
            visitBtn.onclick = () => visitUrl(input.id);

            const deleteBtn = document.createElement("button");
            deleteBtn.type = "button";
            deleteBtn.className = "btn btn-outline-danger btn-sm ms-2";
            deleteBtn.innerHTML = `<i class="fa fa-trash"></i>`;
            deleteBtn.onclick = () => {
                fieldWrapper.remove();
                updateUrlPlatformJson();
            };

            inputCol.appendChild(input);
            inputCol.appendChild(visitBtn);
            inputCol.appendChild(deleteBtn);

            const errorDiv = document.createElement("div");
            errorDiv.id = `${input.id}-error`;
            errorDiv.className = "text-danger mt-1";
            errorDiv.style.fontSize = "0.85rem";

            fieldWrapper.appendChild(inputCol);
            fieldWrapper.appendChild(errorDiv);

            container.appendChild(fieldWrapper);

            updateUrlPlatformJson();
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
            let urlSet = new Set();
            let hasDuplicate = false;

            for (let i = 1; i <= urlFieldCount; i++) {
                const input = document.getElementById(`url_platform_${i}`);
                if (input) {
                    if (!validateUrl(input)) {
                        valid = false;
                    }
                    // Normalize URL: trim and lowercase for duplicate check
                    const val = input.value.trim();
                    const normVal = val.toLowerCase();
                    if (val) {
                        if (urlSet.has(normVal)) {
                            hasDuplicate = true;
                        }
                        urlSet.add(normVal);
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

            if (hasDuplicate) {
                alert("Duplicate URLs are not allowed. Please remove duplicates.");
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
                    maxOptions: null
                });
            }
        });
    </script>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const form = document.getElementById('businessForm');
            const finalSubmitBtn = document.getElementById('saveAppointmentBtn');
            const confirmCheckbox = document.getElementById('confirmCheckbox');
            const confirmSubmitBtn = document.getElementById('confirmSubmitBtn');

            // Prevent default submit to show modal
            finalSubmitBtn.addEventListener('click', function(e) {
                // Always check for duplicate URLs before showing modal
                if (!prepareUrlsForSubmit()) {
                    e.preventDefault();
                    return;
                }
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
                // Check again for duplicate URLs before final submit
                if (!prepareUrlsForSubmit()) {
                    confirmSubmitBtn.disabled = false;
                    confirmSubmitBtn.innerHTML = 'Confirm';
                    return;
                }
                confirmSubmitBtn.disabled = true;
                confirmSubmitBtn.innerHTML =
                    `<span class="spinner-border spinner-border-sm me-2" role="status" aria-hidden="true"></span>Submitting...`;
                form.submit();
            });
        });
    </script>


    <style>
        input[readonly] {
            background-color: #e9ecef;
            pointer-events: none;
        }
    </style>
<script>
$(document).ready(function () {
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
                    <span class="delete-btn text-danger fs-4" style="cursor: pointer;">
                        <i class="fa fa-trash"></i>
                    </span>
                </div>
            </div>
        `;
    }
    $('#addDocumentBtn').on('click', function () {
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
        $('.document-row').each(function () {
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
        // $('#saveAppointmentBtn').prop('disabled', hasIncompleteRow());
    }
    $(document).on('input change', '.doc-name, .doc-file', checkButtonState);
    $(document).on('click', '.delete-btn', function () {
        $(this).closest('.document-row').remove();
        checkButtonState();
    });
});
</script>


    <script>
$(document).ready(function () {
    $('.delete-btn').click(function () {
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
                    success: function (response) {
                        $this.closest('.document-row').remove();

                        Swal.fire(
                            'Deleted!',
                            'Document has been deleted.',
                            'success'
                        );
                    },
                    error: function (xhr) {
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
$(document).ready(function () {
    const $docField = $("#bmbe_doc");
    const $saveBtn = $("#saveAppointmentBtn");
    const hasExistingFile = @json(!empty($business->bmbe_doc));

    function toggleBmbeDoc() {
        let selected = $("input[name='is_bmbe']:checked").val();

        if (selected == "1") {
            //$docField.prop("disabled", false).prop("required", true).removeAttr("readonly");
            toggleSaveBtn();
        } else {
            $docField.prop("disabled", true).prop("required", false).attr("readonly", true).val("");
            // $saveBtn.prop("disabled", false); 
        }
    }

    function toggleSaveBtn() {
        let selected = $("input[name='is_bmbe']:checked").val();
        if (selected == "1" && !hasExistingFile && $docField.val().trim() === "") {
            $saveBtn.prop("disabled", true);
        } else {
            $saveBtn.prop("disabled", false);
        }
    }
    toggleBmbeDoc();
    $("input[name='is_bmbe']").on("change", function () {
        toggleBmbeDoc();
    });
    $docField.on("input", function () {
        toggleSaveBtn();
    });
});

</script>
@endsection
