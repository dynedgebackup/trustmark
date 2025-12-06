@extends('layouts.app')

@section('content')
    <div class="d-flex justify-content-between align-items-center mb-4" style="margin-top: 15px;">
        @if (Auth::user()->role == 2)
            <h3 class="page-title">MANAGE BUSINESS</h3>
        @else
            <h3 class="page-title">VIEW BUSINESS</h3>
        @endif
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
        .toggle-box {
            border: 1px solid #ccc;
            border-radius: 4px;
            width: 98.9%;
            margin-left: 5px;
            overflow: hidden;
            font-family: Arial, sans-serif;
            transition: all 0.3s ease;
        }
        .toggle-header {
            background: #ff0000;
            color: white;
            padding: 12px 16px;
            cursor: pointer;
            font-weight: bold;
            display: flex;
            align-items: center;
            justify-content: space-between;
        }
        .toggle-arrow {
            transition: transform 0.3s ease;
            font-size: 18px;
        }
        .collapsed .toggle-arrow {
            transform: rotate(-90deg); /* Point right when collapsed */
        }
        .toggle-content {
            padding: 15px;
            display: block;
            height: 500px;
            overflow: overlay;
            width: 100%;
        }
        .collapsed .toggle-content {
            display: none;
        }
        thead, tbody, tfoot, tr, th {
            border-color: inherit;
            border-style: solid;
            border-width: 0;
            border: 1px solid #ccc;
            padding: 8px;
            width: 50%;
        }
        .swal2-confirm {
            background-color: #09325d !important;
            color: #fff !important;
            margin-left: 10px !important;
            border: none !important;
            padding: 8px 18px !important;
            border-radius: 5px !important;
            font-weight: 500 !important;
            cursor: pointer;
        }

        .swal-cancel-btn {
            background-color: #e5e5e5 !important;
            color: #333 !important;
            border: none !important;
            padding: 8px 18px !important;
            border-radius: 5px !important;
            font-weight: 500 !important;
            cursor: pointer;
        }
        .tab-btn {
            border: none;
            background: none;
            color: #09325d;
            font-weight: 500;
            margin-right: 15px;
            cursor: pointer;
        }
        .tab-btn.active {
            border-bottom: 2px solid #09325d;
        }
        .refund-details .table-heading{
            color:red;
        }
        .refund-details .table-label{
            color:red;
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
                                href="#tab-documents">Documents</a>
                        </li>

                        @if ($business->status == 'UNDER EVALUATION' || $business->status == 'RETURNED')
                            @if (Auth::user()->role == 2)
                                <li class="nav-item" role="presentation">
                                    <a class="nav-link custom-tab-link" role="tab" data-bs-toggle="tab"
                                        href="#tab-action">Action</a>
                                </li>
                            @endif
                        @else
                        @if (Auth::user()->role == 2 && $business->status == 'APPROVED' && $business->payment_id != null)
                            <li class="nav-item" role="presentation">
                                <a class="nav-link custom-tab-link" role="tab" data-bs-toggle="tab"
                                    href="#tab-action">Action</a>
                            </li>
                        @endif
                            <li class="nav-item" role="presentation">
                                <a class="nav-link custom-tab-link" role="tab" data-bs-toggle="tab"
                                    href="#tab-payment">Payment</a>
                            </li>
                        @endif
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
                    <input type="hidden" id ="latitude" name="latitude" value="">
                    <input type="hidden" id ="longitude" name="longitude"  value="">
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
                                                                        <img src="{{ asset($business->qr_code) }}"
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
                                                    <a href="#" id="downloadQrBtn" style="width: 100%;"
                                                        class="btn btn-primary mb-3">
                                                        <i class="fa fa-download"></i> Download QR as PNG
                                                    </a>
                                                </div>
                                            </div>
                                        </div>
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
                                            <th style="width:2% !important;">No</th>
                                            <th style="width:10% !important;">Date | Time</th>
                                            <th style="width:25% !important;">User Name</th>
                                            <th style="width:10% !important;">Action</th>
                                            <th style="width:15% !important;">Audit Description</th>
                                            <th style="width:10% !important;">Status</th>
                                            <th style="width:25% !important;">Remarks</th>
                                            <th style="width:3% !important;">Location</th>
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
                            <div class="tab-pane" role="tabpanel" id="tab-certificate">
                                <div class="row custom-row">
                                    <div class="col custom-col">
                                        <div class="mb-3">
                                            <div style="padding-top: 15px;">
                                                <a href="{{ route('business.download_certificate', $business->id) }}?v={{ time() }}"
                                                    class="btn btn-primary mb-3" id="downloadCertificateBtn"
                                                        data-busn-id="{{ $business->id }}"
                                                        data-status="{{ $business->status }}">
                                                    <i class="fa fa-download"></i> Download Certificate
                                                </a>
                                                @php
                                                    $UserRole = \App\Models\User::where('id', Auth::id())->first();
                                                @endphp

                                                @if ($UserRole->role == 2)
                                                    <a href="{{ route('business.certReGenerate', $business->id) }}"
                                                        class="btn btn-primary mb-3"
                                                        onclick="event.preventDefault(); confirmReGenerate('{{ route('business.certReGenerate', $business->id) }}');">
                                                        <i class="fa fa-certificate"></i> Re-Generate
                                                    </a>
                                                @endif
                                            </div>
                                            @php
                                                $certificate = str_replace('storage/', '', $business->certificate);
                                                $filePathCertificate = asset('storage/app/public/' . $certificate);
                                            @endphp
                                            <iframe src="{{ $filePathCertificate }}" width="100%" height="1200px"
                                                style="border:none;margin-top: -14px;">
                                            </iframe>
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
                                    @if ($business->app_status_id == 1)
                                        <div class="col-md-9">
                                            <span>{{ $business->admin_updated_at ? \Carbon\Carbon::parse($business->admin_updated_at)->format('F j, Y') : 'N/A' }}</span>
                                        </div>
                                    @else
                                        <div class="col-md-9">
                                            <span>N/A</span>
                                        </div>
                                    @endif

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
                                        <span>{{ $business->tin ?? 'N/A' }}</span>
                                        @if (Auth::user()->role == 2)
                                            <a href="javascript:void(0)" class="btn btn-primary check-records"
                                                title="Make Check Records" attr-id="{{ $business->id }}"
                                                style="margin-right:20px;font-family:sans-serif;font-size:10px;padding: 1px 6px;border: none;background: #09325d;">
                                                <i class="fa fa-file"></i> Check Records
                                            </a>
                                        @endif
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="col-md-3">
                                        <label class="form-label">Business Name :&nbsp;</label>
                                    </div>
                                    <div class="col-md-9">
                                        <span>{{ $business->business_name ?? 'N/A' }}</span>
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
                                <div class="divider-line" style="margin-bottom: 8px !important;"></div>
                                <h6 class="text-center multisteps-form__title"
                                    style="font-family: sans-serif;color: rgb(0,0,0);font-weight: bold;">
                                    Business URL | Website | Social Media Platform Link 
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
                                        <span>{{ $business->pic_name ?? 'N/A' }}</span>
                                    </div>
                                    <div class="col-md-3">
                                        <label class="form-label">Mobile No. :&nbsp;</label>
                                    </div>
                                    <div class="col-md-9">
                                        <span>{{ $business->pic_ctc_no ?? 'N/A' }}</span>
                                        @if (Auth::user()->role == 2)
                                            @if ($business->payment_id > 0)
                                                @if ($business->pic_ctc_no_is_confidential == 1)
                                                    <a href="#" id="confidentialBtn" data-id="{{ $business->id }}"
                                                        data-value="0"
                                                        style="border: 2px solid #dc3545; color: #dc3545; background: #ffe3e3; padding: 3px; font-size: 10px;"
                                                        data-bs-toggle="tooltip" data-bs-placement="bottom"
                                                        title="Confidential">
                                                        Make it Public
                                                    </a>
                                                @else
                                                    <a href="#" id="confidentialBtn" data-id="{{ $business->id }}"
                                                        data-value="1"
                                                        style="border: 2px solid #dc3545; color: #dc3545; background: #ffe3e3; padding: 3px; font-size: 10px;"
                                                        data-bs-toggle="tooltip" data-bs-placement="bottom"
                                                        title="Confidential">
                                                        Make it Confidential
                                                    </a>
                                                @endif
                                            @endif
                                        @endif
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
                                    <!-- Left side: labels and values -->
                                    <div class="col-md-9">
                                        <div class="row">
                                            <div class="col-md-3">
                                                <label class="form-label">Security No. :&nbsp;</label>
                                            </div>
                                            <div class="col-md-9">
                                                <span>{{ $business->trustmark_id ?? 'N/A' }}</span>
                                            </div>

                                            <div class="col-md-3">
                                                <label class="form-label">Expired Date :&nbsp;</label>
                                            </div>
                                            <div class="col-md-9">
                                                <span>{{ $business->expired_date ? formatDatePH($business->expired_date) : 'N/A' }}</span>
                                            </div>

                                            <div class="col-md-3">
                                                <label class="form-label">Approved Date
                                                    :&nbsp;</label>
                                            </div>
                                            @if ($business->app_status_id == 1)
                                                <div class="col-md-9">
                                                    <span>{{ $business->admin_updated_at ? \Carbon\Carbon::parse($business->admin_updated_at)->format('F j, Y') : 'N/A' }}</span>
                                                </div>
                                            @else
                                                <div class="col-md-9">
                                                    <span>N/A</span>
                                                </div>
                                            @endif
                                        </div>
                                    </div>

                                    {{-- button to regenerate trustmark id --}}
                                    @if (Auth::user()->role == 2)
                                        @if ($business->trustmark_id == null && $business->status == 'UNDER EVALUATION')
                                            <div class="col-md-3 d-flex justify-content-end align-items-start">
                                                <a href="{{ route('business.regenerate-trustmark', $business->id) }}"
                                                    class="btn btn-primary" style="font-size: 12px;">
                                                    Generate Security No.
                                                </a>
                                            </div>
                                        @endif
                                    @endif
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
                                            <th style="width:2% !important;">No</th>
                                            <th style="width:10% !important;">Date | Time</th>
                                            <th style="width:25% !important;">User Name</th>
                                            <th style="width:10% !important;">Action</th>
                                            <th style="width:15% !important;">Audit Description</th>
                                            <th style="width:10% !important;">Status</th>
                                            <th style="width:25% !important;">Remarks</th>
                                            <th style="width:3% !important;">Location</th>
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
                            <div class="tab-pane" role="tabpanel" id="tab-corporations">
                                <div class="row" id="tab-corporations-content">
                                </div>
                            </div>
                        @endif

                        <div class="tab-pane" role="tabpanel" id="tab-detail">
                            <div class="divider-line"></div>
                            <h6 class="text-center multisteps-form__title"
                                style="font-family: sans-serif;color: rgb(0,0,0);font-weight: bold;">
                                Address Information
                                @if ((Auth::user()->role == 2 && $isAdmin == 1) || (Auth::user()->id == $business->evaluator_id)))
                                    <a href="javascript:void(0)" class="btn btn-primary edit-businessAddress"
                                        title="Make Edit" attr-id="{{ $business->id }}"
                                        style="float: inline-end;margin-right:20px;font-family:sans-serif;font-size:10px;padding: 1px 6px;border: none;background: #09325d;">
                                        <i class="fa fa-pencil"></i> Edit
                                    </a>
                                @endif
                            </h6>
                            <div class="divider-line"></div>
                            <div class="row">
                                <div class="col-md-3">
                                    <label class="form-label">Complete Address :&nbsp;</label>
                                </div>
                                <div class="col-md-9">
                                    <span> @if ((Auth::user()->role == 2 && $isAdmin == 1) || (Auth::user()->id == $business->evaluator_id))
                                        <input type="checkbox" name="add_comp_is_compliance" id="add_comp_is_compliance" class="compliance-check"  data-busn="{{ $business->id }}" {{ optional($business_compliance)->add_comp_is_compliance == 1 ? 'checked' : '' }}>
                                        
                                        @endif {{ $business->complete_address }}</span>
                                </div>
                                <div class="col-md-3">
                                    <label class="form-label">Region :&nbsp;</label>
                                </div>
                                <div class="col-md-9">
                                    <span>@if ((Auth::user()->role == 2 && $isAdmin == 1) || (Auth::user()->id == $business->evaluator_id))
                                        <input type="checkbox" name="add_region_is_compliance" id="add_region_is_compliance" class="compliance-check"  data-busn="{{ $business->id }}" {{ optional($business_compliance)->add_region_is_compliance == 1 ? 'checked' : '' }}> 
                                        @endif
                                         {{ $business->region->reg_region ?? 'N/A' }}</span>
                                </div>
                                <div class="col-md-3">
                                    <label class="form-label">Province :&nbsp;</label>
                                </div>
                                <div class="col-md-9">
                                    <span>@if ((Auth::user()->role == 2 && $isAdmin == 1) || (Auth::user()->id == $business->evaluator_id))
                                        <input type="checkbox" name="add_province_is_compliance" id="add_province_is_compliance" class="compliance-check"  data-busn="{{ $business->id }}" {{ optional($business_compliance)->add_province_is_compliance == 1 ? 'checked' : '' }}>
                                        @endif
                                         {{ $business->province->prov_desc ?? 'N/A' }}</span>
                                </div>
                                <div class="col-md-3">
                                    <label class="form-label">Municipality :</label>
                                </div>
                                <div class="col-md-9">
                                    <span>@if ((Auth::user()->role == 2 && $isAdmin == 1) || (Auth::user()->id == $business->evaluator_id))
                                        <input type="checkbox" name="add_muncity_is_compliance" id="add_muncity_is_compliance" class="compliance-check"  data-busn="{{ $business->id }}" {{ optional($business_compliance)->add_muncity_is_compliance == 1 ? 'checked' : '' }}> 
                                        @endif{{ $business->municipality->mun_desc ?? 'N/A' }}</span>
                                </div>
                                <div class="col-md-3">
                                    <label class="form-label">Barangay :&nbsp;</label>
                                </div>
                                <div class="col-md-9">
                                    <span>@if ((Auth::user()->role == 2 && $isAdmin == 1) || (Auth::user()->id == $business->evaluator_id))
                                        <input type="checkbox" name="add_barangay_is_compliance" id="add_barangay_is_compliance" class="compliance-check"  data-busn="{{ $business->id }}" {{ optional($business_compliance)->add_barangay_is_compliance == 1 ? 'checked' : '' }}>
                                        @endif {{ $business->barangay->brgy_name ?? 'N/A' }}</span>
                                </div>
                            </div>
                        </div>

                        <div class="tab-pane" role="tabpanel" id="tab-documents">
                            <div class="row" id="tab-documents-content">
                            </div>
                        </div>

                        @if ($business->status == 'UNDER EVALUATION')
                            @if (Auth::user()->role == 2)
                                <div class="tab-pane" role="tabpanel" id="tab-action">
                                    <form action="{{ route('business.admin_update', $business->id) }}" method="POST"
                                        id="businessForm">
                                        @csrf
                                        @method('PUT')
                                        <div class="row">
                                            <div class="col-md-11">
                                                <div class="mb-3">
                                                     <input type="hidden" id ="latitude" name="latitude" value="">
                                                     <input type="hidden" id ="longitude" name="longitude"  value="">
                                                    <label class="form-label">Evaluator <span
                                                            class="required-field">*</span></label>
                                                    <select class="form-select" name="evaluator_id" id="evaluator_id"
                                                        required>
                                                        <option value="" disabled selected>Select Evaluator</option>
                                                        @foreach ($Eveluator as $id => $Eveluatorname)
                                                            <option value="{{ $id }}"
                                                                {{ ($business->evaluator_id ?? '') == $id ? 'selected' : '' }}>
                                                                {{ $Eveluatorname }}
                                                            </option>
                                                        @endforeach
                                                    </select>
                                                </div>
                                            </div>
                                            <div class="col-md-1"
                                                style="padding-top: 27px;padding-left: 0px;padding-right: 0px;">
                                                <button type="button" class="btn btn-primary" id="saveEveluatorAssigned"
                                                    attr-id="{{ $business->id }}"
                                                    style="border: none;background: #09325d;width: 100%;">Assigned</button>
                                                    <button class="btn btn-primary" type="submit" id="status-action-btn"
                                                    style="border: none;background: #09325d;width: 100%;margin-top:10px;font-size: 15px;">
                                                    Update
                                                </button>
                                            </div>
                                            <p id="evaluator-msg" style="color:red; display:none;">⚠️ Please select an
                                                evaluator to enable these fields.</p>
                                        </div>
                                        <div class="row">
                                            @if ($business->evaluator_id > 0)
                                                <div class="col">
                                                    <div class="mb-3">
                                                        <label class="form-label">Status <span
                                                                class="required-field">*</span></label>
                                                        <select class="form-select" name="status_id" id="status_id"
                                                            required>
                                                            <option value="" disabled selected>Select Status</option>
                                                            @foreach ($status as $id => $name)
                                                                <option value="{{ $id }}" {{ $business->app_status_id == $id ? 'selected' : '' }}>
                                                                    {{ $name }}
                                                                </option>
                                                            @endforeach
                                                        </select>
                                                    </div>
                                                </div>
                                            @else
                                                <div class="col">
                                                    <div class="mb-3">
                                                        <label class="form-label">Status <span
                                                                class="required-field">*</span></label>
                                                        <select class="form-select" name="status_id" id="status_id"
                                                            required disabled>
                                                            <option value="" disabled selected>Select Status</option>
                                                            @foreach ($status as $id => $name)
                                                                <option value="{{ $id }}">{{ $name }}
                                                                </option>
                                                            @endforeach
                                                        </select>
                                                    </div>
                                                </div>
                                            @endif
                                            <p id="compliance-message" class="text-red-500 text-sm mt-2" style="display:none;color:red;">
                                                Evaluator must identify the compliance !!!
                                            </p>
                                            <div class="col">
                                                <div class="mb-3">
                                                    <label class="form-label">Reason <span
                                                            class="required-field">*</span></label>
                                                    <select class="form-select" name="reason_id" id="reason_id" required
                                                        disabled>
                                                        <option value="" disabled selected>Select Reason</option>
                                                        @foreach ($reasons as $reason)
                                                            <option value="{{ $reason->id }}" selected>
                                                                {{ $reason->description }}
                                                            </option>
                                                        @endforeach
                                                        
                                                    </select>
                                                </div>
                                            </div>

                                        </div>
                                        <div class="row">
                                            <div class="col">
                                                <div class="mb-3">
                                                    <label class="form-label">Remarks <span
                                                            class="required-field">*</span></label><br>
                                                    <textarea class="form-control custom-input" name="remark" id="remark" cols="30" rows="4" required>{{ old('remark', $business->admin_remarks ?? '') }}</textarea>
                                                </div>
                                                <p id="status-msg"
                                                    style="color:red; display:{{ session('status_error') ? 'block' : 'none' }};">
                                                    {{ session('status_error') }}
                                                </p>

                                            </div>
                                        </div>
                                       

                                    </form>
                                    


                                    <div class="toggle-box" id="myToggle">
                                    <div class="toggle-header">
                                        For Customer Guidelines
                                        <span class="toggle-arrow" style="color: #fff !important;">▼</span>
                                    </div>
                                    <div style="padding-top: 10px;">
                                    <button id="latestTab" class="tab-btn active" style="margin: 4px 0px 0px 17px;background: #09325d;color: #fff;padding: 7px;border-radius: 5px;">Latest Updates</button>
                                    <button id="historyTab" class="tab-btn" style="background: #09325d;color: #fff;padding: 7px;border-radius: 5px;">History</button>
                                    <button type="button" class="btn btn-primary mt-2" id="updateRemarksBtn" style="float:right;margin-bottom: 10px;background: #09325d;margin-right: 17px;">Update</button>
                                    </div>
                                    <div class="toggle-content">
                                   
                                    <div id="latestUpdatesDiv">
                                    <form id="complianceRemarksForm" style="margin-top: -24px;">
                                            @csrf
                                            
                                            <input type="hidden" name="busn_id" value="{{ $business->id }}" >
                                            <table class="table table-bordered" id="remarksTable" width="100%;" style="font-size: 12px;margin-top: 20px;">
                                                <thead>
                                                    <tr>
                                                    <th style="width:5%;color: #fff;background: #09325d;">No.</th>
                                                    <th style="width:45%;color: #fff;background: #09325d;">Description</th>
                                                    <th style="width:50%;color: #fff;background: #09325d;">Evaluator Comments</th>
                                                    </tr>
                                                </thead>
                                                <tbody>
                                                    <!-- Rows will be loaded via AJAX -->
                                                </tbody>
                                            </table>
                                            
                                        </form>

                                        {{-- Success message --}}
                                        <div id="updateSuccessMsg" class="alert alert-success mt-2" style="display:none;">
                                            Remarks updated successfully!
                                        </div>
                                    </div>
                                    <div id="historyDiv" style="display:none;">
                                        <table class="table table-bordered" id="remarksTableHistory" width="100%;" style="font-size: 12px;">
                                            <thead>
                                                <tr>
                                                <th style="width:5%;color: #fff;background: #09325d;">No.</th>
                                                <th style="width:45%;color: #fff;background: #09325d;">Description</th>
                                                <th style="width:50%;color: #fff;background: #09325d;">Evaluator Comments</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                <!-- Rows will be loaded via AJAX -->
                                            </tbody>
                                        </table>
                                    </div>
                                    </div>
                                    </div>


                                </div>

                            @endif
                        @elseif ($business->status == 'RETURNED')
                            @if (Auth::user()->role == 2)
                                <div class="tab-pane" role="tabpanel" id="tab-action">
                                    <div class="row">
                                        <div class="col-md-3">
                                            <label class="form-label">Status :&nbsp;</label>
                                        </div>
                                        <div class="col-md-9">
                                            <span>{{ $app_status->name ?? 'N/A' }}</span>
                                        </div>
                                        <div class="col-md-3">
                                            <label class="form-label">Reason :&nbsp;</label>
                                        </div>
                                        <div class="col-md-9">
                                            <span>{{ $app_canned_messages->description ?? 'N/A' }}</span>
                                        </div>
                                        <div class="col-md-3">
                                            <label class="form-label">Remarks :&nbsp;</label>
                                        </div>
                                        <div class="col-md-9">
                                            <span>{{ $business->admin_remarks ?? 'N/A' }}</span>
                                        </div>
                                    </div>
                                    <button class="btn btn-primary btn-sm" type="submit" id="status-action-btn"
                                        style="padding-left: 14px;margin-top: 21px;margin-left: 0px;padding-right: 14px;margin-bottom: 21px;font-size: 12px;font-family: sans-serif;">
                                        Update
                                    </button>
                                    <div class="toggle-box" id="myToggle">
                                        <div class="toggle-header">
                                        Compliance Guidelines
                                            <span class="toggle-arrow" style="color: #fff !important;">▼</span>
                                        </div>
                                    <div class="toggle-content">
                                            @php 
                                            $regLabel = '';
                                            if ($business) {
                                                    switch ($business->corporation_type) {
                                                        case 1:
                                                            $regLabel = 'Business Info: DTI Registration No.';
                                                            break;
                                                        case 2:
                                                            $regLabel = 'Business Info: SEC Registration No.';
                                                            break;
                                                        case 3:
                                                            $regLabel = 'Business Info: CDA Registration No.';
                                                            break;
                                                    }
                                                }
                                                $complianceFields = [
                                                    'busn_type_is_compliance' => 'Business Info: Business Type',
                                                    'busn_name_is_compliance' => 'Business Info: Business Name',
                                                    'busn_trade_is_compliance' => 'Business Info: Trade Name',
                                                    'busn_category_is_compliance' => 'Business Info: Business Category',
                                                    'busn_regno_is_compliance' => $regLabel,
                                                    'tin_is_compliance' => 'Business Info: Taxpayers Identification No.(TIN)',
                                                    'url_is_compliance' => 'Business URL | Website | Social Media Platform Link',
                                                    'authrep_name_is_compliance' => 'Authorized Representative: Name',
                                                    'authrep_mobile_is_compliance' => 'Authorized Representative: Mobile No.',
                                                    'authrep_email_is_compliance' => 'Authorized Representative: Email',
                                                    'authrep_govtid_is_compliance' => 'Authorized Representative: Gov-Issued ID',
                                                    'authrep_govtid_doc_is_compliance' => 'Authorized Representative: Gov-Issued Attachment',
                                                    'authrep_govtid_expiry_is_compliance' => 'Authorized Representative: Gov-Issued ID Expiry Date',
                                                    'add_comp_is_compliance' => 'Address Info: Complete Address',
                                                    'add_barangay_is_compliance' => 'Address Info: Barangay',
                                                    'add_muncity_is_compliance' => 'Address Info: Municipality',
                                                    'add_province_is_compliance' => 'Address Info: Province',
                                                    'add_region_is_compliance' => 'Address Info: Region',
                                                    'doc_busnreg_is_compliance' => 'Attachments: Business Registration',
                                                    'doc_bir_is_compliance' => 'Attachments: BIR 2303',
                                                    'doc_irm_is_compliance' => 'Attachments: Internal Redress Mechanism',
                                                    'doc_bmbe_is_compliance' => 'Attachments: BMBE',
                                                    'asset_category_is_compliance' => 'Attachments: Business Category (based on Asset Size)',
                                                    'asset_valuation_is_compliance' => 'Attachments: Proof of Total Asset Valuation',
                                                    'doc_addpermit_is_compliance' => 'Additional Permits (For Regulated Products)',
                                                ];
                                            @endphp

                                            <table class="table table-bordered" style="font-size: 12px;">
                                                <thead>
                                                    <tr>
                                                    <th style="width:5%;color: #fff;background: #09325d;">No.</th>
                                                    <th style="width:45%;color: #fff;background: #09325d;">Description</th>
                                                    <th style="width:50%;color: #fff;background: #09325d;">Evaluator Comments</th>
                                                    </tr>
                                                </thead>
                                                <tbody>
                                                    @php $no = 1; @endphp
                                                    @foreach ($complianceFields as $field => $label)
                                                        @if (optional($business_compliance)->{$field} == 1)
                                                            <tr>
                                                            <td>{{ $no++ }}</td>
                                                                <td>{{ $label }}</td>
                                                                <td style="padding:5px;">
                                                                    @php
                                                                        $remarksField = str_replace('_is_compliance', '_remarks', $field);
                                                                        $remarks = optional($business_compliance)->{$remarksField};
                                                                        $limit = 60; 
                                                                    @endphp

                                                                    @if(strlen($remarks) > $limit)
                                                                        <span class="short-text">{{ Str::limit($remarks, $limit) }}</span>
                                                                        <span class="full-text" style="display:none;">{{ $remarks }}</span>
                                                                        <a href="javascript:void(0);" class="toggle-remarks" style="color:blue;">Read more</a>
                                                                    @else
                                                                        {{ $remarks }}
                                                                    @endif
                                                                </td>
                                                            </tr>
                                                        @endif
                                                       
                                                    @endforeach
                                                </tbody>
                                            </table>


                                    </div>
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
                            @if (Auth::user()->role == 2)
                                    <div class="tab-pane" role="tabpanel" id="tab-action">
                                        <form action="{{ route('business.admin_update', $business->id) }}" method="POST"
                                            id="businessForm">
                                            @csrf
                                            @method('PUT')
                                            <div class="row">
                                                <div class="col-md-11">
                                                    <div class="mb-3">
                                                        <input type="hidden" id ="latitude" name="latitude" value="">
                                                        <input type="hidden" id ="longitude" name="longitude"  value="">
                                                        <label class="form-label">Evaluator <span
                                                                class="required-field">*</span></label>
                                                        <select class="form-select" name="evaluator_id" id="evaluator_id"
                                                            required>
                                                            <option value="" disabled selected>Select Evaluator</option>
                                                            @foreach ($Eveluator as $id => $Eveluatorname)
                                                                <option value="{{ $id }}"
                                                                    {{ ($business->evaluator_id ?? '') == $id ? 'selected' : '' }}>
                                                                    {{ $Eveluatorname }}
                                                                </option>
                                                            @endforeach
                                                        </select>
                                                    </div>
                                                </div>
                                                <div class="col-md-1"
                                                    style="padding-top: 27px;padding-left: 0px;padding-right: 0px;">
                                                    <button type="button" class="btn btn-primary" id="saveEveluatorAssigned"
                                                        attr-id="{{ $business->id }}"
                                                        style="border: none;background: #09325d;width: 100%;" disabled>Assigned</button>
                                                        <button class="btn btn-primary" type="submit" id="btn-payment"
                                                        style="border: none;background: #09325d;width: 100%;margin-top:10px;font-size: 15px;" disabled>
                                                        Update
                                                    </button>
                                                </div>
                                                <p id="evaluator-msg" style="color:red; display:none;">⚠️ Please select an
                                                    evaluator to enable these fields.</p>
                                            </div>
                                            <div class="row">
                                                @if ($business->evaluator_id > 0)
                                                    <div class="col">
                                                        <div class="mb-3">
                                                            <label class="form-label">Status <span
                                                                    class="required-field">*</span></label>
                                                            <select class="form-select" name="status_id" id="status_id"
                                                                required>
                                                                <option value="" disabled selected>Select Status</option>
                                                                @foreach ($status as $id => $name)
                                                                    <option value="{{ $id }}">{{ $name }}
                                                                    </option>
                                                                @endforeach
                                                            </select>
                                                        </div>
                                                    </div>
                                                @else
                                                    <div class="col">
                                                        <div class="mb-3">
                                                            <label class="form-label">Status <span
                                                                    class="required-field">*</span></label>
                                                            <select class="form-select" name="status_id" id="status_id"
                                                                required disabled>
                                                                <option value="" disabled selected>Select Status</option>
                                                                @foreach ($status as $id => $name)
                                                                    <option value="{{ $id }}">{{ $name }}
                                                                    </option>
                                                                @endforeach
                                                            </select>
                                                        </div>
                                                    </div>
                                                @endif
                                                <p id="compliance-message" class="text-red-500 text-sm mt-2" style="display:none;color:red;">
                                                    Evaluator must identify the compliance !!!
                                                </p>
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
                                                        <textarea class="form-control custom-input" name="remark" id="remark" cols="30" rows="4" required></textarea>
                                                    </div>
                                                    <p id="status-msg"
                                                        style="color:red; display:{{ session('status_error') ? 'block' : 'none' }};">
                                                        {{ session('status_error') }}
                                                    </p>

                                                </div>
                                            </div>
                                        

                                        </form>
                                        


                                        <div class="toggle-box" id="myToggle">
                                        <div class="toggle-header">
                                            For Customer Guidelines
                                            <span class="toggle-arrow" style="color: #fff !important;">▼</span>
                                        </div>
                                        <div style="padding-top: 10px;">
                                        <button id="latestTab" class="tab-btn active" style="margin: 4px 0px 0px 17px;background: #09325d;color: #fff;padding: 7px;border-radius: 5px;">Latest Updates</button>
                                        <button id="historyTab" class="tab-btn" style="background: #09325d;color: #fff;padding: 7px;border-radius: 5px;">History</button>
                                        <button type="button" class="btn btn-primary mt-2" id="updateRemarksBtn" style="float:right;margin-bottom: 10px;background: #09325d;margin-right: 17px;" disabled>Update</button>
                                        </div>
                                        <div class="toggle-content">
                                    
                                        <div id="latestUpdatesDiv">
                                        <form id="complianceRemarksForm" style="margin-top: -24px;">
                                                @csrf
                                                
                                                <input type="hidden" name="busn_id" value="{{ $business->id }}" >
                                                <table class="table table-bordered" id="remarksTable" width="100%;" style="font-size: 12px;margin-top: 20px;">
                                                    <thead>
                                                        <tr>
                                                        <th style="width:5%;color: #fff;background: #09325d;">No.</th>
                                                        <th style="width:45%;color: #fff;background: #09325d;">Description</th>
                                                        <th style="width:50%;color: #fff;background: #09325d;">Evaluator Comments</th>
                                                        </tr>
                                                    </thead>
                                                    <tbody>
                                                        <!-- Rows will be loaded via AJAX -->
                                                    </tbody>
                                                </table>
                                                
                                            </form>

                                            {{-- Success message --}}
                                            <div id="updateSuccessMsg" class="alert alert-success mt-2" style="display:none;">
                                                Remarks updated successfully!
                                            </div>
                                        </div>
                                        <div id="historyDiv" style="display:none;">
                                            <table class="table table-bordered" id="remarksTableHistory" width="100%;" style="font-size: 12px;">
                                                <thead>
                                                    <tr>
                                                    <th style="width:5%;color: #fff;background: #09325d;">No.</th>
                                                    <th style="width:45%;color: #fff;background: #09325d;">Description</th>
                                                    <th style="width:50%;color: #fff;background: #09325d;">Evaluator Comments</th>
                                                    </tr>
                                                </thead>
                                                <tbody>
                                                    <!-- Rows will be loaded via AJAX -->
                                                </tbody>
                                            </table>
                                        </div>
                                        </div>
                                        </div>


                                    </div>

                                @endif
                                <div class="tab-pane" role="tabpanel" id="tab-payment">
                                    <div class="row tab-section">
                                        <div class="col d-flex justify-content-center align-items-center">
                                            <img class="img-fluid"
                                                src="{{ asset('assets/img/DTI-BP-transparent.png') }}" alt="DTI Logo">
                                        </div>
                                        <div class="col">
                                        <div class="charges-table-wrapper">
                                                <div class="table-responsive text-end charges-table">
                                                    <table class="table">
                                                <thead>
                                                    <tr>
                                                        <th style="text-align:right;border: none;"><label for="receipt_number" class="form-label-inline" style="margin: 0px;">Transaction Reference Number
                                                    :&nbsp;</label></th>
                                                        <th style="text-align:right ;;border: none;width: 39%;"><span
                                                    class="form-value">{{ isset($payment) ? $payment->tranID : '' }}</span></th>
                                                    </tr> 

                                                    <tr>
                                                        <th style="text-align:right;border: none;"><label for="receipt_number" class="form-label-inline" style="margin: 0px;">Receipt Number
                                                    :&nbsp;</label></th>
                                                        <th style="text-align:right ;;border: none;width: 39%;"><span
                                                    class="form-value">{{ isset($payment) ? $payment->transaction_id : '' }}</span></th>
                                                    </tr> 
                                                    <tr>
                                                        <th style="text-align:right;border: none;"><label for="receipt_number" class="form-label-inline" style="margin: 0px;">Payment Channel
                                                    :&nbsp;</label></th>
                                                        <th style="text-align:right ;width: 39%;border: none;"><span
                                                    class="form-value">{{ isset($business) ? $business->payment_channel : '' }}</span></th>
                                                    </tr>
                                            </table>
                                            </div>
                                            </div>
                                            <!-- <div class="receipt-info">
                                                <label for="receipt_number" class="form-label-inline" style="margin-right: 86px;">Receipt Number
                                                    :&nbsp;</label>
                                                
                                            </div>
                                            <div class="receipt-info">
                                                <label for="receipt_number" class="form-label-inline" style="margin-right: 86px;">Payment Channel
                                                    :&nbsp;</label>
                                                <span
                                                    class="form-value">{{ isset($PaymentChannel) ? $PaymentChannel->channel_name : '' }}</span>
                                            </div> -->
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
                                            @if(count($refund_fee)>0)
                                                <div class="charges-table-wrapper">
                                                    <div class="table-responsive text-end charges-table">
                                                        <table class="table refund-details">
                                                            <thead>
                                                                <tr>
                                                                    <th class="table-heading">Refund</th>
                                                                    <th class="table-heading">PHP</th>
                                                                </tr>
                                                            </thead>
                                                            <tbody class="text-end">
                                                                @php
                                                                        $total=0;
                                                                @endphp
                                                                @forelse ($refund_fee as $fee)
                                                                    @php
                                                                         $total +=$fee->refund_amount;
                                                                    @endphp
                                                                    <tr>
                                                                        <td class="table-label">{{ $fee->fee_name }}</td>
                                                                        <td class="table-label">
                                                                            {{ number_format($fee->refund_amount, 2) }}</td>
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
                                                                        class="table-value total table-label">
                                                                        {{ number_format($total, 2) }}
                                                                    </td>
                                                                </tr>
                                                            </tbody>
                                                        </table>
                                                    </div>
                                                </div>
                                            @endif

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
    <div id="captureArea"
        style="position: absolute; top: -9999px; left: -9999px; width: 500px; height: 450px; border-radius: 15px; background: #fff; padding: 50px;">
        <div class="row p-3" style="border-radius: 15px;border: 21px solid rgb(62,67,134);width: 415px;">
            <div class="col text-center">
                <div class="col" style="text-align: center;margin-top: 20px;">
                    <div class="container  p-3">
                        <div class="d-flex justify-content-center align-items-start gap-1">
                            <div class="text-center">
                                <img src="{{ asset('assets/img/TRUSTMARK-SHIELD.png') }}" width="106"
                                    height="126" />
                            </div>
                            <div class="text-center d-flex flex-column align-items-center">
                                <img src="{{ asset($business->qr_code) }}" width="95" height="95" />
                                <!-- <img src="{{ asset('assets/img/qr_1_25072013313523.png') }}" width="95" height="95" /> -->
                                <img class="mt-0" src="{{ asset('assets/img/TRUSTMARK-REGISTERED-ONLY.png') }}"
                                    width="87" height="21" />
                                <p class="mb-0" style="font-size: 12px;">{{ $business->trustmark_id }}</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            @php
                $primaryUser = \App\Models\User::where('is_primary', 1)->first();
            @endphp
            @if ($primaryUser && $primaryUser->profile_photos)
                <div class="row mb-0 pb-0 text-center">
                    <div style="text-align: center;width:100%;">
                        <!-- <img src="{{ asset('assets/img/signature_1752930308.png') }}"
                                                                         width="150" height="100%"/> -->
                        <img src="{{ asset('storage/' . $primaryUser->profile_photos) }}" width="150"
                            height="100%" />

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

        @include('business/view_edit');
        <script>
        let complianceData = {};
        let remarksData = {};
        let busnId = "{{ $business->id ?? '' }}";
        let is_bmbe = "{{ $business->is_bmbe ?? '' }}"; 
        let busn_category_id = "{{ $business->busn_category_id ?? '' }}"; 
        function checkReturnedStatus() {
            const actionBtn = document.getElementById('status-action-btn');
            const messageEl = document.getElementById('compliance-message');

            const hasCompliantField = Object.values(complianceData).some(v => Number(v) === 1);
            const hasMissingRemarks = Object.keys(complianceData).some(key => {
                const isCompliant = Number(complianceData[key]) === 1;
                const remarkKey = key.replace('_is_compliance', '_remarks');
                const remarkValue = (remarksData[remarkKey] || '').trim();
                return isCompliant && remarkValue === '';
            });

            if (!hasCompliantField) {
                actionBtn.disabled = true;
                messageEl.style.display = 'block';
            } else if (hasMissingRemarks) {
                actionBtn.disabled = true;
                messageEl.style.display = 'block';
            } else {
                actionBtn.disabled = false;
                messageEl.style.display = 'none';
            }
        }



        function checkApprovedStatus() {
            const hasCompliant = Object.values(complianceData).some(v => Number(v) === 1);
            // Check BMBE and business category rule
            if ((is_bmbe == 0 || is_bmbe === '' || is_bmbe === null) && 
                (busn_category_id == 0 || busn_category_id === '' || busn_category_id === null)) {

                Swal.fire({
                    icon: 'warning',
                    title: 'Business Category Required',
                    text: 'Business category based on asset size must be defined !!',
                    confirmButtonText: 'OK',
                    confirmButtonColor: '#6c757d'
                });
                return false;
            }
            if (hasCompliant) {
                Swal.fire({
                    icon: 'warning',
                    title: 'Are you sure?',
                    html: `
                        Approval were not allowed due to pending details.<br>
                        Evaluator is required to clear the issues.
                    `,
                    confirmButtonText: 'OK',
                    confirmButtonColor: '#6c757d'
                });
                return false;
            }

            return true;
        }

        function updateButtonLabel() {
            const statusSelect = document.getElementById('status_id');
            const actionBtn = document.getElementById('status-action-btn');
            const messageEl = document.getElementById('compliance-message');

            const selectedOption = statusSelect.options[statusSelect.selectedIndex];
            const statusValue = selectedOption?.value || '';
            const statusName = selectedOption?.text?.trim().toUpperCase() || '';

            if (!statusValue) return;

            actionBtn.textContent = statusName;

            if (statusName === 'RETURNED') {
                checkReturnedStatus();
            } else {
                actionBtn.disabled = false;
                messageEl.style.display = 'none';
            }
            
            
        }

        document.addEventListener('DOMContentLoaded', function() {
            const statusSelect = document.getElementById('status_id');
            const actionBtn = document.getElementById('status-action-btn');
            const evaluatorId = "{{ $business->evaluator_id }}";
            const currentUserId = "{{ auth()->id() }}";

            statusSelect.addEventListener('change', updateButtonLabel);

            actionBtn.addEventListener('click', function(e) {
                const selectedOption = statusSelect.options[statusSelect.selectedIndex];
                const statusName = selectedOption?.text?.trim().toUpperCase() || '';
                if (statusName === 'APPROVED') {
                    const allowProceed = checkApprovedStatus();
                    if (!allowProceed) {
                        e.preventDefault();
                        return false;
                    }
                }
                if (['RETURNED', 'DISAPPROVED' , 'APPROVED'].includes(statusName)) {
                    if (evaluatorId != currentUserId) {
                        e.preventDefault();
                        Swal.fire({
                            icon: 'error',
                            title: 'Access Denied',
                            text: 'Application does not belong to your evaluator account.'
                        });
                        return false;
                    }
                }
            });
        });

        function loadRemarks() {
            $.ajax({
                url: "{{ route('business_compliance.getRemarks') }}",
                type: "GET",
                data: { busn_id: busnId },
                success: function(response) {
                    $('#remarksTable tbody').html(response.html);
                    complianceData = response.complianceFieldsData || {};
                    remarksData = response.remarksFieldsData || {};

                    const actionBtn = document.getElementById('status-action-btn');
                    if (actionBtn.textContent.trim().toUpperCase() === 'RETURNED') {
                        checkReturnedStatus();
                    }

                    const hasCompliance = Object.values(complianceData).some(v => Number(v) === 1);
                    $('#updateRemarksBtn').css('display', hasCompliance ? 'block' : 'none');
                    // $('#latestTab').css('display', hasCompliance ? 'block' : 'block');
                    // $('#historyTab').css('display', hasCompliance ? 'block' : 'block');
                },
                error: function() {
                    alert('Failed to load remarks data.');
                }
            });
        }
        function loadRemarksHistory() {
            $.ajax({
                url: "{{ route('business_compliance.getRemarksHistory') }}",
                type: "GET",
                data: { busn_id: busnId },
                beforeSend: function() {
                    // Show loader before the request starts
                    $('#remarksTableHistory tbody').html(`
                        <tr>
                            <td colspan="3" style="text-align:center; padding:20px;">
                                <div class="spinner-border text-primary" role="status" style="margin:0 auto;">
                                    <span class="visually-hidden">Loading...</span>
                                </div>
                            </td>
                        </tr>
                    `);
                },
                success: function(response) {
                    // Replace loader with data
                    $('#remarksTableHistory tbody').html(response.html);
                },
                error: function() {
                    // Show error message
                    $('#remarksTableHistory tbody').html(`
                        <tr>
                            <td colspan="3" class="text-center text-danger">
                                Failed to load remarks data.
                            </td>
                        </tr>
                    `);
                }
            });
        }

        </script>

        <script>
        $(document).on('shown.bs.tab', 'a[href="#tab-action"]', function(e) {
            loadRemarks();
        });
        document.addEventListener('DOMContentLoaded', function () {
            const tabTrigger = document.querySelector('a[href="#tab-documents"]');
            const tabContent = document.getElementById('tab-documents-content');
            let loaded = false;

            tabTrigger.addEventListener('shown.bs.tab', function () {
                if (!loaded) {
                    tabContent.innerHTML = '<div class="text-center p-3">Loading...</div>';

                    fetch("{{ route('tabs.tabDocument', ['id' => $business->id]) }}")
                        .then(response => response.text())
                        .then(html => {
                            tabContent.innerHTML = html;
                            loaded = true;
                            tabContent.addEventListener('change', function(e) {
                                const target = e.target;
                                if (target.classList.contains('compliance-check')) {
                                    const fieldName = target.name;
                                    const value = target.checked ? 1 : 0;
                                    const busnId = target.dataset.busn;
                                    fetch("{{ route('business_compliance.update') }}", {
                                        method: "POST",
                                        headers: {
                                            "Content-Type": "application/json",
                                            "X-CSRF-TOKEN": "{{ csrf_token() }}"
                                        },
                                        body: JSON.stringify({
                                            busn_id: busnId,
                                            field: fieldName,
                                            value: value
                                        })
                                    })
                                    .then(res => res.json())
                                    .then(data => console.log(data.message))
                                    .catch(err => console.error(err));
                                }
                            });
                        })
                        .catch(() => {
                            tabContent.innerHTML = '<div class="text-danger p-3">Failed to load content.</div>';
                        });
                }
            });
        });
        </script>


        <script>
        document.addEventListener('DOMContentLoaded', function () {
            const tabTrigger = document.querySelector('a[href="#tab-corporations"]');
            const tabContent = document.getElementById('tab-corporations-content');
            let loaded = false;

            tabTrigger.addEventListener('shown.bs.tab', function () {
                if (!loaded) {
                    tabContent.innerHTML = '<div class="text-center p-3">Loading...</div>';

                    fetch("{{ route('tabs.tabCorporations', ['id' => $business->id]) }}")
                        .then(response => response.text())
                        .then(html => {
                            tabContent.innerHTML = html;
                            loaded = true;

                            // --- Checkbox event delegation ---
                            tabContent.addEventListener('change', function(e) {
                                const target = e.target;
                                if (target.classList.contains('compliance-check')) {
                                    const fieldName = target.name;
                                    const value = target.checked ? 1 : 0;
                                    const busnId = target.dataset.busn;

                                    fetch("{{ route('business_compliance.update') }}", {
                                        method: "POST",
                                        headers: {
                                            "Content-Type": "application/json",
                                            "X-CSRF-TOKEN": "{{ csrf_token() }}"
                                        },
                                        body: JSON.stringify({
                                            busn_id: busnId,
                                            field: fieldName,
                                            value: value
                                        })
                                    })
                                    .then(res => res.json())
                                    .then(data => console.log(data.message))
                                    .catch(err => console.error(err));
                                }
                            });

                            // --- Modal DataTables for check-records ---
                            initCheckRecordsModals();
                        })
                        .catch(() => {
                            tabContent.innerHTML = '<div class="text-danger p-3">Failed to load content.</div>';
                        });
                }
            });

            // --- Initialize all check-records modals ---
            function initCheckRecordsModals() {
                let Table, businessTable, registrationTable;

                // First modal
                $(document).on('click', '.check-records', function () {
                    let businessId = $(this).attr('attr-id');
                    $('#checkRecordsModal').modal('show');

                    $('#checkRecordsModal').one('shown.bs.modal', function () {
                        if (!$.fn.DataTable.isDataTable('#checkRecordsTable')) {
                            Table = $('#checkRecordsTable').DataTable({
                                scrollX: true,
                                autoWidth: false,
                                paging: true,
                                searching: true,
                                ordering: true,
                                pageLength: 10,
                                ajax: '{{ route('business.check-records', ':id') }}'.replace(':id', businessId),
                                columnDefs: getColumnDefs(),
                                columns: getColumns()
                            });
                        } else {
                            Table.ajax.url('{{ route('business.check-records', ':id') }}'.replace(':id', businessId)).load();
                        }
                    });
                });

                // Second modal
                $(document).on('click', '.check-business-name', function () {
                    let businessId = $(this).attr('attr-id');
                    $('#checkRecordsModalBusiness').modal('show');

                    $('#checkRecordsModalBusiness').one('shown.bs.modal', function () {
                        if (!$.fn.DataTable.isDataTable('#checkRecordsTableBusiness')) {
                            businessTable = $('#checkRecordsTableBusiness').DataTable({
                                scrollX: true,
                                autoWidth: false,
                                paging: true,
                                searching: true,
                                ordering: true,
                                pageLength: 10,
                                ajax: '{{ route('business.check-business-name-records', ':id') }}'.replace(':id', businessId),
                                columnDefs: getColumnDefs(),
                                columns: getColumns()
                            });
                        } else {
                            businessTable.ajax.url('{{ route('business.check-business-name-records', ':id') }}'.replace(':id', businessId)).load();
                        }
                    });
                });

                // Third modal
                $(document).on('click', '.check-business-registration', function () {
                    let businessId = $(this).attr('attr-id');
                    $('#checkRecordsModalBusinessRegistration').modal('show');

                    $('#checkRecordsModalBusinessRegistration').one('shown.bs.modal', function () {
                        if (!$.fn.DataTable.isDataTable('#checkRecordsTableBusinessRegistration')) {
                            registrationTable = $('#checkRecordsTableBusinessRegistration').DataTable({
                                scrollX: true,
                                autoWidth: false,
                                paging: true,
                                searching: true,
                                ordering: true,
                                pageLength: 10,
                                ajax: '{{ route('business.check-business-Registration-records', ':id') }}'.replace(':id', businessId),
                                columnDefs: getColumnDefs(),
                                columns: getColumns()
                            });
                        } else {
                            registrationTable.ajax.url('{{ route('business.check-business-Registration-records', ':id') }}'.replace(':id', businessId)).load();
                        }
                    });
                });

                // Read more toggle
                $('#checkRecordsTable, #checkRecordsTableBusiness, #checkRecordsTableBusinessRegistration')
                    .on('click', '.toggle-text', function (e) {
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
            }

            function getColumnDefs() {
                return [
                    { width: '2%', targets: 0 },
                    { width: '12%', targets: 1 },
                    { width: '18%', targets: 2 },
                    { width: '18%', targets: 3 },
                    { width: '18%', targets: 4 },
                    { width: '12%', targets: 5 },
                    { width: '10%', targets: 6 },
                    { width: '12%', targets: 7 },
                    { width: '8%', targets: 8 },
                    { width: '5%', targets: 9 },
                    { width: '3%', targets: 10 }
                ];
            }

            function getColumns() {
                return [
                    {
                        data: null,
                        render: function (data, type, row, meta) {
                            return meta.row + 1 + meta.settings._iDisplayStart;
                        }
                    },
                    { data: 'SecurityNo' },
                    { data: 'BusinessName', render: renderWithReadMore },
                    { data: 'RegistrationNo', render: renderWithReadMore },
                    { data: 'Evaluator', render: renderWithReadMore },
                    { data: 'BusinessType' },
                    { data: 'TIN' },
                    { data: 'Representative' },
                    { data: 'Submitted' },
                    { data: 'Remarks', render: renderWithReadMore },
                    {
                        data: 'Status',
                        render: function (data) {
                            let badgeClass = '';
                            switch (data) {
                                case 'APPROVED': badgeClass = 'badge-bg-approve'; break;
                                case 'UNDER EVALUATION': badgeClass = 'badge-bg-evaluation'; break;
                                case 'RETURNED':
                                case 'DISAPPROVED': badgeClass = 'badge-bg-returned'; break;
                                default: badgeClass = 'badge-bg-draft';
                            }
                            return `<span class="badge ${badgeClass} px-2 py-1 small text-center d-inline-block" 
                                        style="min-width:100px;text-align:center!important;">${data}</span>`;
                        }
                    }
                ];
            }

            function renderWithReadMore(data) {
                if (!data) return '';
                if (data.length > 30) {
                    const shortText = data.substr(0, 30) + '...';
                    return `
                        <div class="text-container" style="white-space: normal; word-break: break-word;">
                            <span class="short-text">${shortText}</span>
                            <span class="full-text" style="display:none;">${data}</span>
                            <a href="#" class="toggle-text" style="color:blue;">Read more</a>
                        </div>`;
                }
                return `<div style="white-space: normal; word-break: break-word;">${data}</div>`;
            }
        });
        </script>

        <!-- Automatically download as PNG -->
        <!-- HTML2Canvas Auto-Download PNG Without Closing Page -->
        <!-- Include html2canvas -->
        <!-- Include html2canvas -->
        <script src="https://cdn.jsdelivr.net/npm/html2canvas@1.4.1/dist/html2canvas.min.js"></script>
        <script>
            $(document).ready(function() {
                function toggleFields() {
                    const evaluatorSelected = $("#evaluator_id").val();
                    const statusSelected = $("#status_id").val();

                    if (!evaluatorSelected) {
                        $("#status_id, #reason_id, #remark, #status-action-btn").prop("disabled", true);
                        $("#evaluator-msg").show();
                    } else {
                        $("#status_id, #reason_id, #remark").prop("disabled", false);
                        if (statusSelected) {
                            // $("#status-action-btn").prop("disabled", false);
                        } else {
                            $("#status-action-btn").prop("disabled", true);
                        }

                        $("#evaluator-msg").hide();
                    }
                }
                toggleFields();
                $("#evaluator_id").on("change", toggleFields);
                $("#status_id").on("change", toggleFields);
            });
        </script>
        






        <script>
            document.getElementById('confidentialBtn').addEventListener('click', function(e) {
                e.preventDefault();
                let btn = $(this);
                let businessId = btn.data('id');
                let value = btn.data('value');
                Swal.fire({
                    title: 'Are you sure?',
                    text: "This action is confidential!",
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonColor: '#17134a',
                    cancelButtonColor: '#dc3545',
                    confirmButtonText: 'Yes, proceed',
                    cancelButtonText: 'Cancel',
                    reverseButtons: true
                }).then((result) => {
                    if (result.isConfirmed) {
                        $.ajax({
                            url: "{{ route('business.confidential') }}", // your route
                            type: "POST",
                            data: {
                                id: businessId,
                                value: value,
                                _token: "{{ csrf_token() }}"
                            },
                            success: function(response) {
                                Swal.fire('Done!', response.message, 'success');
                                if (result.isConfirmed) {
                                    location.reload();
                                }
                            },
                            error: function(xhr) {
                                Swal.fire('Error!', 'Something went wrong.', 'error');
                            }
                        });
                    }
                });
            });
        </script>
        <script>
        $(document).on('click', '#downloadCertificateBtn', function() {
            let busnId = $(this).data('busn-id');
            let status = $(this).data('status');
            $.ajax({
                url: "{{ route('business.downloadCert') }}",
                type: "POST",
                data: {
                    _token: "{{ csrf_token() }}",
                    busn_id: busnId,
                    action_id: 10,
                    action_name: "downloaded",
                    status: status
                },
                success: function(res) {
                    console.log("Certificate download log saved:", res);
                },
                error: function(xhr) {
                    console.error("Log failed:", xhr.responseText);
                }
            });
        });
        </script>

        <script>
        document.getElementById('downloadQrBtn').addEventListener('click', function(e) {
            e.preventDefault();

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
            }).then(function(canvas) {
                const imgData = canvas.toDataURL('image/png');
                const link = document.createElement('a');
                link.href = imgData;
                link.download = `TMKQR_${timestamp}.png`;
                document.body.appendChild(link);
                link.click();
                document.body.removeChild(link);
                $.ajax({
                    url: "{{ route('business.submit_downloadQR') }}",  
                    type: "POST",
                    data: {
                        _token: "{{ csrf_token() }}",
                        busn_id: "{{ $business->id ?? '' }}", 
                        trustmark_id: "{{ $business->trustmark_id ?? '' }}", 
                        action_id: 17,                         
                        action_name: "paid",
                        status: "{{ $business->status ?? '' }}"
                    },
                    success: function(res) {
                        console.log("Log saved:", res);
                    },
                    error: function(xhr) {
                        console.error("Log failed:", xhr.responseText);
                    }
                });

            }).catch(err => {
                console.error('Canvas generation failed:', err);
            });
        });
        
        </script>
        <!-- <script>
            document.getElementById('downloadQrBtn').addEventListener('click', function() {
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
                }).then(function(canvas) {
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
        </script> -->
        <script src="https://cdn.jsdelivr.net/npm/html2canvas@1.4.1/dist/html2canvas.min.js"></script>

        <script>
            document.getElementById('businessForm').addEventListener('submit', function(e) {
                e.preventDefault(); // prevent immediate submission
                let status = $("#status_id option:selected").val();
                let reason = $("#reason_id option:selected").val();
                let remark = $("#remark").val();
                const evaluatorId = "{{ $business->evaluator_id }}";
                const currentUserId = "{{ auth()->id() }}";

                // Check if current user matches evaluator
                if (evaluatorId != currentUserId) {
                    Swal.fire({
                        icon: 'error',
                        title: 'Access Denied',
                        text: 'Application does not belong to the current user account. Please rectify the evaluator!'
                    });
                    return; 
                }
                Swal.fire({
                    title: 'Are you sure?',
                    text: "Do you want to submit this form?",
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonText: 'Yes, submit',
                    cancelButtonText: 'Cancel',
                    reverseButtons: true
                }).then((result) => {
                    if (result.isConfirmed) {
                        e.target.submit(); // submit the form if confirmed
                        $.ajax({
                            url: "{{ route('business.performance') }}",
                            type: "POST",
                            data: {
                                _token: "{{ csrf_token() }}",
                                id: "{{ $business->id }}",
                                evaluator_id: "{{ $business->evaluator_id }}",
                                status: $("#status_id option:selected").text()
                            }
                        });
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
        <script src="{{ asset('assets/js/fiuu_payment.js') }}?v={{ rand(100000, 999999) }}"></script>

        {{-- tomselect and cascading for admin action tab --}}
        <script>
            document.addEventListener('DOMContentLoaded', function() {
                const BASE_URL = document.getElementById('BASE_URL').value;
                new TomSelect('#evaluator_id', {
                    placeholder: "Select Evaluator",
                    valueField: 'id',
                    labelField: 'name',
                    searchField: 'name',
                    create: false,
                    loadThrottle: 400,
                    maxOptions: 50,
                    preload: false,
                    load: function(query, callback) {
                        if (!query.length) return callback();

                        $.ajax({
                            url: "{{ route('business.eveluatorsearch') }}",
                            data: {
                                q: query
                            },
                            success: function(res) {
                                callback(res);
                            },
                            error: function() {
                                callback();
                            }
                        });
                    }
                });
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
                        $.ajax({
                            url: "{{ route('business.submit_regenerateCert') }}",  
                            type: "POST",
                            data: {
                                _token: "{{ csrf_token() }}",
                                busn_id: "{{ $business->id ?? '' }}", 
                                trustmark_id: "{{ $business->trustmark_id ?? '' }}", 
                                action_id: 18,                         
                                action_name: "re-generated",
                                status: "{{ $business->status ?? '' }}"
                            },
                            success: function(res) {
                                console.log("Log saved:", res);
                            },
                            error: function(xhr) {
                                console.error("Log failed:", xhr.responseText);
                            }
                        });
                    }
                });
            }
            $(document).on("click", "#saveEveluatorAssigned", function() {
                let businessId = $(this).attr("attr-id");
                let evaluatorId = $("#evaluator_id").val();
                let evaluatorName = $("#evaluator_id option:selected").text();

                if (!evaluatorId) {
                    Swal.fire({
                        icon: "warning",
                        title: "No Evaluator Selected",
                        text: "Please select an evaluator first!"
                    });
                    return;
                }

                Swal.fire({
                    title: "Are you sure?",
                    text: "Assign application to evaluator " + evaluatorName + "?",
                    icon: "question",
                    showCancelButton: true,
                    confirmButtonText: 'Yes, assign',
                    cancelButtonText: 'Cancel',
                    reverseButtons: true,
                    customClass: {
                        confirmButton: 'swal-confirm-btn',
                        cancelButton: 'swal-cancel-btn'
                    },
                    buttonsStyling: false
                }).then((result) => {
                    if (result.isConfirmed) {
                        $.ajax({
                            url: "{{ route('business.assignEvaluator') }}",
                            type: "POST",
                            data: {
                                _token: "{{ csrf_token() }}",
                                business_id: businessId,
                                evaluator_id: evaluatorId,
                                lat:$('input[name="latitude"]').val(),
                                long:$('input[name="longitude"]').val(),
                                status:$("#status_id option:selected").val() ? $("#status_id option:selected").text() : "",
                                reason : $("#reason_id option:selected").val() ? $("#reason_id option:selected").text() : "",
                                remark:$('input[name="remark"]').val(),
                            },
                            success: function(response) {
                                if (response.success) {
                                    Swal.fire({
                                        icon: "success",
                                        title: "Assigned!",
                                        text: response.message,
                                        confirmButtonColor: "#09325d"
                                    }).then(() => {
                                        location.reload();
                                    });
                                } else {
                                    Swal.fire({
                                        icon: "error",
                                        title: "Oops...",
                                        text: response.message
                                    });
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
                });
            });
            window.addEventListener("pageshow", function (event) {
                // If navigation type is not reload
                if (event.persisted || performance.getEntriesByType("navigation")[0].type !== "reload") {
                    setTimeout(function () {
                        onloadUserlogsave();
                    }, 3000);
                }
            });

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
                            data: 'remarks',
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
                            data: 'location', className: 'text-center' 
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
        <script>
        $(document).ready(function() {
        $('#myToggle .toggle-header').on('click', function() {
            $('#myToggle').toggleClass('collapsed');
        });
        });
        </script>
        <script>
            $(document).ready(function() {
                $('.compliance-check').on('change', function() {
                    let fieldName = $(this).attr('name');   
                    let value = $(this).is(':checked') ? 1 : 0;
                    let busnId = $(this).data('busn');      

                    $.ajax({
                        url: "{{ route('business_compliance.update') }}",
                        type: "POST",
                        data: {
                            _token: "{{ csrf_token() }}",
                            busn_id: busnId,
                            field: fieldName,
                            value: value
                        },
                        success: function(response) {
                            console.log(response.message);
                        },
                        error: function(xhr) {
                            console.error("Error:", xhr.responseText);
                        }
                    });
                });
            });
            $(document).ready(function() {
                
                let busnId = "{{ $business->id ?? '' }}";
                // alert(busnId);
                    // function loadRemarks() {
                    //     $.ajax({
                    //         url: "{{ route('business_compliance.getRemarks') }}",
                    //         type: "GET",
                    //         data: { busn_id: busnId },
                    //         success: function(response) {
                    //             $('#remarksTable tbody').html(response.html);
                    //             $('#complianceFieldsData')= response.complianceFieldsData || {};
                    //         },
                    //         error: function() {
                    //             alert('Failed to load remarks data.');
                    //         }
                    //     });
                    // }
                    
                    $('#updateRemarksBtn').click(function() {
                        const evaluatorId = "{{ $business->evaluator_id }}";
                        const currentUserId = "{{ auth()->id() }}";

                        // Check if current user matches evaluator
                        if (evaluatorId != currentUserId) {
                            Swal.fire({
                                icon: 'error',
                                title: 'Access Denied',
                                text: 'Application does not belong to the current user account. Please rectify the evaluator!'
                            });
                            return; 
                        }
                    let formData = $('#complianceRemarksForm').serialize();

                    Swal.fire({
                        title: 'Are you sure?',
                        // text: "Save the following findings?",
                        icon: 'warning',
                        showCancelButton: true,
                        confirmButtonText: 'Update',
                        cancelButtonText: 'Cancel',
                        reverseButtons: true,
                        customClass: {
                            confirmButton: 'btn btn-success',
                            cancelButton: 'btn btn-danger'
                        },
                        buttonsStyling: false
                    }).then((result) => {
                        if (result.isConfirmed) {
                            $.ajax({
                                url: "{{ route('business_compliance.updateRemarks') }}",
                                type: "POST",
                                data: formData,
                                success: function(response) {
                                    if (response.success) {
                                        Swal.fire({
                                            title: 'Updated!',
                                            text: 'Compliance successfully updated !!!',
                                            icon: 'success',
                                            timer: 2000,
                                            showConfirmButton: false
                                        });
                                        loadRemarks();
                                    } else {
                                        Swal.fire('Error', 'Failed to update remarks.', 'error');
                                    }
                                },
                                error: function() {
                                    Swal.fire('Error', 'Something went wrong.', 'error');
                                }
                            });
                        }
                    });
                });
            });

            </script>
            <script>
            document.addEventListener('DOMContentLoaded', function() {
                document.querySelectorAll('.toggle-remarks').forEach(function(btn) {
                    btn.addEventListener('click', function() {
                        const td = this.closest('td');
                        const shortText = td.querySelector('.short-text');
                        const fullText = td.querySelector('.full-text');

                        if (fullText.style.display === 'none') {
                            fullText.style.display = 'inline';
                            shortText.style.display = 'none';
                            this.textContent = 'Read less';
                        } else {
                            fullText.style.display = 'none';
                            shortText.style.display = 'inline';
                            this.textContent = 'Read more';
                        }
                    });
                });
            });
            </script>
            <script>
            document.getElementById('latestTab').addEventListener('click', function() {
                document.getElementById('latestUpdatesDiv').style.display = 'block';
                document.getElementById('historyDiv').style.display = 'none';
                this.classList.add('active');
                document.getElementById('historyTab').classList.remove('active');
                loadRemarks();
            });

            document.getElementById('historyTab').addEventListener('click', function() {
                document.getElementById('latestUpdatesDiv').style.display = 'none';
                document.getElementById('historyDiv').style.display = 'block';
                this.classList.add('active');
                document.getElementById('latestTab').classList.remove('active');
                loadRemarksHistory();
                document.getElementById('updateRemarksBtn').style.display = 'none';
            });
            </script>
    @endsection
