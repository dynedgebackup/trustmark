<div class="divider-line"></div>
    <h6 class="text-center multisteps-form__title"
        style="font-family: sans-serif;color: rgb(0,0,0);font-weight: bold;">
        Attachments
        @if ((Auth::user()->role == 2 && $isAdmin == 1) || (Auth::user()->id == $business->evaluator_id))
            <a href="javascript:void(0)" class="btn btn-primary edit-attachments"
                title="Make Edit" attr-id="{{ $business->id }}"
                style="float: inline-end;margin-right:20px;font-family:sans-serif;font-size:10px;padding: 1px 6px;border: none;background: #09325d;">
                <i class="fa fa-pencil"></i> Edit
            </a>
        @endif
    </h6>
    <div class="divider-line"></div>
        @php
            $documents = [
                'Business Registration' => [
                    'field' => 'docs_business_reg',
                    'checkfield' => 'doc_busnreg_is_compliance',
                    'type' => 'registration',
                ],
                'BIR 2303' => [
                    'field' => 'docs_bir_2303',
                    'checkfield' => 'doc_bir_is_compliance',
                    'type' => 'bir',
                ],
                'Internal Redress Mechanism' => [
                    'field' => 'docs_internal_redress',
                    'checkfield' => 'doc_irm_is_compliance',
                    'type' => 'redress',
                ],
            ];
        @endphp

        @foreach ($documents as $label => $info)
            @php $file = $business->{$info['field']}; @endphp
            <div class="row mb-2 align-items-center">
                <div class="col-md-3">
                    <label class="form-label custom-label mb-0">{{ $label }}:</label>
                </div>
                <div class="col-md-9">
                    @if (!empty($file))
                        <a href="{{ route('business.download_business_document', ['id' => $business->id, 'type' => $info['type']]) }}"
                            class="custom-label d-flex align-items-center gap-2"
                            title="{{ basename($file) }}" target="_blank">
                            @if ((Auth::user()->role == 2 && $isAdmin == 1) || (Auth::user()->id == $business->evaluator_id))
                                <input type="checkbox" name="{{ $info['checkfield'] }}" id="{{ $info['checkfield'] }}"
                                    class="compliance-check" data-busn="{{ $business->id }}"
                                    {{ optional($business_compliance)->{$info['checkfield']} == 1 ? 'checked' : '' }}>
                            @endif
                            <i class="custom-icon fa fa-download"></i>
                            <span>{{ basename($file) }}</span>
                        </a>
                    @else
                        @if ((Auth::user()->role == 2 && $isAdmin == 1) || (Auth::user()->id == $business->evaluator_id))
                            <input type="checkbox" name="{{ $info['checkfield'] }}" id="{{ $info['checkfield'] }}"
                                class="compliance-check" data-busn="{{ $business->id }}"
                                {{ optional($business_compliance)->{$info['checkfield']} == 1 ? 'checked' : '' }}>
                        @endif
                    @endif
                </div>
            </div>
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
            <a href="{{ route('business.download_bmbe_doc', ['id' => $business->id, 'type' => 'bmbe_doc']) }}?v={{ time() }}"
                class="custom-label d-flex align-items-center gap-2"
                title="{{ basename($business->bmbe_doc) }}"
                target="_blank">
                @if ((Auth::user()->role == 2 && $isAdmin == 1) || (Auth::user()->id == $business->evaluator_id))
                <input type="checkbox" name="doc_bmbe_is_compliance" id="doc_bmbe_is_compliance" class="compliance-check"  data-busn="{{ $business->id }}" {{ optional($business_compliance)->doc_bmbe_is_compliance == 1 ? 'checked' : '' }}>
                    @endif
                    {{ optional($business)->is_bmbe == 1 ? 'Yes' : 'No' }} <i class="custom-icon fa fa-download"></i>
                <span>{{ basename($business->bmbe_doc) }}</span>
                </a>
            @else
            @if ((Auth::user()->role == 2 && $isAdmin == 1) || (Auth::user()->id == $business->evaluator_id))
             <input type="checkbox" name="doc_bmbe_is_compliance" id="doc_bmbe_is_compliance" class="compliance-check"  data-busn="{{ $business->id }}" {{ optional($business_compliance)->doc_bmbe_is_compliance == 1 ? 'checked' : '' }}> <span>{{ optional($business)->is_bmbe == 1 ? 'Yes' : 'No' }}</span>
             @else
             <span>{{ optional($business)->is_bmbe == 1 ? 'Yes' : 'No' }}</span>
             @endif
            @endif
        </div>
    </div>
    
    <div class="row mb-2 align-items-center">
        <div class="col-md-3">
            <label class="form-label custom-label mb-0">Business Category (based on Asset Size):</label>
        </div>
        <div class="col-md-9">
        <span>
        @if ((Auth::user()->role == 2 && $isAdmin == 1) || (Auth::user()->id == $business->evaluator_id))
        <input type="checkbox" name="asset_category_is_compliance" id="asset_category_is_compliance" class="compliance-check"  data-busn="{{ $business->id }}" {{ optional($business_compliance)->asset_category_is_compliance == 1 ? 'checked' : '' }}>
        @endif
        {{ $businessCatName->busn_category_name ?? '' }}</span>
        </div>
    </div>
    <div class="row mb-2 align-items-center">
        <div class="col-md-3">
            <label class="form-label custom-label mb-0">Proof of Total Asset Valuation: </label>
        </div>
        <div class="col-md-9">
            @php
                $hasFile = !empty($business->busn_valuation_doc);
                $filename = basename($business->busn_valuation_doc);
                $shortName =
                    strlen($filename) > 15 ? substr($filename, 0, 15) . '...' : $filename;
            @endphp
            
            @if ($hasFile)
            <a href="{{ route('business.download_busn_valuation_doc', ['id' => $business->id, 'type' => 'busn_valuation_doc']) }}?v={{ time() }}"
                class="custom-label d-flex align-items-center gap-2"
                title="{{ basename($business->busn_valuation_doc) }}"
                target="_blank">
                @if ((Auth::user()->role == 2 && $isAdmin == 1) || (Auth::user()->id == $business->evaluator_id))
                <input type="checkbox" name="asset_valuation_is_compliance" id="asset_valuation_is_compliance" class="compliance-check"  data-busn="{{ $business->id }}" {{ optional($business_compliance)->asset_valuation_is_compliance == 1 ? 'checked' : '' }}>
                    @endif
                <i class="custom-icon fa fa-download"></i>
                <span>{{ basename($business->busn_valuation_doc) }}</span>
                </a>
                @else
                @if ((Auth::user()->role == 2 && $isAdmin == 1) || (Auth::user()->id == $business->evaluator_id))
                <input type="checkbox" name="asset_valuation_is_compliance" id="asset_valuation_is_compliance" class="compliance-check"  data-busn="{{ $business->id }}" {{ optional($business_compliance)->asset_valuation_is_compliance == 1 ? 'checked' : '' }}>
                @endif
            @endif
        </div>
    </div>
    <br>
    <div class="divider-line"></div>
    <h6 class="text-center multisteps-form__title"
        style="font-family: sans-serif;color: rgb(0,0,0);font-weight: bold;">
        Additional Permits (For Regulated Products) 
        @if ((Auth::user()->role == 2 && $isAdmin == 1) || (Auth::user()->id == $business->evaluator_id))
        <input type="checkbox" name="doc_addpermit_is_compliance" id="doc_addpermit_is_compliance" class="compliance-check"  data-busn="{{ $business->id }}" {{ optional($business_compliance)->doc_addpermit_is_compliance == 1 ? 'checked' : '' }}>
        @endif
        @if ((Auth::user()->role == 2 && $isAdmin == 1) || (Auth::user()->id == $business->evaluator_id))
            <a href="javascript:void(0)" class="btn btn-primary edit-additionalpermits"
                title="Make Edit" data-id="{{ $business->id }}"
                style="float: inline-end;margin-right:20px;font-family:sans-serif;font-size:10px;padding: 1px 6px;border: none;background: #09325d;">
                <i class="fa fa-pencil"></i> Edit
            </a>
        @endif
    </h6>
    <div class="divider-line"></div>

    @foreach ($AdditionalDocuments as $doc)
        <div class="row mb-2 align-items-center">
            <div class="col-md-3">
                <label class="form-label custom-label mb-0">{{ $doc->name }}</label>
            </div>
            <div class="col-md-9">
                <a href="{{ route('business.download_AdditionalDocuments', ['id' => $doc->id, 'AdditionalDocuments']) }}"
                    target="_blank" class="custom-label d-flex align-items-center gap-2"
                    title="{{ $doc->attachment }}">
                    <i class="custom-icon fa fa-download"></i>
                    <span>{{ basename($doc->attachment) }}</span>
                </a>
            </div>
        </div>
    @endforeach