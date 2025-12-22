@extends('layouts.app')

@section('content')
    <div class="d-flex justify-content-between align-items-center mb-4" style="margin-top: 15px;">
        <h3 class="page-title">VIEW BUSINESS</h3>
    </div>
    
    <ol class="breadcrumb custom-breadcrumb">
        <li class="breadcrumb-item"><a href="{{ route('dashboard') }}"><span>Dashboard</span></a></li>
        <li class="breadcrumb-item"><a href="{{ route('business.index') }}"><span>Business List</span></a></li>
        <li class="breadcrumb-item"><a href="#"><span>Details</span></a></li>
    </ol>
    <style>
        .text-center {
        text-align: left !important;
        }
    </style>
    <div id="wrapper">
        <div class="d-flex flex-column" id="content-wrapper">
            <div class="card">
                <div class="card-body">
                    <ul class="nav nav-tabs custom-tab-list" role="tablist">
                        @if ($business->status == 'APPROVED' && $business->payment_id != null)
                            <li class="nav-item" role="presentation">
                                <a class="nav-link active custom-tab-link" role="tab" data-bs-toggle="tab"
                                    href="#tab-qr-code">QR Code</a>
                            </li>
                            <li class="nav-item" role="presentation">
                                <a class="nav-link custom-tab-link" role="tab" data-bs-toggle="tab"
                                    href="#tab-certificate">Certificate</a>
                            </li>
                            <li class="nav-item" role="presentation">
                                <a class="nav-link custom-tab-link" role="tab" data-bs-toggle="tab"
                                    href="#tab-info">Information</a>
                            </li>
                            <li class="nav-item" role="presentation">
                                <a class="nav-link custom-tab-link" role="tab" data-bs-toggle="tab"
                                    href="#tab-corporations">Business Registration</a>
                            </li>
                        @else
                            <li class="nav-item" role="presentation">
                                <a class="nav-link active custom-tab-link" role="tab" data-bs-toggle="tab"
                                    href="#tab-info">Information</a>
                            <li class="nav-item" role="presentation">
                                <a class="nav-link custom-tab-link" role="tab" data-bs-toggle="tab"
                                    href="#tab-corporations">Business Registration</a>
                            </li>
                        @endif

                        <li class="nav-item" role="presentation">
                            <a class="nav-link custom-tab-link" role="tab" data-bs-toggle="tab"
                                href="#tab-detail">Business Address</a>
                        </li>
                        <li class="nav-item" role="presentation">
                            <a class="nav-link custom-tab-link" role="tab" data-bs-toggle="tab"
                                href="#tab-document">Documents</a>
                        </li>
                        <li class="nav-item" role="presentation">
                            <a class="nav-link custom-tab-link" role="tab" data-bs-toggle="tab"
                                href="#tab-remarks">Remarks</a>
                        </li>
                        @if (Auth::user()->role == 2)
                            <li class="nav-item" role="presentation">
                                <a class="nav-link custom-tab-link" role="tab" data-bs-toggle="tab"
                                    href="#tab-audit-trail">Audit Trail</a>
                            </li>
                            <li class="nav-item" role="presentation">
                                <a class="nav-link custom-tab-link" role="tab" data-bs-toggle="tab"
                                    href="#tab-follow-up-email">Follow-up Emails</a>
                            </li>
                        @endif
                    </ul>

                    <div class="tab-content">
                        @if ($business->status == 'APPROVED' && $business->payment_id != null)
                            <div class="tab-pane active" role="tabpanel" id="tab-qr-code">
                                <div class="row custom-row">
                                    <div class="col custom-col">
                                        <div class="mb-3">
                                            <div class="d-flex justify-content-center">
                                                <div class="border rounded p-4 bg-white text-center"
                                                    style="width: fit-content;">
                                                    <div class="container">
                                                        <div class="row">
                                                            <div class="col" style="text-align: center;">
                                                                <div
                                                                    class="d-flex justify-content-center align-items-start gap-3">
                                                                    <div class="text-center">
                                                                        <img src="{{ asset('assets/img/TRUSTMARK-SHIELD.png') }}"
                                                                            width="160" height="190">
                                                                    </div>
                                                                    <div
                                                                        class="text-center d-flex flex-column align-items-center">
                                                                        @php
                                                                            $filename = str_replace('storage/', '', $business->qr_code);
                                                                            $filepath = asset('storage/app/public/' . $filename);

                                                                            $fileSystemPath = public_path('storage/' . $filename);
                                                                            if(file_exists($fileSystemPath)){
                                                                                $filepath = asset('storage/' . $filename);
                                                                            }
                                                                        @endphp
                                                                        
                                                                        <img src="{{ $filepath }}"
                                                                            width="145" height="145">
                                                                        <img class="mt-0"
                                                                            src="{{ asset('assets/img/TRUSTMARK-REGISTERED-ONLY.png') }}"
                                                                            width="133" height="32"
                                                                            style="margin-top: -8px;">
                                                                        <p class="mb-0"
                                                                            style="font-size: 19px;color:black">
                                                                            {{ $business->trustmark_id }}</p>
                                                                    </div>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div>
                                                    <a href="#"  id="downloadQrBtn" style="width: 100%;"
                                                        class="btn btn-primary mb-3">
                                                        <i class="fa fa-download"></i> Download QR as PNG
                                                    </a>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            

                            <div class="tab-pane" role="tabpanel" id="tab-certificate">
                                <div class="row custom-row">
                                    <div class="col custom-col">
                                        <div class="mb-3">
                                            <div style="text-align: center;padding-top: 15px;">
                                                <a href="{{ route('business.download_certificate', $business->id) }}?v={{ time() }}"
                                                    class="btn btn-primary mb-3">
                                                    <i class="fa fa-download"></i> Download Certificate
                                                </a>
                                                @php
                                                    $UserRole = \App\Models\User::where('id', Auth::id())->first();
                                                @endphp

                                                @if($UserRole->role == 2)
                                                <a href="{{ route('business.certReGenerate', $business->id) }}"
                                                    class="btn btn-primary mb-3"
                                                    onclick="event.preventDefault(); confirmReGenerate('{{ route('business.certReGenerate', $business->id) }}');">
                                                    <i class="fa fa-certificate"></i> Re-Generate
                                                </a>
                                                @endif
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            
                            <div class="tab-pane" role="tabpanel" id="tab-info">
                                <div class="row">
                                    <div class="col-md-3">
                                            <label class="form-label">Security No. :&nbsp;</label>
                                    </div>
                                    <div class="col-md-9">
                                            <span>{{ $business->trustmark_id ?? 'N/A' }}</span>
                                    </div>
                                    <div class="col-md-3">
                                            <label class="form-label">Approved Date
                                                :&nbsp;</label>
                                    </div>
                                    <div class="col-md-9">
                                            <span>{{ $business->date_issued ? \Carbon\Carbon::parse($business->date_issued)->format('F j, Y') : 'N/A' }}</span>
                                    </div>
                                    <div class="col-md-3">
                                            <label class="form-label">Expired Date
                                                :&nbsp;</label>
                                    </div>
                                    <div class="col-md-9">
                                            <span>{{ $business->expired_date ? \Carbon\Carbon::parse($business->expired_date)->format('F j, Y') : 'N/A' }}</span>
                                    </div>
                                    
                                </div>
                            </div>

                            <div class="tab-pane" role="tabpanel" id="tab-corporations">
                                <div class="row">
                                    <div class="col-md-3">
                                            <label class="form-label">Business Type :&nbsp;</label>
                                    </div>
                                    <div class="col-md-9">
                                            <span>{{ $business->corporationType->name ?? 'N/A' }}</span>
                                    </div>
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
                                            <span>{{ $business->reg_num ?? 'N/A' }}</span>
                                    </div>
                                    <div class="col-md-3">
                                            <label class="form-label">Tax Identification Number (TIN) :</label>
                                    </div>
                                    <div class="col-md-9">
                                            <span>{{ $business->tin ?? 'N/A' }}</span>
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="col-md-3">
                                            <label class="form-label">Business Name :&nbsp;</label>
                                    </div>
                                    <div class="col-md-9">
                                            <span>{{ $business->business_name ?? 'N/A' }}</span>
                                    </div>
                                    <div class="col-md-3">
                                            <label class="form-label">Trade Name :&nbsp;</label>
                                    </div>
                                    <div class="col-md-9">
                                            <span>{{ $business->franchise ?? 'N/A' }}</span>
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="col-md-3">
                                            <label class="form-label">Business Category :</label>
                                    </div>
                                    <div class="col-md-9">
                                            <span>{{ $business->category->name ?? 'N/A' }}</span>
                                    </div>
                                    @if ($business->category_other_description)
                                        <div class="col-md-3">
                                                <label class="form-label">Description :</label>
                                        </div>
                                        <div class="col-md-9">
                                                <span>{{ $business->category_other_description ?? 'N/A' }}</span>
                                        </div>
                                    @endif
                                </div>

                                <br>
                                <h6 class="text-center multisteps-form__title"
                                    style="font-family: sans-serif;color: rgb(0,0,0);font-weight: bold;">
                                    Business URL/Website/Social Media Platform Link</h6>
                                <div class="divider-line"></div>
                                <div class="row" style="padding:0px;">
                                    <div class="col-md-12" style="padding:0px 7px;">
                                            <!-- <label class="form-label">Business URL/Website/Social Media Platform Link
                                                :&nbsp;</label> -->
                                            @if (!empty($business->url_platform) && is_array($business->url_platform))
                                                @foreach ($business->url_platform as $url)
                                                    @if (!empty($url))
                                                        <a href="{{ $url }}" class="custom-label"
                                                            target="_blank" rel="noopener noreferrer"
                                                            title="{{ $url }}">
                                                            {{ \Illuminate\Support\Str::limit($url, 150) }}
                                                        </a>
                                                    @endif
                                                @endforeach
                                            @else
                                                <span class="custom-label">N/A</span>
                                            @endif
                                    </div>
                                </div>

                                <br>
                                <h6 class="text-center multisteps-form__title"
                                    style="font-family: sans-serif;color: rgb(0,0,0);font-weight: bold;">
                                    Authorized Representative</h6>
                                <div class="divider-line"></div>
                                <br>

                                <div class="row">
                                    <div class="col-md-3">
                                            <label class="form-label">Name :&nbsp;</label>
                                    </div>
                                    <div class="col-md-9">
                                            <span>{{ $business->pic_name ?? 'N/A' }}</span>
                                    </div>
                                    <div class="col-md-3">
                                            <label class="form-label">Mobile No. :&nbsp;</label>
                                    </div>
                                    <div class="col-md-9">
                                            <span>{{ $business->pic_ctc_no ?? 'N/A' }}</span>
                                    </div>
                                    <div class="col-md-3">
                                            <label class="form-label">Email :</label>
                                    </div>
                                    <div class="col-md-9">
                                            <span>{{ $business->pic_email ?? 'N/A' }}</span>
                                    </div>
                                </div>

                                <div class="row">
                                    <div class="col-md-3">
                                            <label class="form-label">Government Issued ID :&nbsp;</label>
                                    </div>
                                    <div class="col-md-9">
                                            <span>{{ $business->requirement->description ?? 'N/A' }}</span>
                                    </div>
                                    <div class="col-md-3">
                                            <label class="form-label">Attachment :&nbsp;</label>
                                            
                                    </div>
                                    <div class="col-md-9">
                                    @php
                                        $filePath = !empty($business->requirement_upload)
                                            ? $business->requirement_upload
                                            : $user->requirement_upload;

                                        $fileRoute = !empty($business->requirement_upload)
                                            ? route('business.download_authorized', encrypt($business->id))
                                            : route('profile.download_authorized', $user->id);

                                        $filename = $filePath ? basename($filePath) : '';
                                    @endphp

                                    @if ($filePath)
                                        <span>
                                            <a href="{{ $fileRoute }}"
                                            class="d-flex align-items-center gap-2"
                                            target="_blank"> 
                                                <i class="fa fa-download"></i>
                                                <span class="custom-label" title="{{ $filename }}">{{ $filename }}</span>
                                            </a>
                                        </span>
                                    @endif
                                    </div>
                                    <div class="col-md-3">
                                            <label class="form-label">Expiry Date :</label>
                                            
                                    </div>
                                    <div class="col-md-9">
                                            <span>{{ $business->requirement_expired ? formatDatePH($business->requirement_expired) : 'N/A' }}</span>
                                    </div>
                                </div>
                            </div>
                        @else
                            <div class="tab-pane active" role="tabpanel" id="tab-info">
                                <div class="row">
                                    <div class="col-md-3">
                                            <label class="form-label">Security No. :&nbsp;</label>
                                    </div>
                                    <div class="col-md-9">
                                            <span>{{ $business->trustmark_id ?? 'N/A' }}</span>
                                    </div>
                                    <div class="col-md-3">
                                            <label class="form-label">Expired Date
                                                :&nbsp;</label>
                                            
                                    </div>
                                    <div class="col-md-9">
                                            <span>{{ $business->expired_date ? formatDatePH($business->expired_date) : 'N/A' }}</span>
                                    </div>
                                    <div class="col-md-3">
                                            <label class="form-label">Approved Date
                                                :&nbsp;</label>
                                    </div>
                                    <div class="col-md-9">
                                    <span>{{ $business->date_issued ? \Carbon\Carbon::parse($business->date_issued)->format('F j, Y') : 'N/A' }}</span>
                                    </div>
                                </div>
                            </div>
                            <div class="tab-pane" role="tabpanel" id="tab-corporations">
                                <div class="row">
                                    <div class="col-md-3">
                                            <label class="form-label">Business Type :&nbsp;</label>
                                    </div>
                                    <div class="col-md-9">
                                            <span>{{ $business->corporationType->name ?? 'N/A' }}</span>
                                    </div>
                                    
                                    <div class="col-md-3">
                                            <label class="form-label">Business Name :&nbsp;</label>
                                    </div>
                                    <div class="col-md-9">
                                            <span>{{ $business->business_name ?? 'N/A' }}</span>
                                    </div>
                                    <div class="col-md-3">
                                            <label class="form-label">Trade Name :&nbsp;</label>
                                    </div>
                                    <div class="col-md-9">
                                            <span>{{ $business->franchise ?? 'N/A' }}</span>
                                    </div>
                                    <div class="col-md-3">
                                            <label class="form-label">Business Category :</label>
                                    </div>
                                    <div class="col-md-9">
                                            <span>{{ $business->category->name ?? 'N/A' }}</span>
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
                                            <span>{{ $business->reg_num ?? 'N/A' }}</span>
                                    </div>
                                    <div class="col-md-3">
                                            <label class="form-label">Tax Identification Number (TIN) :</label>
                                    </div>
                                    <div class="col-md-9">
                                            <span>{{ $business->tin ?? 'N/A' }}</span>
                                    </div>
                                </div>
                                <!-- <div class="row">
                                    
                                    
                                </div>
                                <div class="row">
                                    
                                </div> -->

                                <br>
                                <h6 class="text-center multisteps-form__title"
                                    style="font-family: sans-serif;color: rgb(0,0,0);font-weight: bold;">
                                    Business URL/Website/Social Media Platform Link</h6>
                                <div class="divider-line"></div>
                                <div class="row" style="padding:0px;">
                                    <div class="col-md-12" style="padding:0px 7px;">
                                            <!-- <label class="form-label">Business URL/Website/Social Media Platform Link
                                                :&nbsp;</label> -->
                                            @if (!empty($business->url_platform) && is_array($business->url_platform))
                                                @foreach ($business->url_platform as $url)
                                                    @if (!empty($url))
                                                        <a href="{{ $url }}" class="custom-label"
                                                            target="_blank" rel="noopener noreferrer"
                                                            title="{{ $url }}">
                                                            {{ \Illuminate\Support\Str::limit($url, 150) }}
                                                        </a>
                                                    @endif
                                                @endforeach
                                            @else
                                                <span class="custom-label">N/A</span>
                                            @endif
                                    </div>
                                </div>

                                <br>
                                <h6 class="text-center multisteps-form__title"
                                    style="font-family: sans-serif;color: rgb(0,0,0);font-weight: bold;">
                                    Authorized Representative</h6>
                                <div class="divider-line"></div>
                                <br>

                                <div class="row">
                                    <div class="col-md-3">
                                            <label class="form-label">Name :&nbsp;</label>
                                    </div>
                                    <div class="col-md-9">
                                            <span>{{ $business->pic_name ?? 'N/A' }}</span>
                                    </div>
                                    <div class="col-md-3">
                                            <label class="form-label">Mobile No. :&nbsp;</label>
                                    </div>
                                    <div class="col-md-9">
                                            <span>{{ $business->pic_ctc_no ?? 'N/A' }}</span>
                                    </div>
                                    <div class="col-md-3">
                                            <label class="form-label">Email :</label>
                                    </div>
                                    <div class="col-md-9">
                                            <span>{{ $business->pic_email ?? 'N/A' }}</span>
                                    </div>
                                </div>

                                <div class="row">
                                    <div class="col-md-3">
                                            <label class="form-label">Government Issued ID :&nbsp;</label>
                                    </div>
                                    <div class="col-md-9">
                                            <span>{{ $business->requirement->description ?? 'N/A' }}</span>
                                    </div>
                                    <div class="col-md-3">
                                            <label class="form-label">Attachment :&nbsp;</label>
                                    </div>
                                    <div class="col-md-9">
                                    @php
                                        $filePath = !empty($business->requirement_upload)
                                            ? $business->requirement_upload
                                            : $user->requirement_upload;

                                        $fileRoute = !empty($business->requirement_upload)
                                            ? route('business.download_authorized', encrypt($business->id))
                                            : route('profile.download_authorized', $user->id);

                                        $filename = $filePath ? basename($filePath) : '';
                                    @endphp

                                    @if ($filePath)
                                        <span>
                                            <a href="{{ $fileRoute }}"
                                            class="d-flex align-items-center gap-2"
                                            target="_blank"> 
                                                <i class="fa fa-download"></i>
                                                <span class="custom-label" title="{{ $filename }}">{{ $filename }}</span>
                                            </a>
                                        </span>
                                    @endif
                                    </div>
                                    <div class="col-md-3">
                                            <label class="form-label">Expiry Date :</label>
                                    </div>
                                    <div class="col-md-9">
                                            <span>{{ $business->requirement_expired ? formatDatePH($business->requirement_expired) : 'N/A' }}</span>
                                    </div>
                                </div>
                            </div>
                        @endif

                        <div class="tab-pane" role="tabpanel" id="tab-detail">
                            <div class="row">
                                <div class="col-md-3">
                                        <label class="form-label">Complete Address :&nbsp;</label>
                                </div>
                                <div class="col-md-9">
                                        <span>{{ $business->complete_address }}</span>
                                </div>
                                <div class="col-md-3">
                                        <label class="form-label">Region :&nbsp;</label>
                                </div>
                                <div class="col-md-9">
                                        <span>{{ $business->region->reg_region ?? 'N/A' }}</span>
                                </div>
                                <div class="col-md-3">
                                        <label class="form-label">Province :&nbsp;</label>
                                </div>
                                <div class="col-md-9">
                                        <span>{{ $business->province->prov_desc ?? 'N/A' }}</span>
                                </div>
                                <div class="col-md-3">
                                        <label class="form-label">Municipality :</label>
                                </div>
                                <div class="col-md-9">
                                        <span>{{ $business->municipality->mun_desc ?? 'N/A' }}</span>
                                </div>
                                <div class="col-md-3">
                                        <label class="form-label">Barangay :&nbsp;</label>
                                </div>
                                <div class="col-md-9">
                                        <span>{{ $business->barangay->brgy_name ?? 'N/A' }}</span>
                                </div>
                            </div>
                        </div>

                        <div class="tab-pane" role="tabpanel" id="tab-document">

                            @php
                                $documents = [
                                    'Business Registration' => [
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
                                                class="custom-label d-flex align-items-center gap-2"
                                                title="{{ basename($file) }}"
                                                target="_blank">
                                                <i class="custom-icon fa fa-download"></i>
                                                <!-- <span>{{ Str::limit(basename($file), 20) }}</span> -->
                                                <span>{{ $file }}</span>
                                            </a>

                                        </div>
                                    </div>
                                @endif
                            @endforeach
                            <br>
                            <div class="divider-line"></div>
                            <h4 class="text-left multisteps-form__title"
                                style="font-family: sans-serif;font-size: 15px;color: rgb(0,0,0);font-weight: bold;padding-left: 10px;">
                                Additional Permits</h4>
                            <div class="divider-line"></div>

                            @foreach ($AdditionalDocuments as $doc)
                                <div class="row mb-2 align-items-center">
                                    <div class="col-md-3">
                                        <label class="form-label custom-label mb-0">{{ $doc->name }}</label>
                                    </div>
                                    <div class="col-md-9">
                                    <a href="{{ route('business.download_AdditionalDocuments', ['id' => $doc->id, 'AdditionalDocuments']) }}"
                                        target="_blank"
                                        class="custom-label d-flex align-items-center gap-2"
                                        title="{{ $doc->attachment }}">
                                            <i class="custom-icon fa fa-download"></i>
                                            <span>{{ $doc->attachment }}</span>
                                        </a>
                                    </div>
                                </div>
                            @endforeach
                        </div>

                        <div class="tab-pane" role="tabpanel" id="tab-remarks">
                            <div class="row">
                                <div class="col-md-3">
                                        <label class="form-label">Status :&nbsp;</label>
                                </div>
                                <div class="col-md-9">
                                        <span>{{ $appStatus->name }}</span>
                                </div>
                                <div class="col-md-3">
                                        <label class="form-label">Reason :&nbsp;</label>
                                </div>
                                <div class="col-md-9">
                                        <span>{{ $appCannedStatus->description ?? 'N/A' }}</span>
                                </div>
                                <div class="col-md-3">
                                        <label class="form-label">Remarks :&nbsp;</label>
                                </div>
                                <div class="col-md-9">
                                        <span>{{ $business->admin_remarks ?? 'N/A' }}</span>
                                </div>
                            </div>
                        </div>
                        <div class="tab-pane" role="tabpanel" id="tab-audit-trail">
                                <div class="row d-flex align-items-center justify-content-end"
                                    style="padding-bottom: 15px;padding-top: 15px;">
                                    <div class="col-xl-2 col-lg-2 col-md-6 col-sm-12 col-12">
                                        <div class="btn-box">
                                            <label for="Search" class="form-label">{{ __('From Date') }}</label>
                                            <input type="date" name="fromdate" id="fromdate" class="form-control"
                                                value="{{ date('Y-m-d') }}" style="font-size:12px; padding:9px;">
                                        </div>
                                    </div>
                                    <div class="col-xl-2 col-lg-2 col-md-6 col-sm-12 col-12">
                                        <div class="btn-box">
                                            <label for="Search" class="form-label">{{ __('To Date') }}</label>
                                            <input type="date" name="todate" id="todate" class="form-control"
                                                value="{{ date('Y-m-d') }}" style="font-size:12px; padding:9px;">
                                        </div>
                                    </div>
                                    <div class="col-xl-3 col-lg-3 col-md-6 col-sm-12 col-12">
                                        <div class="btn-box">
                                            <label for="Search" class="form-label">{{ __('Search Details') }}</label>
                                            <input type="text" id="q" class="form-control"
                                                placeholder="Search Details">
                                            <input type="hidden" id="businessid" class="form-control"
                                                value="{{ $business->id }}">
                                        </div>
                                    </div>
                                    <div class="col-xl-1 col-lg-1 col-md-6 col-sm-12 col-12" style="padding-top: 23px;">
                                        <button id="searchBtn" class="btn btn-primary">Search</button>
                                    </div>
                                </div>

                                <table id="auditLogsTable" class="table table-bordered table-striped" style="width:100%">
                                    <thead>
                                        <tr>
                                            <th style="width:5% !important;">No</th>
                                            <th style="width:10% !important;">Date | Time</th>
                                            <th style="width:25% !important;">User Name</th>
                                            <th style="width:10% !important;">Action</th>
                                            <th style="width:15% !important;">Audit Description</th>
                                            <th style="width:15% !important;">Status</th>
                                            <th style="width:15% !important;">Remarks</th>
                                            <th style="width:5% !important;">Location</th>
                                        </tr>
                                    </thead>
                                </table>

                            </div>
                            
                            <div class="tab-pane" role="tabpanel" id="tab-follow-up-email">
                                <div class="row d-flex align-items-center justify-content-end"
                                    style="padding-bottom: 15px;padding-top: 15px;">
                                    <div class="col-xl-2 col-lg-2 col-md-6 col-sm-12 col-12">
                                        <div class="btn-box">
                                            <label for="Search" class="form-label">{{ __('From Date') }}</label>
                                            <input type="date" name="followupemailfromdate" id="followupemailfromdate" class="form-control"
                                                value="{{ date('Y-m-d') }}" style="font-size:12px; padding:9px;">
                                        </div>
                                    </div>
                                    <div class="col-xl-2 col-lg-2 col-md-6 col-sm-12 col-12">
                                        <div class="btn-box">
                                            <label for="Search" class="form-label">{{ __('To Date') }}</label>
                                            <input type="date" name="followupemailtodate" id="followupemailtodate" class="form-control"
                                                value="{{ date('Y-m-d') }}" style="font-size:12px; padding:9px;">
                                        </div>
                                    </div>
                                    <div class="col-xl-3 col-lg-3 col-md-6 col-sm-12 col-12">
                                        <div class="btn-box">
                                            <label for="Search" class="form-label">{{ __('Search Details') }}</label>
                                            <input type="text" id="followupemailq" class="form-control"
                                                placeholder="Search Details">
                                                <input type="hidden" id="businessid" class="form-control"
                                                value="{{ $business->id }}">
                                            
                                        </div>
                                    </div>
                                    <div class="col-xl-1 col-lg-1 col-md-6 col-sm-12 col-12" style="padding-top: 23px;">
                                        <button id="followupemailTablesearchBtn" class="btn btn-primary">Search</button>
                                    </div>
                                </div>

                                <table id="followupemailTable" class="table table-bordered table-striped" style="width:100%">
                                    <thead>
                                        <tr>
                                            <th style="width:5% !important;">No</th>
                                            <th style="width:20% !important">Date | Time</th>
                                            <th style="width:25% !important">Type</th>
                                            <th style="width:50% !important">Message Description</th>
                                            
                                        </tr>
                                    </thead>
                                </table>

                            </div>
                        @if ($business->status == 'UNDER EVALUATION')
                            @if (Auth::user()->role == 2)
                                <div class="tab-pane" role="tabpanel" id="tab-action">
                                    <form action="{{ route('business.admin_update', $business->id) }}" method="POST"
                                        id="businessForm">
                                        @csrf
                                        @method('PUT')
                                        <div class="row">
                                            <div class="col">
                                                <div class="mb-3">
                                                    <label class="form-label">Status <span
                                                            class="required-field">*</span></label>
                                                    <select class="form-select" name="status_id" id="status_id" required>
                                                        <option value="" disabled selected>Select Status</option>
                                                        @foreach ($status as $id => $name)
                                                            <option value="{{ $id }}">{{ $name }}
                                                            </option>
                                                        @endforeach
                                                    </select>
                                                </div>
                                            </div>

                                            <div class="col">
                                                <div class="mb-3">
                                                    <label class="form-label">Reason <span
                                                            class="required-field">*</span></label>
                                                    <select class="form-select" name="reason_id" id="reason_id" required
                                                        disabled>
                                                        <option value="" disabled selected>Select Reason</option>
                                                    </select>
                                                </div>
                                            </div>

                                        </div>
                                        <div class="row">
                                            <div class="col">
                                                <div class="mb-3">
                                                    <label class="form-label">Remarks <span
                                                            class="required-field">*</span></label><br>
                                                    <textarea class="form-control custom-input" name="remark" id="remark" cols="30" rows="4" required>{{ $business->admin_remarks }}</textarea>
                                                </div>
                                            </div>
                                        </div>
                                        <button class="btn btn-primary btn-sm" type="submit" id="status-action-btn"
                                            style="padding-left: 14px;margin-top: 21px;margin-left: 0px;padding-right: 14px;margin-bottom: 21px;font-size: 12px;font-family: sans-serif;">
                                            Update
                                        </button>
                                    </form>
                                </div>
                            @endif
                        @else
                            @if ($business->status == 'APPROVED' && $business->payment_id == null)
                                @if (Auth::user()->role == 1)
                                    <div class="tab-pane" role="tabpanel" id="tab-payment">
                                        <div class="row tab-section">
                                            <div class="col d-flex justify-content-center align-items-center">
                                                <img class="img-fluid"
                                                    src="{{ asset('assets/img/DTI-BP-transparent.png') }}"
                                                    alt="DTI Logo">
                                            </div>
                                            <div class="col">
                                                <div class="charges-table-wrapper">
                                                    <div class="table-responsive text-end charges-table">
                                                        <table class="table">
                                                            <thead>
                                                                <tr>
                                                                    <th class="table-heading">Charges</th>
                                                                    <th class="table-heading">PHP</th>
                                                                </tr>
                                                            </thead>
                                                            <tbody class="text-end">
                                                                @forelse ($busines_fee as $fee)
                                                                    <tr>
                                                                        <td class="table-label">{{ $fee->fee_name }}</td>
                                                                        <td class="table-label">
                                                                            {{ number_format($fee->amount, 2) }}</td>
                                                                    </tr>
                                                                @empty
                                                                    <tr>
                                                                        <td colspan="2" class="table-label">No fees
                                                                            found.</td>
                                                                    </tr>
                                                                @endforelse

                                                                <tr>
                                                                    <td class="table-label total">Total Amount</td>
                                                                    <td id="ins-total" name="ins_total"
                                                                        class="table-value total">
                                                                        {{ number_format($busines_fee->sum('amount'), 2) }}
                                                                    </td>
                                                                </tr>
                                                            </tbody>
                                                        </table>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="d-flex justify-content-end mt-4" id="payment-button">
                                            {{-- the correct one --}}
                                            <button class="btn btn-primary" type="button" title="Make Payment"
                                                style="margin-right:20px;font-family:sans-serif;font-size:14px;"
                                                id="makePayment">
                                                Make Payment
                                            </button>

                                            {{-- testing purpose --}}
                                            {{-- <button class="btn btn-primary" type="button" title="Make Payment"
                                                style="margin-right:20px;font-family:sans-serif;font-size:14px;"
                                                data-bs-toggle="modal" data-bs-target="#modal-payment">
                                                Make Payment
                                            </button> --}}
                                            <hr>
                                        </div>
                                    </div>
                                @else
                                    <div class="tab-pane" role="tabpanel" id="tab-payment">
                                        <div class="row tab-section">
                                            <div class="col d-flex justify-content-center align-items-center">
                                                <img class="img-fluid"
                                                    src="{{ asset('assets/img/DTI-BP-transparent.png') }}"
                                                    alt="DTI Logo">
                                            </div>
                                            <div class="col">
                                                <div class="charges-table-wrapper">
                                                    <div class="table-responsive text-end charges-table">
                                                        <table class="table">
                                                            <thead>
                                                                <tr>
                                                                    <th class="table-heading">Charges</th>
                                                                    <th class="table-heading">PHP</th>
                                                                </tr>
                                                            </thead>
                                                            <tbody class="text-end">
                                                                @forelse ($busines_fee as $fee)
                                                                    <tr>
                                                                        <td class="table-label">{{ $fee->fee_name }}</td>
                                                                        <td class="table-label">
                                                                            {{ number_format($fee->amount, 2) }}</td>
                                                                    </tr>
                                                                @empty
                                                                    <tr>
                                                                        <td colspan="2" class="table-label">No fees
                                                                            found.</td>
                                                                    </tr>
                                                                @endforelse

                                                                <tr>
                                                                    <td class="table-label total">Total Amount</td>
                                                                    <td id="ins-total" name="ins_total"
                                                                        class="table-value total">
                                                                        {{ number_format($busines_fee->sum('amount'), 2) }}
                                                                    </td>
                                                                </tr>
                                                            </tbody>
                                                        </table>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                @endif
                            @else
                                <div class="tab-pane" role="tabpanel" id="tab-payment">
                                    <div class="row tab-section">
                                        <div class="col d-flex justify-content-center align-items-center">
                                            <img class="img-fluid" src="{{ asset('assets/img/DTI-BP-transparent.png') }}"
                                                alt="DTI Logo">
                                        </div>
                                        <div class="col">
                                            <div class="receipt-info">
                                                <label for="receipt_number" class="form-label-inline">Receipt Number
                                                    :&nbsp;</label>
                                                <span
                                                    class="form-value">{{ isset($payment) ? $payment->transaction_id : '' }}</span>
                                            </div>
                                            <div class="charges-table-wrapper">
                                                <div class="table-responsive text-end charges-table">
                                                    <table class="table">
                                                        <thead>
                                                            <tr>
                                                                <th class="table-heading">Charges</th>
                                                                <th class="table-heading">PHP</th>
                                                            </tr>
                                                        </thead>
                                                        <tbody class="text-end">
                                                            @forelse ($busines_fee as $fee)
                                                                <tr>
                                                                    <td class="table-label">{{ $fee->fee_name }}</td>
                                                                    <td class="table-label">
                                                                        {{ number_format($fee->amount, 2) }}</td>
                                                                </tr>
                                                            @empty
                                                                <tr>
                                                                    <td colspan="2" class="table-label">No fees
                                                                        found.</td>
                                                                </tr>
                                                            @endforelse

                                                            <tr>
                                                                <td class="table-label total">Total Amount</td>
                                                                <td id="ins-total" name="ins_total"
                                                                    class="table-value total">
                                                                    {{ number_format($busines_fee->sum('amount'), 2) }}
                                                                </td>
                                                            </tr>
                                                        </tbody>
                                                    </table>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            @endif
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="modal fade" role="dialog" tabindex="-1" id="modal-payment">
        <div class="modal-dialog" role="document">
            <form id="payment-form" action="{{ route('business.save_payment') }}" method="POST">
                @csrf
                <div class="modal-content">
                    <div class="modal-header">
                        <h4 class="modal-title" style="font-family: sans-serif;font-size: 14px;">Payment</h4>
                        <button class="btn-close" type="button" aria-label="Close" data-bs-dismiss="modal"></button>
                    </div>
                    <div class="modal-body">
                        <p style="font-family: sans-serif;font-size: 12px;">Fiuu Payment</p>
                        <input type="hidden" name="amount" value="">
                        <input type="hidden" name="business_id" value="{{ $business->id }}">
                    </div>
                    <div class="modal-footer"><button class="btn btn-light" type="button" data-bs-dismiss="modal"
                            style="font-family: sans-serif;font-size: 12px;">Close</button><button class="btn btn-primary"
                            type="submit" style="font-family: sans-serif;font-size: 12px;">Make Payment</button></div>
                </div>
            </form>
        </div>
    </div>
    <div class="modal" role="dialog" tabindex="-1" id="modal-success" style="margin-top: 0px;">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header" style="background: #d1e7dd;color: #0f5132;">
                    <h4 class="modal-title" style="font-family: sans-serif;font-size: 15px;">Successful</h4>
                    <button class="btn-close" type="button" aria-label="Close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <p style="font-family: sans-serif; font-size: 12px;">
                        Payment completed successfully
                    </p>
                </div>
                {{-- <div class="modal-footer"><a href="{{ route('business.view', $id) }}"><button
                            class="btn btn-light" type="button" data-bs-dismiss="modal">Close</button></a></div> --}}
                <div class="modal-footer">
                    <button class="btn btn-light" type="button" data-bs-dismiss="modal">Close</button>
                </div>
            </div>
        </div>
    </div>

    <div class="modal fade" role="dialog" tabindex="-1" id="modal-error" style="margin-top: 0px;">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header" style="background: #f8d7da; color: #842029;">
                    <h4 class="modal-title" style="font-family: sans-serif; font-size: 15px;">Error</h4>
                    <button class="btn-close" type="button" aria-label="Close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <p style="font-family: sans-serif; font-size: 12px;">
                        Payment still pending
                    </p>
                </div>
                <div class="modal-footer">
                    <button class="btn btn-light" type="button" data-bs-dismiss="modal">Close</button>
                </div>
            </div>
        </div>
    </div>
    <!-- Include html2canvas -->
    <div id="captureArea" style="position: absolute; top: -9999px; left: -9999px; width: 500px; height: 450px; border-radius: 15px; background: #fff; padding: 50px;">
        <div class="row p-3" style="border-radius: 15px;border: 21px solid rgb(62,67,134);width: 415px;">
            <div class="col text-center">
                <div class="col" style="text-align: center;margin-top: 20px;">
                <div class="container  p-3"
                >
                    <div class="d-flex justify-content-center align-items-start gap-1">
                        <div class="text-center">
                            <img src="{{ asset('assets/img/TRUSTMARK-SHIELD.png') }}" width="106" height="126" />
                        </div>
                        <div class="text-center d-flex flex-column align-items-center">
                            @php
                                $filename = str_replace('storage/', '', $business->qr_code);
                                $filepath = asset('storage/app/public/' . $filename);

                                $fileSystemPath = public_path('storage/' . $filename);
                                if(file_exists($fileSystemPath)){
                                    $filepath = asset('storage/' . $filename);
                                }
                            @endphp
                            <img src="{{ $filepath }}" width="95" height="95" />
                            <!-- <img src="{{ asset('assets/img/qr_1_25072013313523.png') }}" width="95" height="95" /> -->
                            <img class="mt-0" src="{{ asset('assets/img/TRUSTMARK-REGISTERED-ONLY.png') }}" width="87" height="21" />
                            <p class="mb-0" style="font-size: 12px;">{{ $business->trustmark_id }}</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        @php
            $primaryUser = \App\Models\User::where('is_primary', 1)->first();
        @endphp
        @if($primaryUser && $primaryUser->profile_photos)
            <div class="row mb-0 pb-0 text-center">
                <div style="text-align: center;width:100%;">
                <!-- <img src="{{ asset('assets/img/signature_1752930308.png') }}" 
                 width="150" height="100%"/> -->
                    <img src="{{ asset('storage/' . $primaryUser->profile_photos) }}" width="150" height="100%" />
                    
                </div>
            </div>
        @endif
        <div class="row mt-0 pt-0 text-center" style="text-align: center;width:100%;">
            <div style="text-align: center;width:100%;">
                <p class="mb-0 fw-bold" style="font-size: 15px; color: black;">MA. CRISTINA A. ROQUE</p>
                <p class="lh-1 mb-0" style="font-size: 13px; color: black;">Secretary</p>
            </div>
        </div>
    </div>

<!-- Automatically download as PNG -->
<!-- HTML2Canvas Auto-Download PNG Without Closing Page -->
<!-- Include html2canvas -->
<!-- Include html2canvas -->
<script src="https://cdn.jsdelivr.net/npm/html2canvas@1.4.1/dist/html2canvas.min.js"></script>
<script>
    document.getElementById('downloadQrBtn').addEventListener('click', function () {
        const captureElement = document.getElementById('captureArea');
        if (!captureElement) {
            console.error('Element #captureArea not found.');
            return;
        }

        const timestamp = Date.now();

        html2canvas(captureElement, {
            useCORS: true,
            allowTaint: false,
            scale: 2
        }).then(function (canvas) {
            const imgData = canvas.toDataURL('image/png');
            const link = document.createElement('a');
            link.href = imgData;
            link.download = `TMKQR_${timestamp}.png`;
            document.body.appendChild(link);
            link.click();
            document.body.removeChild(link);
        }).catch(err => {
            console.error('Canvas generation failed:', err);
        });
    });
</script>
<script src="https://cdn.jsdelivr.net/npm/html2canvas@1.4.1/dist/html2canvas.min.js"></script>

    <script>
        document.getElementById('businessForm').addEventListener('submit', function(e) {
            e.preventDefault(); // prevent immediate submission

            Swal.fire({
                title: 'Are you sure?',
                text: "Do you want to submit this form?",
                icon: 'warning',
                showCancelButton: true,
                confirmButtonText: 'Yes, submit',
                cancelButtonText: 'Cancel'
            }).then((result) => {
                if (result.isConfirmed) {
                    e.target.submit(); // submit the form if confirmed
                }
            });
        });

        document.addEventListener('DOMContentLoaded', function() {
            const remarkField = document.getElementById('remark');
            const returnRadio = document.getElementById('return');
            const form = document.querySelector('form');

            function toggleRemarkRequirement() {
                if (returnRadio.checked) {
                    remarkField.setAttribute('required', 'required');
                } else {
                    remarkField.removeAttribute('required');
                }
            }

            // Run initially in case RETURNED is pre-selected
            toggleRemarkRequirement();

            // Run when radio buttons change
            document.querySelectorAll('input[name="status"]').forEach(function(radio) {
                radio.addEventListener('change', toggleRemarkRequirement);
            });

            // Optional: add custom alert if remark is missing
            form.addEventListener('submit', function(e) {
                if (returnRadio.checked && remarkField.value.trim() === '') {
                    e.preventDefault();
                    alert('Please provide a remark.');
                    remarkField.focus();
                }
            });
        });
    </script>

    {{-- <input type="hidden" id="BASE_URL" value="{{ url('/') }}">
    <input type="hidden" id="business_id" value="{{ $business->id }}"> --}}

    <script>
        // Wait for the page to fully load
        document.addEventListener('DOMContentLoaded', function() {
            // Get the encrypted ID from the URL
            const path = window.location.pathname;
            const segments = path.split('/');
            const encryptedId = segments[segments.length - 1]; // get the last part

            // Set the value into the input field
            document.getElementById('business_id').value = encryptedId;
        });
    </script>

    <input type="hidden" id="BASE_URL" value="{{ url('/') }}">
    <input type="hidden" id="business_id" value="">

    <script src="{{ asset('assets/js/fiuu_payment.js') }}"></script>

    {{-- tomselect and cascading for admin action tab --}}
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const BASE_URL = document.getElementById('BASE_URL').value;
            const statusTom = new TomSelect("#status_id", {
                create: false,
                sortField: {
                    field: "text",
                    direction: "asc"
                }
            });

            const reasonTom = new TomSelect("#reason_id", {
                create: false,
                sortField: {
                    field: "text",
                    direction: "asc"
                }
            });

            const statusSelect = document.getElementById('status_id');
            const reasonSelect = document.getElementById('reason_id');
            const remarkTextarea = document.getElementById('remark');

            statusSelect.addEventListener('change', function() {
                const statusId = this.value;
                const selectedStatusText = this.options[this.selectedIndex].text.trim();

                // Toggle required attribute on reason based on status ID = 1
                if (statusId === '1') {
                    // Make reason NOT required if status_id is 1
                    reasonSelect.required = false;
                } else {
                    // Otherwise, reason is required
                    reasonSelect.required = true;
                }

                // Toggle required on remark textarea the same way
                if (statusId === '1') {
                    remarkTextarea.required = false;
                } else {
                    remarkTextarea.required = true;
                }

                reasonTom.clearOptions();
                reasonTom.disable();
                reasonTom.addOption({
                    value: "",
                    text: "Loading..."
                });
                reasonTom.refreshOptions();

                fetch(`${BASE_URL}/get-reasons/${statusId}`)
                    .then(res => res.json())
                    .then(data => {
                        reasonTom.clearOptions();
                        if (Object.keys(data).length > 0) {
                            for (const id in data) {
                                reasonTom.addOption({
                                    value: id,
                                    text: data[id]
                                });
                            }
                            reasonTom.enable();
                            reasonTom.refreshOptions(false); // Prevent auto dropdown
                        } else {
                            reasonTom.addOption({
                                value: "",
                                text: "No reasons found"
                            });
                            reasonTom.refreshOptions(false); // Prevent auto dropdown
                        }
                    })
            });
        });
    </script>


    {{-- testing purpose --}}
    <script>
        // pass amount to modal
        document.addEventListener('DOMContentLoaded', function() {
            var modal = document.getElementById('modal-payment');

            modal.addEventListener('show.bs.modal', function(event) {
                var button = event.relatedTarget;
                var amount = button.getAttribute('data-amount');

                var display = modal.querySelector('#modal-amount-display');
                if (display) {
                    display.textContent = '₱' + parseFloat(amount).toFixed(2);
                }

                // Update the hidden input inside the form
                var input = modal.querySelector('input[name="amount"]');
                if (input) {
                    input.value = parseFloat(amount).toFixed(2);
                }
            });
        });

        $(document).ready(function() {
            // Check on page load if payment was successful before reload
            if (localStorage.getItem('paymentSuccess') === 'true') {
                $('#modal-success').modal('show');
                localStorage.removeItem('paymentSuccess');
            }

            $('#modal-payment .btn-primary[type="submit"]').click(function(e) {
                e.preventDefault();

                const form = $('#payment-form');
                const url = form.attr('action');
                const formData = form.serialize();

                $.ajax({
                    type: 'POST',
                    url: url,
                    data: formData,
                    success: function(response) {
                        if (response.success) {
                            // Save flag before reload
                            localStorage.setItem('paymentSuccess', 'true');

                            // Reload the page
                            location.reload();
                        }
                    },
                    error: function(xhr) {
                        alert('Payment failed. Please try again.');
                        console.error(xhr.responseText);
                    }
                });
            });
        });
    </script>

    {{-- submit button based on status selected --}}
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const statusSelect = document.getElementById('status_id');
            const actionBtn = document.getElementById('status-action-btn');
            const statusOptions = @json($status);

            function updateButtonLabel() {
                const selectedOption = statusSelect.options[statusSelect.selectedIndex];
                const statusName = selectedOption && selectedOption.value ? selectedOption.text.trim() : '';
                if (statusName) {
                    actionBtn.textContent = statusName;
                } else {
                    actionBtn.textContent = 'Update';
                }
            }

            statusSelect.addEventListener('change', updateButtonLabel);

            // Set initial button label if editing
            updateButtonLabel();
        });
    </script>
    <script>
        function confirmReGenerate(url) {
            Swal.fire({
                title: 'Are you sure?',
                text: "Do you want to re-generate the certificate?",
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#728cd8',
                cancelButtonColor: '#84879b',
                cancelButtonText: 'Cancel',
                confirmButtonText: 'Yes',
                reverseButtons: true
            }).then((result) => {
                if (result.isConfirmed) {
                    window.location.href = url;
                }
            });
        }
    </script>
    <script>
            $(document).ready(function() {
                $(document).on('shown.bs.tab', 'a[href="#tab-audit-trail"]', function(e) {
                    datatablefunction();
                });
                
                $("#searchBtn").click(function() {
                    datatablefunction();
                });
               setTimeout(function () {
                   // onloadUserlogsave();
                }, 3000);
            });

            function datatablefunction() {
                if ($.fn.DataTable.isDataTable('#auditLogsTable')) {
                    $('#auditLogsTable').DataTable().destroy();
                }
                $('#auditLogsTable').DataTable({
                    processing: true,
                    serverSide: true,
                    searching: false,
                    autoWidth: false,
                    dom: "<'row'<'col-sm-12'f>>" + "<'row'<'col-sm-3'l><'col-sm-9'p>>" + "<'row'<'col-sm-12'tr>>" +
                        "<'row'<'col-sm-12'p>>",
                    ajax: {
                        url: "{{ route('audit.logs') }}",
                        data: function(d) {
                            d.businessId = $('#businessid').val();
                            d.fromdate = $('#fromdate').val();
                            d.todate = $('#todate').val();
                            d.q = $('#q').val();
                        }
                    },
                    pageLength: 10,
                    columns: [{
                            data: 'no',
                            orderable: false
                        },
                        {
                            data: 'date_time'
                        },
                        {
                            data: 'user_name',
                            render: function(data, type, row, meta) {
                            if (!data) return '';
                            if (data.length > 25) {
                                const shortText = data.substr(0, 25) + '...';
                                return `
                            <div class="text-container" style="white-space: normal; word-break: break-word;">
                                <span class="short-text">${shortText}</span>
                                <span class="full-text" style="display:none;">${data}</span>
                                <a href="#" class="toggle-text" style="color:blue;">Read more</a>
                            </div>
                        `;
                            }
                            return `<div style="white-space: normal; word-break: break-word;">${data}</div>`;
                           }
                        },
                        {
                            data: 'action_name',
                        },
                        {
                            data: 'audit_description',
                            render: function(data, type, row, meta) {
                            if (!data) return '';
                            if (data.length > 25) {
                                const shortText = data.substr(0, 25) + '...';
                                return `
                            <div class="text-container" style="white-space: normal; word-break: break-word;">
                                <span class="short-text">${shortText}</span>
                                <span class="full-text" style="display:none;">${data}</span>
                                <a href="#" class="toggle-text" style="color:blue;">Read more</a>
                            </div>
                        `;
                            }
                            return `<div style="white-space: normal; word-break: break-word;">${data}</div>`;
                           }
                        },
                        {
                            data: 'status'
                        },
                        {
                            data: 'remarks'
                        },
                        {
                            data: 'location'
                        }
                    ],
                    drawCallback: function(s){ 
                        var api = this.api();
                        api.$('.viewlocation').click(function() {
                            var latitude = $(this).attr('latitude');
                            var longitude = $(this).attr('longitude');
                                openGoogleMap(latitude,longitude);
                        });
                    }
                });
            }

         function  onloadUserlogsave(){
                $.ajax({
                            url: "{{ route('business.savelogview') }}",
                            type: "POST",
                            data: {
                                _token: "{{ csrf_token() }}",
                                business_id: $('#businessid').val(),
                                lat:$('input[name="latitude"]').val(),
                                long:$('input[name="longitude"]').val()
                            },
                            success: function(response) {
                                if (response.success) {
                                    // Swal.fire({
                                    //     icon: "success",
                                    //     title: "Assigned!",
                                    //     text: response.message,
                                    //     confirmButtonColor: "#09325d"
                                    // }).then(() => {
                                    //    // location.reload();
                                    // });
                                } 
                            },
                            error: function(xhr) {
                                Swal.fire({
                                    icon: "error",
                                    title: "Server Error",
                                    text: "Something went wrong. Please try again."
                                });
                            }
                        });
            }
            function openGoogleMap(latitude, longitude){
                const url = `https://www.google.com/maps?q=`+latitude+`,`+longitude;
                window.open(url, '_blank');
            }
            $('#auditLogsTable').on('click', '.toggle-text', function(e) {
                e.preventDefault();
                const $container = $(this).closest('.text-container');
                const $short = $container.find('.short-text');
                const $full = $container.find('.full-text');

                if ($full.is(':visible')) {
                    $full.hide();
                    $short.show();
                    $(this).text('Read more');
                } else {
                    $short.hide();
                    $full.show();
                    $(this).text('Read less');
                }
            });
        </script>
        <script>
            $(document).ready(function() {
                $(document).on('shown.bs.tab', 'a[href="#tab-follow-up-email"]', function(e) {
                followupemailTableDatatable();
                });
            

            $("#followupemailTablesearchBtn").click(function() {
                followupemailTableDatatable();
            });
        });

        function followupemailTableDatatable() {
            if ($.fn.DataTable.isDataTable('#followupemailTable')) {
                $('#followupemailTable').DataTable().destroy();
            }

            $('#followupemailTable').DataTable({
                processing: true,
                serverSide: true,
                searching: false,
                autoWidth: false,
                ajax: {
                    url: "{{ route('getFollowupEmailList') }}",
                    data: function(d) {
                        d.busn_id = $('#businessid').val();
                        d.fromdate = $('#followupemailfromdate').val();
                        d.todate = $('#followupemailtodate').val();
                        d.q = $('#qfollowupemail').val();
                    }
                },
                pageLength: 10,
                columns: [
                    { data: 'no', orderable: false },
                    { data: 'date_time' },
                    { data: 'typeData' },
                    { 
                        data: 'message_description',
                        render: function(data, type, row, meta) {
                            if (!data) return '';
                            if (data.length > 25) {
                                const shortText = data.substr(0, 25) + '...';
                                return `
                                    <div class="text-container" style="white-space: normal; word-break: break-word;">
                                        <span class="short-text">${shortText}</span>
                                        <span class="full-text" style="display:none;">${data}</span>
                                        <a href="#" class="toggle-text" style="color:blue;">Read more</a>
                                    </div>
                                `;
                            }
                            return `<div style="white-space: normal; word-break: break-word;">${data}</div>`;
                        }
                    }
                ]
            });
        }

        // Toggle "Read more / Read less"
        $('#followupemailTable').on('click', '.toggle-text', function(e) {
            e.preventDefault();
            const $container = $(this).closest('.text-container');
            const $short = $container.find('.short-text');
            const $full = $container.find('.full-text');

            if ($full.is(':visible')) {
                $full.hide();
                $short.show();
                $(this).text('Read more');
            } else {
                $short.hide();
                $full.show();
                $(this).text('Read less');
            }
        });

        </script>
@endsection
