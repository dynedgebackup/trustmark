<!-- Business Information -->
<div class="modal fade" id="business_informationModal" tabindex="-1" aria-labelledby="business_informationModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
        <div class="modal-header">
            <h5 class="modal-title" id="business_informationModalLabel">Business Information</h5>
            <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
        </div>
        <div class="modal-body">
            <form id="business_informationForm">
            @csrf
                <div class="row">
                    <div class="col-12">
                        <div class="mb-3">
                            <label class="form-label custom-label">Business Type:<span
                                    class="required-field">*</span></label>
                            <select class="form-select custom-select" name="type_id"
                                id="type_id" required>
                                <option value="" selected>Select Business Type
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
                                required value="{{ $business->reg_num }}" maxlength="25">
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
           </form>
        </div>
        <div class="modal-footer">
            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
            <button type="button" class="btn btn-primary" id="saveBusinessInformation" style="border: none;background: #09325d;">Save Changes</button>
        </div>
        </div>
    </div>
</div>
<!-- Business URL/Website/Social Media Platform Link -->
<div class="modal fade" id="urlModal" tabindex="-1" aria-labelledby="urlModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
        <div class="modal-header">
            <h5 class="modal-title" id="urlModalLabel">Business URL/Website/Social Media Platform Link</h5>
            <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
        </div>
        <div class="modal-body">
            <button type="button" class="btn btn-sm btn-primary mt-2" id="addMore" style="float: inline-end;border: none;background: #09325d;">+ Add Online Store URL</button>
            <br><br>
            <form id="urlForm">
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
            <div id="urlFields" style="padding-top:10px;"></div>
            
            </form>
        </div>
        <div class="modal-footer">
            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
            <button type="button" class="btn btn-primary" id="saveUrls" style="border: none;background: #09325d;">Save Changes</button>
        </div>
        </div>
    </div>
