
                                    <div class="divider-line" style="margin-bottom: 8px !important;"></div>
                                    <h6 class="text-center multisteps-form__title"
                                        style="font-family: sans-serif;color: rgb(0,0,0);font-weight: bold;">
                                        Business Information
                                        @if ((Auth::user()->role == 2 && $isAdmin == 1) || (Auth::user()->id == $business->evaluator_id))
                                            <a href="javascript:void(0)" class="btn btn-primary edit-information"
                                                title="Make Edit" attr-id="{{ $business->id }}"
                                                style="float: inline-end;margin-right:20px;font-family:sans-serif;font-size:10px;padding: 1px 6px;border: none;background: #09325d;">
                                                <i class="fa fa-pencil"></i> Edit
                                            </a>
                                        @endif
                                    </h6>
                                    <div class="divider-line"></div>
                                    <div class="col-md-3">
                                        <label class="form-label">Business Type :&nbsp;</label>
                                    </div>
                                    <div class="col-md-9">
                                     <span> 
                                     @if ((Auth::user()->role == 2 && $isAdmin == 1) || (Auth::user()->id == $business->evaluator_id))
                                            <input type="checkbox"
                                                name="busn_type_is_compliance"
                                                id="busn_type_is_compliance"
                                                class="compliance-check"
                                                data-busn="{{ $business->id }}"
                                                {{ optional($business_compliance)->busn_type_is_compliance == 1 ? 'checked' : '' }}>
                                        @endif {{ $business->corporationType->name ?? 'N/A' }}</span>
                                    </div>

                                    <div class="col-md-3">
                                        <label class="form-label">Business Name :&nbsp;</label>
                                    </div>
                                    <div class="col-md-9">
                                        <span>
                                        @if ((Auth::user()->role == 2 && $isAdmin == 1) || (Auth::user()->id == $business->evaluator_id))
                                        <input type="checkbox" name="busn_name_is_compliance" id="busn_name_is_compliance" class="compliance-check"  data-busn="{{ $business->id }}" {{ optional($business_compliance)->busn_name_is_compliance == 1 ? 'checked' : '' }}>
                                        @endif
                                         {{ $business->business_name ?? 'N/A' }}</span>
                                        @if (Auth::user()->role == 2)
                                            <a href="javascript:void(0)" class="btn btn-primary check-business-name"
                                                title="Make Check Records" attr-id="{{ $business->id }}"
                                                style="margin-right:20px;font-family:sans-serif;font-size:10px;padding: 1px 6px;border: none;background: #09325d;">
                                                <i class="fa fa-file"></i> Check Records
                                            </a>
                                        @endif
                                    </div>
                                    <div class="col-md-3">
                                        <label class="form-label">Trade Name :&nbsp;</label>
                                    </div>
                                    <div class="col-md-9">
                                        <span> 
                                        @if ((Auth::user()->role == 2 && $isAdmin == 1) || (Auth::user()->id == $business->evaluator_id))
                                        <input type="checkbox" name="busn_trade_is_compliance" id="busn_trade_is_compliance" 
                                        class="compliance-check"  data-busn="{{ $business->id }}" {{ optional($business_compliance)->busn_trade_is_compliance == 1 ? 'checked' : '' }}>
                                        @endif {{ $business->franchise ?? 'N/A' }}</span>
                                    </div>
                                    <div class="col-md-3">
                                        <label class="form-label">Business Category :</label>
                                    </div>
                                    <div class="col-md-9">
                                        <span>@if ((Auth::user()->role == 2 && $isAdmin == 1) || (Auth::user()->id == $business->evaluator_id))
                                            <input type="checkbox" name="busn_category_is_compliance" id="busn_category_is_compliance" class="compliance-check"  data-busn="{{ $business->id }}" {{ optional($business_compliance)->busn_category_is_compliance == 1 ? 'checked' : '' }}>
                                            @endif {{ $business->category->name ?? 'N/A' }}</span>
                                    </div>
                                    @if ($business->category_other_description)
                                        <div class="col-md-3">
                                            <label class="form-label">Description :</label>
                                        </div>
                                        <div class="col-md-9">
                                            <span>{{ $business->category_other_description ?? 'N/A' }}</span>
                                        </div>
                                    @endif
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
                                    <div class="col-md-3">
                                        <label class="form-label">{{ $regLabel }}:&nbsp;</label>
                                    </div>
                                    <div class="col-md-9">
                                        <span>@if ((Auth::user()->role == 2 && $isAdmin == 1) || (Auth::user()->id == $business->evaluator_id))
                                            <input type="checkbox" name="busn_regno_is_compliance" id="busn_regno_is_compliance" class="compliance-check"  data-busn="{{ $business->id }}" {{ optional($business_compliance)->busn_regno_is_compliance == 1 ? 'checked' : '' }}>
                                            @endif {{ $business->reg_num ?? 'N/A' }}</span>
                                        @if (Auth::user()->role == 2)
                                            <a href="javascript:void(0)"
                                                class="btn btn-primary check-business-registration"
                                                title="Make Check Records" attr-id="{{ $business->id }}"
                                                style="margin-right:20px;font-family:sans-serif;font-size:10px;padding: 1px 6px;border: none;background: #09325d;">
                                                <i class="fa fa-file"></i> Check Records
                                            </a>
                                        @endif
                                    </div>
                                    <div class="col-md-3">
                                        <label class="form-label">Tax Identification Number (TIN) :</label>
                                    </div>
                                    <div class="col-md-9">
                                        <span> @if ((Auth::user()->role == 2 && $isAdmin == 1) || (Auth::user()->id == $business->evaluator_id))
                                            <input type="checkbox" name="tin_is_compliance" id="tin_is_compliance" class="compliance-check"  data-busn="{{ $business->id }}" {{ optional($business_compliance)->tin_is_compliance == 1 ? 'checked' : '' }}> 
                                            @endif {{ $business->tin ?? 'N/A' }}</span>
                                        @if (Auth::user()->role == 2)
                                            <a href="javascript:void(0)" class="btn btn-primary check-records"
                                                title="Make Check Records" attr-id="{{ $business->id }}"
                                                style="margin-right:20px;font-family:sans-serif;font-size:10px;padding: 1px 6px;border: none;background: #09325d;">
                                                <i class="fa fa-file"></i> Check Records
                                            </a>
                                        @endif
                                    </div>
                                </div>
                                <!-- <div class="row">
                                                                                            
                                                                                            
                                                                                        </div>
                                                                                        <div class="row">
                                                                                            
                                                                                        </div> -->

                                <br>
                                <div class="divider-line" style="margin-bottom: 8px !important;"></div>
                                <h6 class="text-center multisteps-form__title"
                                    style="font-family: sans-serif;color: rgb(0,0,0);font-weight: bold;">
                                    Business URL | Website | Social Media Platform Link 
                                    @if ((Auth::user()->role == 2 && $isAdmin == 1) || (Auth::user()->id == $business->evaluator_id)) 
                                    <input type="checkbox" name="url_is_compliance" id="url_is_compliance" class="compliance-check"  data-busn="{{ $business->id }}" {{ optional($business_compliance)->url_is_compliance == 1 ? 'checked' : '' }}>
                                    @endif
                                    @if ((Auth::user()->role == 2 && $isAdmin == 1) || (Auth::user()->id == $business->evaluator_id))
                                        <a href="javascript:void(0)" class="btn btn-primary edit-url" title="Make Edit"
                                            attr-id="{{ $business->id }}"
                                            style="float: inline-end;margin-right:20px;font-family:sans-serif;font-size:10px;padding: 1px 6px;border: none;background: #09325d;">
                                            <i class="fa fa-pencil"></i> Edit
                                        </a>
                                    @endif
                                </h6>
                                <div class="divider-line"></div>
                                <div class="row" style="padding:0px;">
                                    <div class="col-md-12" style="padding:0px 7px;">
                                        <!-- <label class="form-label">Business URL/Website/Social Media Platform Link
                                                                                                        :&nbsp;</label> -->
                                        @if (!empty($business->url_platform) && is_array($business->url_platform))
                                            @foreach ($business->url_platform as $url)
                                                @if (!empty($url))
                                                    <a href="{{ $url }}" class="custom-label" target="_blank"
                                                        rel="noopener noreferrer" title="{{ $url }}">
                                                        {{ \Illuminate\Support\Str::limit($url, 150) }}
                                                    </a><br>
                                                @endif
                                            @endforeach
                                        @else
                                            <span class="custom-label">N/A</span>
                                        @endif
                                    </div>
                                </div>

                                <br>
                                <div class="divider-line"></div>
                                <h6 class="text-center multisteps-form__title"
                                    style="font-family: sans-serif;color: rgb(0,0,0);font-weight: bold;">
                                    Authorized Representative
                                    @if ((Auth::user()->role == 2 && $isAdmin == 1) || (Auth::user()->id == $business->evaluator_id))
                                        <a href="javascript:void(0)" class="btn btn-primary edit-authorizedRepresentative"
                                            title="Make Edit" attr-id="{{ $business->id }}"
                                            style="float: inline-end;margin-right:20px;font-family:sans-serif;font-size:10px;padding: 1px 6px;border: none;background: #09325d;">
                                            <i class="fa fa-pencil"></i> Edit
                                        </a>
                                    @endif
                                </h6>
                                <div class="divider-line"></div>
                                <br>

                                <div class="row">
                                    <div class="col-md-3">
                                        <label class="form-label">Name :&nbsp;</label>
                                    </div>
                                    <div class="col-md-9">
                                        <span>@if ((Auth::user()->role == 2 && $isAdmin == 1) || (Auth::user()->id == $business->evaluator_id))
                                            <input type="checkbox" name="authrep_name_is_compliance" id="authrep_name_is_compliance" class="compliance-check"  data-busn="{{ $business->id }}" {{ optional($business_compliance)->authrep_name_is_compliance == 1 ? 'checked' : '' }}> 
                                            @endif {{ $business->pic_name ?? 'N/A' }}</span>
                                    </div>
                                    <div class="col-md-3">
                                        <label class="form-label">Mobile No. :&nbsp;</label>
                                    </div>
                                    <div class="col-md-9">
                                        <span>@if ((Auth::user()->role == 2 && $isAdmin == 1) || (Auth::user()->id == $business->evaluator_id))
                                            <input type="checkbox" name="authrep_mobile_is_compliance" id="authrep_mobile_is_compliance" class="compliance-check"  data-busn="{{ $business->id }}" {{ optional($business_compliance)->authrep_mobile_is_compliance == 1 ? 'checked' : '' }}> 
                                            @endif {{ $business->pic_ctc_no ?? 'N/A' }} </span>
                                    </div>
                                    <div class="col-md-3">
                                        <label class="form-label">Email :</label>
                                    </div>
                                    <div class="col-md-9">
                                        <span>@if ((Auth::user()->role == 2 && $isAdmin == 1) || (Auth::user()->id == $business->evaluator_id))
                                            <input type="checkbox" name="authrep_email_is_compliance" id="authrep_email_is_compliance" class="compliance-check"  data-busn="{{ $business->id }}" {{ optional($business_compliance)->authrep_email_is_compliance == 1 ? 'checked' : '' }}>
                                            @endif {{ $business->pic_email ?? 'N/A' }}</span>
                                    </div>
                                </div>

                                <div class="row">
                                    <div class="col-md-3">
                                        <label class="form-label">Government Issued ID :&nbsp;</label>
                                    </div>
                                    <div class="col-md-9">
                                        <span> @if ((Auth::user()->role == 2 && $isAdmin == 1) || (Auth::user()->id == $business->evaluator_id))
                                            <input type="checkbox" name="authrep_govtid_is_compliance" id="authrep_govtid_is_compliance" class="compliance-check"  data-busn="{{ $business->id }}" {{ optional($business_compliance)->authrep_govtid_is_compliance == 1 ? 'checked' : '' }}>
                                            @endif {{ $business->requirement->description ?? 'N/A' }}</span>
                                    </div>
                                    <div class="col-md-3">
                                        <label class="form-label">Attachment :&nbsp;</label>
                                    </div>
                                    <div class="col-md-9">
                                    @php
                                        $filePath = $business?->requirement_upload ?? $user?->requirement_upload;
                                        if ($business?->requirement_upload) {
                                            $fileRoute = route('business.download_authorized', encrypt($business->id));
                                        } elseif ($user?->requirement_upload) {
                                            $fileRoute = route('profile.download_authorized', $user->id);
                                        } else {
                                            $fileRoute = null;
                                        }
                                        $filename = $filePath ? basename($filePath) : '';
                                    @endphp

                                    @if ($filePath && $fileRoute)
                                        <span>
                                            <a href="{{ $fileRoute }}"
                                            class="d-flex align-items-center gap-2"
                                            target="_blank"> 
                                            @if ((Auth::user()->role == 2 && $isAdmin == 1) || (Auth::user()->id == $business->evaluator_id)) <input type="checkbox" name="authrep_govtid_doc_is_compliance" id="authrep_govtid_doc_is_compliance" class="compliance-check"  data-busn="{{ $business->id }}" {{ optional($business_compliance)->authrep_govtid_doc_is_compliance == 1 ? 'checked' : '' }}> 
                                            @endif <i class="fa fa-download"></i>
                                                <span class="custom-label" title="{{ $filename }}"> {{ $filename }}</span>
                                            </a>
                                        </span>
                                    @else
                                    @if ((Auth::user()->role == 2 && $isAdmin == 1) || (Auth::user()->id == $business->evaluator_id))
                                    <input type="checkbox" name="authrep_govtid_doc_is_compliance" id="authrep_govtid_doc_is_compliance" class="compliance-check"  data-busn="{{ $business->id }}" {{ optional($business_compliance)->authrep_govtid_doc_is_compliance == 1 ? 'checked' : '' }}>
                                    @endif
                                    @endif
                                    </div>
                                    <div class="col-md-3">
                                        <label class="form-label">Expiry Date :</label>
                                    </div>
                                    <div class="col-md-9">
                                        <span>
                                        @if ((Auth::user()->role == 2 && $isAdmin == 1) || (Auth::user()->id == $business->evaluator_id))
                                        <input type="checkbox" name="authrep_govtid_expiry_is_compliance" id="authrep_govtid_expiry_is_compliance" class="compliance-check"  data-busn="{{ $business->id }}" {{ optional($business_compliance)->authrep_govtid_expiry_is_compliance == 1 ? 'checked' : '' }}>
                                        @endif
                                         {{ $business->requirement_expired ? formatDatePH($business->requirement_expired) : 'N/A' }}</span>
                                    </div>
                               