</div>
<!-- Authorized Representative -->
<div class="modal fade" id="authorizedRepresentativeModal" tabindex="-1" aria-labelledby="authorizedRepresentativeModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
        <div class="modal-header">
            <h5 class="modal-title" id="authorizedRepresentativeModalLabel">Authorized Representative</h5>
            <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
        </div>
        <div class="modal-body">
            <form id="authorizedRepresentativeForm" enctype="multipart/form-data">
            @csrf
            <div class="row">
                <div class="col-12 col-sm-6 col-md-4">
                    <div class="mb-3">
                        <label class="form-label custom-label">First Name <span
                                class="required-field">*</span></label>
                        <input class="form-control custom-input" type="text"
                            name="first_name" id="first_name" placeholder="First Name" required
                            value="{{ optional($business)->first_name ?? optional($user)->first_name }}">
                    </div>
                </div>
                <div class="col-12 col-sm-6 col-md-4">
                    <div class="mb-3">
                        <label class="form-label custom-label">Middle Name</label>
                        <input class="form-control custom-input" type="text"
                            name="middle_name" id="middle_name" placeholder="Middle Name" 
                            value="{{ optional($business)->middle_name ?? optional($user)->middle_name }}">
                    </div>
                </div>
                <div class="col-12 col-sm-6 col-md-4">
                    <div class="mb-3">
                        <label class="form-label custom-label">Last Name <span
                                class="required-field">*</span></label>
                        <input class="form-control custom-input" type="text"
                            name="last_name" id="last_name" placeholder="Last Name" 
                            value="{{ optional($business)->last_name ?? optional($user)->last_name }}" required>
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
                <div class="col-12 col-sm-4 col-md-4">
                    <div class="mb-6">
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
                            value="{{ $business->pic_ctc_no ?? '' }}">

                    </div>
                </div>
                <div class="col-12 col-sm-4 col-md-4">
                    <div class="mb-6">
                        <label class="form-label custom-label">Email: <span
                                class="required-field">*</span></label>
                        <input class="form-control custom-input" type="email"
                            name="email" id="email" placeholder="Email" required
                            value="{{ $business->pic_email }}" readonly>
                        <div id="email-error" class="text-danger mt-1"
                            style="font-size: 13px;"></div>
                    </div>
                </div>
            </div>

            <div class="row" style="margin-bottom:10px;">
                <div class="col-md-12">
                    <div class="mb-6">
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
                                    {{ old('requirement_id', $business->requirement_id ?? '') == $req->id ? 'selected' : '' }}>
                                    {{ $req->description }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="col-md-12" >
                        <label class="form-label custom-label">Attachment<span
                                class="required-field">*</span></label>

                        <div class="mb-12" style="padding-bottom:10px;">
                            <input class="form-control custom-input" type="file"
                                id="req_upload" name="req_upload"
                                accept=".jpg,.jpeg,.png,.pdf"
                                title="Please upload .jpg, .jpeg, .png, or .pdf. Max size 10 MB">
                        </div>

                        @php
                        $filePath = optional($business)->requirement_upload ?? optional($user)->requirement_upload ?? null;

                        $fileRoute = optional($business)->requirement_upload
                            ? route('business.download_authorized', encrypt($business->id))
                            : (optional($user)->requirement_upload
                                ? route('profile.download_authorized', $user->id)
                                : null);

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
                <div class="col-md-6">
                    @php
                        $today = \Carbon\Carbon::today()->format('Y-m-d');
                    @endphp

                    <div class="mb-6">
                        <label class="form-label custom-label">Expiry Date<span
                                class="required-field">*</span></label>
                        <input class="form-control custom-input" type="date"
                            id="expirationDateInputVisible" name="expiration_date_visible"
                            placeholder="Date" min="{{ $today }}"
                            value="{{ old('requirement_expired', $business->requirement_expired ?? '') }}"
                            readonly>

                        <input type="hidden" name="expired_date"
                            id="expirationDateInput"
                            value="{{ old('requirement_expired', $business->requirement_expired ?? '') }}">
                    </div>
                </div>
                
                
            </div>
           </form>
        </div>
        <div class="modal-footer">
            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
            <button type="button" class="btn btn-primary" id="saveAuthorizedRepresentative" attr-id="{{ $business->id }}" style="border: none;background: #09325d;">Save Changes</button>
        </div>
        </div>
    </div>
</div>
<!-- Business Address -->
<div class="modal fade" id="businessAddressModal" tabindex="-1" aria-labelledby="businessAddressModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
        <div class="modal-header">
            <h5 class="modal-title" id="businessAddressModalLabel">Business Address</h5>
            <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
        </div>
        <div class="modal-body">
            <form id="businessAddressForm" enctype="multipart/form-data">
            @csrf
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
                <div class="col-12 col-sm-6 col-md-12">
                    <div class="mb-3">
                        <label class="form-label custom-label">Barangay, City/Municipality, Province, Region<span
                                class="required-field">*</span></label>
                        <select class="form-select custom-select" id="barangay_id"
                            name="barangay_id" required>
                            <option value="">Select Barangay, City/Municipality, Province, Region</option>
                            @foreach ($barangays as $id => $brgy_description)
                                <option value="{{ $id }}"
                                    {{ ($business->barangay_id ?? '') == $id ? 'selected' : '' }}>
                                    {{ $brgy_description }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                </div>
                
            </div>
           </form>
        </div>
        <div class="modal-footer">
            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
            <button type="button" class="btn btn-primary" id="savebusinessAddress" attr-id="{{ $business->id }}" style="border: none;background: #09325d;">Save Changes</button>
        </div>
        </div>
    </div>
</div>
<!-- Attachments -->
<div class="modal fade" id="attachmentsModal" tabindex="-1" aria-labelledby="attachmentsModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content" style="width:124% !important;">
        <div class="modal-header">
            <h5 class="modal-title" id="attachmentsLabel">Document Attachments</h5>
            <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
        </div>
        <div class="modal-body">
            <form id="attachmentsForm" enctype="multipart/form-data">
            @csrf
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
                                        title="{{ $filename }}">{{ $filename }}</span>
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
                                        title="{{ $filename }}">{{ $filename }}</span>
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
                                        title="{{ $filename }}">{{ $filename }}</span>
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
                            <input type="hidden" id="payment_id" value="{{(int)$business->payment_id}}">
                            <input type="hidden" id="prev_bmbe_doc" value="{{$business->bmbe_doc}}">
                            <input type="hidden" id="prev_busn_valuation_doc" value="{{$business->busn_valuation_doc}}">

                            @php
                                $disabled = ($business->payment_id)>0?'disabled':'';
                            @endphp
                            <label>
                                <input type="radio" name="is_bmbe" value="1" 
                                    {{ old('is_bmbe', $business->is_bmbe) == 1 ? 'checked' : '' }} {{$disabled}}>
                                Yes
                            </label>

                            <label style="margin-left:15px;">
                                <input type="radio" name="is_bmbe" value="0"
                                    {{ old('is_bmbe', $business->is_bmbe) == 0 ? 'checked' : '' }} {{$disabled}}>
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
                                <a href="{{ route('business.download_bmbe_doc', $business->id) }}?v={{ time() }}"
                                    class="d-flex align-items-center gap-2" style="margin-top: -20px;padding-left: 119px;" target="_blank">
                                    <i class="custom-icon fa fa-download"></i>
                                    <span class="custom-label"
                                        title="{{ $filename }}">{{ $filename }}</span>
                                </a>
                            @endif
                            <input {{$disabled}} class="form-control custom-input" type="file" id="bmbe_doc"
                                name="bmbe_doc" accept=".jpg,.jpeg,.png,.pdf"
                                title="Please upload file type like .jpg, .jpeg, .png, .pdf. Maximum file size untill 10 MB"
                                @if (!$hasFile) required @endif >
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
                                                            @if(isset($business->busn_category_id) && $business->busn_category_id == $businesscategory->busn_category_id)
                                                                selected
                                                            @elseif(!isset($business->busn_category_id) && ($businesscategory->is_default ?? 0) == 1)
                                                                selected
                                                            @endif
                                                        >
                                                            {{ $businesscategory->busn_category_name ?? '' }}
                                                        </option>
                                                    @endforeach
                                                </select>


                                                        <small id="error-busn_category_id" class="text-danger"
                                                        style="display: none; font-size: 13px;"></small>
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
                                                        <a href="{{ route('business.download_busn_valuation_doc', $business->id) }}?v={{ time() }}"
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
           </form>
        </div>
        <div class="modal-footer">
            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
            <button type="button" class="btn btn-primary" id="saveattachments" attr-id="{{ $business->id }}" style="border: none;background: #09325d;">Save Changes</button>
        </div>
        </div>
    </div>
</div>
<!-- Additional Permits -->
<div class="modal fade" id="additionalPermitsModal" tabindex="-1" aria-labelledby="additionalPermitsModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
        <div class="modal-header">
            <h5 class="modal-title" id="additionalPermitsLabel">Additional Permits (For Regulated Products)</h5>
            <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
        </div>
        <div class="modal-body">
            <form id="additionalPermitsForm" enctype="multipart/form-data">
            @csrf
            <p style="font-size: 10px;margin-bottom: 2px !important;color: #bb2121;">Please upload file type like .jpg, .jpeg, .png, .pdf. Maximum file size is 10mb</p>
                <button type="button" class="btn btn-primary" id="addDocumentBtn" style=" padding: 4px 8px; font-size: 12px; float: inline-end;">
                    Add Document
                </button>
                <br>
                <div class="row document-row mb-2" id="document-row" style="margin-top: 13px;border: 1px solid #ccc;">
                    <div class="col-md-6" style="border-right: 1px solid #ccc;">
                        <strong>Document Name </strong>
                    </div>
                    <div class="col-md-5" style="border-right: 1px solid #ccc;">
                    <strong>Attachment</strong>
                    
                    </div>
                    <div class="col-md-1" style="">
                    <strong>Action</strong>
                    
                    </div>
                </div>
                <div id="document-container">
                    @foreach($AdditionalDocuments as $doc)
                    <div class="row document-row mb-2">
                        <div class="col-md-6" style="">
                            <input type="text" class="form-control custom-input" name="document_name[]" value="{{ $doc->name }}" placeholder="Document Name" disabled/>
                        </div>
                        <div class="col-md-5">
                       
                        
                            <input type="file" class="form-control custom-input" name="attachment[]" accept=".jpg,.jpeg,.png,.pdf" disabled/>
                            
                        </div>
                        <div class="col-md-1 d-flex align-items-center" style="padding-top: 0px;">
                        @if ($doc)
                                <a href="{{ route('business.download_AdditionalDocuments', $doc->id) }}" target="_blank"
                                    class="custom-label d-flex align-items-center gap-2"
                                    title="{{ $doc->attachment}}">
                                    <i class="custom-icon fa fa-download" style="font-size: 21px;padding-right: 5px;"></i>
                                    <!-- <span>{{ Str::limit(basename($doc->attachment), 20) }}</span> -->
                                </a>
                            @endif
                        <span class="delete-btn text-danger fs-4" style="cursor: pointer;" data-id="{{ $doc->id }}">
                            <i class="fa fa-trash"></i>
                        </span>
                        </div>
                    </div>
                   @endforeach
                </div>
           </form>
        </div>
        <div class="modal-footer">
            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
            <button type="button" class="btn btn-primary" id="saveadditionalPermits" attr-id="{{ $business->id }}" style="border: none;background: #09325d;">Save Changes</button>
        </div>
        </div>
    </div>
</div>
<style>
            .modal-xxl {
                max-width: 90% !important;
            }

            #checkRecordsTableBusiness {
                width: 100% !important;
            }
        </style>
        <!-- Modal -->
        <div class="modal fade" id="checkRecordsModal" tabindex="-1" aria-hidden="true">
            <div class="modal-dialog modal-xxl">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title">Duplicated TIN Reference</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                    </div>
                    <div class="modal-body">
                        <!-- Add wrapper div for horizontal scroll -->
                        <div style="overflow-x:auto;" style="width:100%;">
                            <table id="checkRecordsTable" class="table table-bordered table-striped" style="width:100%;">
                                <thead style="font-size: 10px  !important;">
                                    <tr>
                                        <th style="width:2%;">No.</th>
                                        <th style="width:12%;">Security No.</th>
                                        <th style="width:18%;">Business Name</th>
                                        <th style="width:18%;">Registration No.</th>
                                        <th style="width:18%;">Evaluator</th>
                                        <th style="width:12%;">Business Type</th>
                                        <th style="width:10%;">TIN</th>
                                        <th style="width:12%;">Representative</th>
                                        <th style="width:8%;">Submitted</th>
                                        <th style="width:5%;">Remarks</th>
                                        <th style="width:3%;">Status</th>
                                    </tr>
                                </thead>
                                <tbody style="font-size: 10px !important;"></tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Modal -->
        <div class="modal fade" id="checkRecordsModalBusiness" tabindex="-1" aria-hidden="true">
            <div class="modal-dialog modal-xxl">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title">Duplicated Business Name Reference</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                    </div>
                    <div class="modal-body">
                        <table id="checkRecordsTableBusiness" class="table table-bordered table-striped"
                            style="width:100%">
                            <thead style="font-size: 10px  !important;">
                                <tr>
                                    <th style="width:2%;">No.</th>
                                    <th style="width:12%;">Security No.</th>
                                    <th style="width:18%;">Business Name</th>
                                    <th style="width:18%;">Registration No.</th>
                                    <th style="width:18%;">Evaluator</th>
                                    <th style="width:12%;">Business Type</th>
                                    <th style="width:10%;">TIN</th>
                                    <th style="width:12%;">Representative</th>
                                    <th style="width:8%;">Submitted</th>
                                    <th style="width:5%;">Remarks</th>
                                    <th style="width:3%;">Status</th>
                                </tr>
                            </thead>
                            <tbody style="font-size: 10px !important;"></tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
        <!-- Modal -->
        <div class="modal fade" id="checkRecordsModalBusinessRegistration" tabindex="-1" aria-hidden="true">
            <div class="modal-dialog modal-xxl">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title">Duplicated Registration No. Reference</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                    </div>
                    <div class="modal-body">
                        <table id="checkRecordsTableBusinessRegistration" class="table table-bordered table-striped"
                            style="width:100%">
                            <thead style="font-size: 10px  !important;">
                                <tr>
                                    <th style="width:2%;">No.</th>
                                    <th style="width:12%;">Security No.</th>
                                    <th style="width:18%;">Business Name</th>
                                    <th style="width:18%;">Registration No.</th>
                                    <th style="width:18%;">Evaluator</th>
                                    <th style="width:12%;">Business Type</th>
                                    <th style="width:10%;">TIN</th>
                                    <th style="width:12%;">Representative</th>
                                    <th style="width:8%;">Submitted</th>
                                    <th style="width:5%;">Remarks</th>
                                    <th style="width:3%;">Status</th>
                                </tr>
                            </thead>
                            <tbody style="font-size: 10px !important;"></tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
        <script>
            
        </script>

    <script>
     document.addEventListener('DOMContentLoaded', function() {
        if (window.TomSelect) {
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
            new TomSelect('#barangay_id', {
                placeholder: "Select ID",
                valueField: 'id',
                labelField: 'name',
                searchField: 'name',
                create: false,
                loadThrottle: 400,
                maxOptions: 50,
                plugins: ['clear_button'],
                preload: false,
                load: function(query, callback) {
                    if (!query.length) return callback();

                    $.ajax({
                        url: "{{ route('business.barangaysearch') }}", 
                        data: { q: query },
                        success: function(res) {
                            callback(res);
                        },
                        error: function() {
                            callback();
                        }
                    });
                }
            });
        }
    });
    $(document).ready(function() {
        $(document).on("click", ".edit-information", function() {
            let businessId = $(this).attr("attr-id");
            $("#business_informationModal").modal("show");
            $("#business_informationModal").data("business-id", businessId);
        });
        $(document).on("click", ".edit-authorizedRepresentative", function() {
            let businessId = $(this).data("id"); 
            $("#authorizedRepresentativeModal").modal("show");
            $("#authorizedRepresentativeModal").data("business-id", businessId);
        });
        $(document).on("click", ".edit-businessAddress", function() {
            let businessId = $(this).data("id"); 
            $("#businessAddressModal").modal("show");
            $("#businessAddressModal").data("business-id", businessId);
        });
        $(document).on("click", ".edit-attachments", function() {
            let businessId = $(this).data("id"); 
            $("#attachmentsModal").modal("show");
            $("#attachmentsModal").data("business-id", businessId);
        });
        $(document).on("click", ".edit-additionalpermits", function() {
            let businessId = $(this).data("id"); 
            console.log("Clicked businessId:", businessId);
            $("#additionalPermitsModal").modal("show");    
            $("#additionalPermitsModal").data("business-id", businessId);
        });
        $(document).on("click", "#saveadditionalPermits", function() {
            let businessId = $(this).attr("attr-id");
            // alert(businessId);
            let formData = new FormData($("#additionalPermitsForm")[0]);

            Swal.fire({
                title: 'Are you sure?',
                text: "Do you want to save these changes?",
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#09325d',
                cancelButtonColor: '#d33',
                confirmButtonText: 'Yes, save it!'
            }).then((result) => {
                if (result.isConfirmed) {
                    $.ajax({
                        url: "{{ route('business.AdditionalPermitsstore', ':id') }}".replace(':id', businessId),
                        type: "POST",
                        data: formData,
                        processData: false,  
                        contentType: false,  
                        success: function(response) {
                            Swal.fire({
                                icon: 'success',
                                title: 'Saved!',
                                text: 'Business updated successfully.',
                                timer: 2000,
                                showConfirmButton: false
                            }).then(() => {
                                location.reload(); 
                            });
                        },
                        error: function(xhr) {
                            Swal.fire({
                                icon: 'error',
                                title: 'Oops...',
                                text: 'Something went wrong!'
                            });
                        }
                    });
                }
            });
        });
        $(document).on("click", "#saveattachments", function() {
            let isBmbe = $('input[name="is_bmbe"]:checked').val();
            let fileVal = $('#bmbe_doc').val();
            let prev_bmbe_doc = $("#prev_bmbe_doc").val();
            $('#error-bmbe_doc').hide(); // reset previous error
            // Only validate if "Yes" is selected
            let busn_category_id = $('#busn_category_id').val();
            $('#error-busn_category_id').hide();

            let busn_valuation_doc = $('#busn_valuation_doc').val();
            let prev_busn_valuation_doc = $("#prev_busn_valuation_doc").val();
            $('#error-busn_valuation_doc').hide();
            if (isBmbe === '1' && fileVal === '' && prev_bmbe_doc=='') {
                $('#error-bmbe_doc').text('Please upload BMBE certificate.').show();
                e.preventDefault(); // stop form submission
                return false;
            }
            if (isBmbe === '0' && busn_category_id === '') {
                $('#error-busn_category_id').text('Please Select Business Category.').show();
                e.preventDefault(); // stop form submission
                return false;
            }
            if (isBmbe === '0' && busn_valuation_doc === '' && prev_busn_valuation_doc=='') {
                $('#error-busn_valuation_doc').text('Please upload Proof of Total Asset Valuation.').show();
                e.preventDefault(); // stop form submission
                return false;
            }
            
            

            let businessId = $(this).attr("attr-id");
            // alert(businessId);
            let formData = new FormData($("#attachmentsForm")[0]);

            Swal.fire({
                title: 'Are you sure?',
                text: "Do you want to save these changes?",
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#09325d',
                cancelButtonColor: '#d33',
                confirmButtonText: 'Yes, save it!'
            }).then((result) => {
                if (result.isConfirmed) {
                    $.ajax({
                        url: "{{ route('business.save_documentattachments', ':id') }}".replace(':id', businessId),
                        type: "POST",
                        data: formData,
                        processData: false,  
                        contentType: false,  
                        success: function(response) {
                            Swal.fire({
                                icon: 'success',
                                title: 'Saved!',
                                text: 'Business updated successfully.',
                                timer: 2000,
                                showConfirmButton: false
                            }).then(() => {
                                location.reload(); 
                            });
                        },
                        error: function(xhr) {
                            Swal.fire({
                                icon: 'error',
                                title: 'Oops...',
                                text: 'Something went wrong!'
                            });
                        }
                    });
                }
            });
        });
        $(document).on("click", "#savebusinessAddress", function (e) {
            e.preventDefault(); 

            let businessId = $(this).attr("attr-id");
            let form = $("#businessAddressForm");
            let formData = new FormData(form[0]);
            form.find("input, select, textarea").removeClass("is-invalid");
            let isValid = true;
            let firstInvalid = null;

            form.find("[required]").each(function () {
                if (!$(this).val().trim()) {
                    $(this).addClass("is-invalid"); 
                    if (!firstInvalid) firstInvalid = $(this); 
                    isValid = false;
                }
            });

            if (!isValid) {
                if (firstInvalid) firstInvalid.focus();
                return;
            }
            Swal.fire({
                title: "Are you sure?",
                text: "Do you want to save these changes?",
                icon: "warning",
                showCancelButton: true,
                confirmButtonColor: "#09325d",
                cancelButtonColor: "#d33",
                confirmButtonText: "Yes, save it!"
            }).then((result) => {
                if (result.isConfirmed) {
                    $.ajax({
                        url: "{{ route('business.updatebusinessAddress', ':id') }}".replace(":id", businessId),
                        type: "POST",
                        data: formData,
                        processData: false,
                        contentType: false,
                        success: function (response) {
                            Swal.fire({
                                icon: "success",
                                title: "Saved!",
                                text: "Business updated successfully.",
                                timer: 2000,
                                showConfirmButton: false
                            }).then(() => {
                                location.reload();
                            });
                        },
                        error: function (xhr) {
                            Swal.fire({
                                icon: "error",
                                title: "Oops...",
                                text: "Something went wrong!"
                            });
                        }
                    });
                }
            });
        });

        $(document).on("click", "#saveAuthorizedRepresentative", function () {
            let businessId = $(this).attr("attr-id");
            let form = $("#authorizedRepresentativeForm");

            let isValid = true;
            let firstInvalid = null;
            form.find("[required]").each(function () {
                if ($(this).val().trim() === "") {
                    isValid = false;
                    $(this).addClass("is-invalid"); 
                    if (!firstInvalid) {
                        firstInvalid = $(this);
                    }
                } else {
                    $(this).css("border", ""); 
                }
            });

            if (!isValid) {
                firstInvalid.focus(); 
                return; 
            }

            let formData = new FormData(form[0]);

            Swal.fire({
                title: "Are you sure?",
                text: "Do you want to save these changes?",
                icon: "warning",
                showCancelButton: true,
                confirmButtonColor: "#09325d",
                cancelButtonColor: "#d33",
                confirmButtonText: "Yes, save it!"
            }).then((result) => {
                if (result.isConfirmed) {
                    $.ajax({
                        url: "{{ route('business.updateAuthorizedRepresentative', ':id') }}".replace(':id', businessId),
                        type: "POST",
                        data: formData,
                        processData: false,
                        contentType: false,
                        success: function (response) {
                            Swal.fire({
                                icon: "success",
                                title: "Saved!",
                                text: "Business updated successfully.",
                                timer: 2000,
                                showConfirmButton: false
                            }).then(() => {
                                location.reload();
                            });
                        },
                        error: function (xhr) {
                            Swal.fire({
                                icon: "error",
                                title: "Oops...",
                                text: "Something went wrong!"
                            });
                        }
                    });
                }
            });
        });


        $(document).on("click", "#saveBusinessInformation", function () {
            let businessId = $("#business_informationModal").data("business-id");
            let form = $("#business_informationForm");

            let isValid = true;
            let firstInvalid = null;

            form.find("[required]").each(function () {
                if ($(this).val().trim() === "") {
                    isValid = false;
                    $(this).addClass("is-invalid"); 
                    if (!firstInvalid) {
                        firstInvalid = $(this); 
                    }
                } else {
                    $(this).css("border", "");
                }
            });

            if (!isValid) {
                firstInvalid.focus(); 
                return; 
            }

            let formData = form.serialize();

            Swal.fire({
                title: "Are you sure?",
                text: "Do you want to save these changes?",
                icon: "warning",
                showCancelButton: true,
                confirmButtonColor: "#09325d",
                cancelButtonColor: "#d33",
                confirmButtonText: "Yes, save it!"
            }).then((result) => {
                if (result.isConfirmed) {
                    $.ajax({
                        url: "{{ route('business.updateBusinessInformation', ':id') }}".replace(':id', businessId),
                        type: "POST",
                        data: formData,
                        success: function (response) {
                            Swal.fire({
                                icon: "success",
                                title: "Saved!",
                                text: "Business updated successfully.",
                                timer: 2000,
                                showConfirmButton: false
                            }).then(() => {
                                location.reload();
                            });
                        },
                        error: function (xhr) {
                            Swal.fire({
                                icon: "error",
                                title: "Oops...",
                                text: "Something went wrong!"
                            });
                        }
                    });
                }
            });
        });


        $(document).on("click", ".edit-url", function() {
            let businessId = $(this).attr("attr-id");

            $.ajax({
                url: "{{ route('business.getUrls', ':id') }}".replace(':id', businessId),
                type: "GET",
                success: function(url_platform) {
                    $("#urlFields").empty();
                    url_platform.forEach(url => {
                        addUrlField(url);
                    });
                    $("#urlModal").modal("show");
                    $("#urlModal").data("business-id", businessId);
                    checkDuplicates(); 
                }
            });
        });
        $("#addMore").on("click", function() {
            addUrlField("");
            checkDuplicates();
        });
        $("#saveUrls").on("click", function() {
            let businessId = $("#urlModal").data("business-id");

            let updatedUrls = [];
            $("#urlFields input").each(function() {
                let val = $(this).val().trim();
                if (val !== "") updatedUrls.push(val);
            });
            if (hasDuplicates(updatedUrls)) {
                return Swal.fire({
                    icon: 'error',
                    title: 'Duplicate Found',
                    text: 'You have duplicate URLs. Please remove them before saving.'
                });
            }
            Swal.fire({
                title: 'Are you sure?',
                text: "Do you want to save these URLs?",
                icon: 'question',
                showCancelButton: true,
                confirmButtonColor: '#09325d',
                cancelButtonColor: '#d33',
                confirmButtonText: 'Yes, save it!'
            }).then((result) => {
                if (result.isConfirmed) {
                    $.ajax({
                        url: "{{ route('business.updateUrls', ':id') }}".replace(':id', businessId),
                        type: "POST",
                        data: {
                            _token: "{{ csrf_token() }}",
                            urls: updatedUrls
                        },
                        success: function(res) {
                            Swal.fire({
                                icon: 'success',
                                title: 'Success!',
                                text: 'URLs updated successfully!',
                                showConfirmButton: false,
                                timer: 2000
                            }).then(() => {
                                location.reload();
                            });
                            $("#urlModal").modal("hide");
                        },
                        error: function() {
                            Swal.fire({
                                icon: 'error',
                                title: 'Oops...',
                                text: 'Something went wrong while saving.'
                            });
                        }
                    });
                }
            });
        });
        function hasDuplicates(arr) {
            return new Set(arr).size !== arr.length;
        }

        function addUrlField(value = "") {
    const container = $("#urlFields");
    const index = $(".url-row").length + 1;

    const row = $(`
        <div class="input-group mb-2 url-row" data-index="${index}" style="border-bottom: 1px solid;">
            <div class="col-7" style="padding-bottom: 5px;">
            <input type="url" 
                class="form-control url-input" 
                id="url_platform_${index}" 
                name="url_platform_${index}" 
                placeholder="https://www.example.com" 
                value="${value}" 
                required>
                <small class="text-danger error-message" style="display:none;"></small>
            </div>
            <div class="col-2" style="text-align: center;">
                <span id="platform_name_${index}" class="ms-1"></span>
            </div>
            <div class="col-1" style="text-align: center;">
                <span id="with_irm_${index}" class="ms-1"></span>
            </div>
        <div class="col-2" style="text-align: center;padding-left: 20px;">
            <button type="button" class="btn btn-primary openUrl">
                <i class="fa fa-globe text-white"></i>
            </button>
            <button type="button" class="btn btn-danger removeRow">
                <i class="fa fa-trash"></i>
            </button>
        </div>
        </div>
    `);

    // Add to container
    container.append(row);

    // Attach event listeners
    const input = row.find(".url-input");

    // Validate + fetch on blur
    input.on("blur", function () {
        fetchPlatformDetailsSimple($(this));
    });

    // Validate live
    input.on("input", function () {
        validateUrlSimple($(this));
    });

    // If value already exists (edit mode)
    if (value.trim() !== "") {
        fetchPlatformDetailsSimple(input);
    }

    // Visit button
    row.find(".openUrl").on("click", function () {
        const url = input.val().trim();
        if (url) window.open(url.startsWith("http") ? url : "https://" + url, "_blank");
    });

    // Remove row
    row.find(".removeRow").on("click", function () {
        row.next("div.row").remove(); // remove the info row
        row.remove();
    });
}

// Validate URL
function validateUrlSimple(input) {
    const val = input.val().trim();
    const error = input.closest(".url-row").nextAll(".error-message").first();
    try {
        if (val && !/^https?:\/\//i.test(val)) {
            new URL("https://" + val);
        } else if (val) {
            new URL(val);
        }
        error.hide();
    } catch (e) {
        error.text("Invalid URL format.").show();
    }
}

// Fetch platform details
function fetchPlatformDetailsSimple(input) {
    let url = input.val().trim();
    if (!url) return;

    if (!/^https?:\/\//i.test(url)) {
        url = "https://" + url;
    }

    try {
        const parsed = new URL(url);
        url = parsed.hostname.toLowerCase().replace(/^www\./, "");
    } catch (e) {
        console.warn("Invalid URL format:", url);
        return;
    }

    const index = input.closest(".url-row").data("index");

    $.ajax({
        url: "{{ route('platform.details') }}",
        type: "POST",
        data: {
            base_url: url,
            _token: $('meta[name="csrf-token"]').attr("content"),
        },
        beforeSend: function () {
            $(`#platform_name_${index}`).text("Checking...");
            $(`#with_irm_${index}`).text("");
        },
        success: function (response) {
            $(`#platform_name_${index}`).text(response.platform_name || "");
            $(`#with_irm_${index}`).text(response.with_irm || "");
        },
        error: function (xhr) {
            console.error("Error fetching platform details:", xhr.responseText);
        },
    });
}

        $(document).on("input", ".url-input", function() {
            let url = $(this).val().trim();
            let errorMsg = $(this).closest(".url-row").next(".error-message");

            if (!url) {
                errorMsg.hide();
                return;
            }
            let pattern = /^https:\/\/(www\.)?[a-zA-Z0-9.-]+\.[a-zA-Z]{2,}.*$/;

            if (!pattern.test(url)) {
                errorMsg.text("Please enter a valid URL starting with https:// (e.g., https://www.example.com)").show();
            } else {
                errorMsg.hide();
            }
        });

        // $(document).on("click", ".openUrl", function() {
        //     let input = $(this).closest(".url-row").find(".url-input");
        //     let url = input.val().trim();
        //     let errorMsg = input.closest(".url-row").next(".error-message");

        //     if (!url) {
        //         errorMsg.text("This field cannot be empty").show();
        //         return;
        //     }

        //     let pattern = /^https:\/\/(www\.)?[a-zA-Z0-9.-]+\.[a-zA-Z]{2,}.*$/;
        //     if (!pattern.test(url)) {
        //         errorMsg.text("Invalid URL. Must start with https://").show();
        //         return;
        //     }

        //     errorMsg.hide();
        //     window.open(url, "_blank");
        // });

        $(document).on("click", ".removeRow", function() {
            $(this).closest(".url-row").remove();
            checkDuplicates();
        });
        $(document).on("input", "#urlFields input", function() {
            checkDuplicates();
        });
        function checkDuplicates() {
            let urls = [];
            let hasDuplicate = false;

            $("#urlFields input").each(function() {
                let val = $(this).val().trim();
                if (val !== "") {
                    if (urls.includes(val)) {
                        hasDuplicate = true;
                    } else {
                        urls.push(val);
                    }
                }
            });
            $("#duplicateMessage").remove();

            if (hasDuplicate) {
                $("#saveUrls").prop("disabled", true);
                $("#urlFields").append(`
                    <div id="duplicateMessage" class="text-danger mt-1">
                        Duplicate URL found. Please change or remove it.
                    </div>
                `);
            } else {
                $("#saveUrls").prop("disabled", false);
            }
        }
        function hasDuplicates(arr) {
            return new Set(arr).size !== arr.length;
        }
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
    // $('#addDocumentBtn').click();
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
        $('#saveadditionalPermits').prop('disabled', hasIncompleteRow());
    }
    $(document).on('input change', '.doc-name, .doc-file', checkButtonState);
    $(document).on('click', '.delete-btn', function () {
        $(this).closest('.document-row').remove();
        checkButtonState();
    });
});
</script>


    <script>
        const BASE_URL = "{{ url('/') }}";
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
</script>
    <script>
        $(document).ready(function() {
            const $issuedId = $('#issued_id');
            const $expirationVisible = $('#expirationDateInputVisible');
            const $expirationHidden = $('#expirationDateInput');

            function updateExpirationField() {
                const selected = $issuedId.find(':selected');
                const withExpiration = selected.attr('data-with-expiration');

                if (withExpiration === '1') {
                    $expirationVisible.prop('required', true);
                    $expirationVisible.prop('readonly', false).trigger('input'); // Trigger validation
                    if (!$expirationVisible.val()) {
                        $expirationVisible.val('');
                        $expirationHidden.val('');
                    }
                } else {
                    $expirationVisible.prop('required', false);
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
        $(document).ready(function () {
            function toggleBmbeDoc() {
                let selected = $("input[name='is_bmbe']:checked").val();
                let $docField = $("#bmbe_doc");
                let $busn_category_id = $("#busn_category_id");
                let $busn_valuation_doc = $("#busn_valuation_doc");

                if (selected == "1") {
                    $busn_category_id.data("previous", $busn_category_id.val());

                    $docField.prop("disabled", false).prop("required", true).removeAttr("readonly");
                    $busn_category_id.prop("disabled", true).prop("required", false).attr("readonly", true).val("");

                    $busn_valuation_doc.prop("disabled", true).prop("required", false).attr("readonly", true).val("");
                } else {
                    $docField.prop("disabled", true).prop("required", false).attr("readonly", true).val("");
                    $busn_category_id.prop("disabled", false).prop("required", true).removeAttr("readonly");
                    $busn_valuation_doc.prop("disabled", false).prop("required", true).removeAttr("readonly");
                    let previousValue = $busn_category_id.data("previous") || $busn_category_id.val();
                    $busn_category_id.val(previousValue);
                }
            }
            toggleBmbeDoc();
            $("input[name='is_bmbe']").on("change", function () {
                toggleBmbeDoc();
            });
        });



    </script>
    <script>
function updateFullNumber(input) {
    input.value = input.value.replace(/[^0-9]/g, '');
    document.getElementById('full_number').value = '+63' + input.value;
}
</script>