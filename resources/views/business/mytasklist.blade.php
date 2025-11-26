@extends('layouts.app')

<style>
    /* Hide DataTables search bar */
    div.dataTables_filter {
        display: none;
    }
</style>

@section('content')
    <div class="d-flex justify-content-between align-items-center mb-4" style="margin-top: 15px;">
        <h3 class="page-title">Tasklist of Business Application</h3>
    </div>

    <ol class="breadcrumb custom-breadcrumb">
        <li class="breadcrumb-item"><a href="{{ route('dashboard') }}"><span>Dashboard</span></a></li>
        <li class="breadcrumb-item"><a href="{{ route('business.mytasklist') }}"><span>Tasklist of Business Application</span></a></li>
    </ol>
    <div class="row d-flex align-items-center justify-content-end">
            <div class="col-md-3" style="margin-top: -86px;padding: 0px;">
                <button type="button" id="summaryBtn" class="btn btn-primary" style="background: #09325d;float: inline-end;">
                Summary
                </button>
                
            </div>
            <div class="col-md-1" style="margin-top: -86px;">
                
                <a href="#" id="filter_box" class="btn btn-sm btn-primary action-item" role="button" data-bs-toggle="dropdown2" aria-haspopup="true" aria-expanded="false" style="background: #09325d;">
                    <i class="fa fa-filter" style="padding: 5px;"></i>
                </a>
                <a href="#" class="btn btn-sm btn-primary" type="button" id="columnDropdown" data-bs-toggle="dropdown" aria-haspopup="true" aria-expanded="false" style="padding: 4px;background: #09325d;">
                <i class="fas fa-bars" style="padding: 5px;"></i>                                  
                </a>
                <div class="dropdown-menu" aria-labelledby="columnDropdown">
                    <div class="card card-body" style="border: none;">
                    <label><input type="checkbox" id="chk_srno" class="toggle-column" data-column="0" checked> No.</label>
                        <label><input type="checkbox" id="chk_arpno" class="toggle-column" data-column="1" checked> Security No.</label>
                        <label><input type="checkbox" id="chk_taxpayer" class="toggle-column" data-column="2" checked> Business Name</label>
                        <label><input type="checkbox" id="chk_Address" class="toggle-column" data-column="3" checked> Registration No. </label>   
                        <label><input type="checkbox" id="chk_Address" class="toggle-column" data-column="4" > Business Type</label>
                        <label><input type="checkbox" id="chk_Address" class="toggle-column" data-column="5" checked> TIN</label>
                        <label><input type="checkbox" id="chk_arpno" class="toggle-column" data-column="6" > Representative</label>
                        <label><input type="checkbox" id="chk_taxpayer" class="toggle-column" data-column="7" > Evaluator</label>
                        <label><input type="checkbox" id="chk_Address" class="toggle-column" data-column="8" > Date Submitted</label>   
                        <label><input type="checkbox" id="chk_Address" class="toggle-column" data-column="9" > Date Returned</label>   
                        <label><input type="checkbox" id="chk_Address" class="toggle-column" data-column="10" > Date Approved</label>   
                        <label><input type="checkbox" id="chk_Address" class="toggle-column" data-column="11" > Date Issued</label>   
                        <label><input type="checkbox" id="chk_Address" class="toggle-column" data-column="12" > Date Expired</label>   
                        <label><input type="checkbox" id="chk_Address" class="toggle-column" data-column="13" checked> Payment</label>   
                        <label><input type="checkbox" id="chk_Address" class="toggle-column" data-column="14" checked> Remarks</label>
                        <label><input type="checkbox" id="chk_arpno" class="toggle-column" data-column="15" checked> Status</label>
                        <label><input type="checkbox" id="chk_taxpayer" class="toggle-column" data-column="16" checked disabled> Action</label>
                    </div>
                </div>
                <script>
                    document.querySelectorAll('.dropdown-menu label, .dropdown-menu input[type="checkbox"]').forEach(el => {
                        el.addEventListener('click', function(event) {
                            event.stopPropagation();
                        });
                    });
                </script>
            </div> 
        </div> 
        <!-- Modal -->
        <div class="modal fade" id="summaryModal" tabindex="-1" role="dialog" aria-labelledby="summaryModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-lg" role="document">
            <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" style="width: 100%;">Monthly Pending Summary</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                <span style="padding: 7px;font-size: 16px;">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <table class="table table-bordered" id="summaryTable">
                <thead>
                    <tr>
                    <th style="background: #09325d;font-size: 12px;color:#fff;">#</th>
                    <th style="background: #09325d;font-size: 12px;color:#fff;">No.</th>
                    <th style="background: #09325d;font-size: 12px;color:#fff;">Month</th>
                    <th style="background: #09325d;font-size: 12px;color:#fff;">Pending</th>
                    </tr>
                </thead>
                <tbody></tbody>
                </table>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancel</button>
            </div>
            </div>
        </div>
        </div>
    <style>
        .d-none {
            display: none;
        }
        .badge-bg-disapproved {
            border: 2px solid #dc3545;
            border-radius: 25px;
            color: #dc3545;
            background: #ffe3e3;
        }
        table.dataTable tbody td {
            padding: 8px 0px;
            font-size: 12px;
        }
    </style>
    {{-- Filter Form --}}
    <div class="card shadow" id="filterCard">
        <div class="card-body">
            <form method="GET" action="{{ route('business.mytasklist') }}" class="mb-4">
                 <input type="hidden" id ="latitude" name="latitude" value="">
                <input type="hidden" id ="longitude" name="longitude"  value="">
                <div class="row g-2 align-items-end">
                    <div class="col-md-3">
                        <label class="form-label custom-label">Evaluator </label>
                        @php
                            if (request('evaluator_id')) {
                                $selectedEvaluator = request('evaluator_id');
                            } elseif (!empty($businessEveluator) && !empty($businessEveluator->evaluator_id)) {
                                $selectedEvaluator = $businessEveluator->evaluator_id;
                            } elseif (!empty($businessEveluator) && $businessEveluator->evaluator_id === auth()->id()) {
                                $selectedEvaluator = auth()->id();
                            } else {
                                $selectedEvaluator = null;
                            }
                        @endphp
                        @if($selectedEvaluator > 0)
                        <select class="form-select custom-select" name="evaluator_id" id="evaluator_id">
                            <option value="" disabled {{ !$selectedEvaluator ? 'selected' : '' }}>Select Evaluator</option>
                            @foreach ($Eveluator as $id => $name)
                                <option value="{{ $id }}" {{ $selectedEvaluator == $id ? 'selected' : '' }}>
                                    {{ $name }}
                                </option>
                            @endforeach
                        </select>
                        @else
                        <select class="form-select custom-select" name="evaluator_id" id="evaluator_id" disabled>
                            <option value="" disabled {{ !$selectedEvaluator ? 'selected' : '' }}>Select Evaluator</option>
                            @foreach ($Eveluator as $id => $name)
                                <option value="{{ $id }}" {{ $selectedEvaluator == $id ? 'selected' : '' }}>
                                    {{ $name }}
                                </option>
                            @endforeach
                        </select>
                        @endif

                    </div>
                    <div class="col-md-2">
                        <label for="type" class="form-label custom-label">Business Type</label>
                        <select name="type" id="type" class="form-select custom-select">
                            <option value="">All</option>
                            @foreach ($types as $type)
                                <option value="{{ $type->id }}" {{ request('type') == $type->name ? 'selected' : '' }}>
                                    {{ $type->name }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-md-2">
                        <label for="payment" class="form-label custom-label">Payment</label>
                        <select name="payment" id="payment" class="form-select custom-select">
                            <option value="">All</option>
                            <option value="Unpaid" {{ request('payment') == 'Unpaid' ? 'selected' : '' }} selected>Unpaid</option>
                            <option value="Paid" {{ request('payment') == 'Paid' ? 'selected' : '' }}>Paid</option>
                        </select>
                    </div>
                    <div class="col-md-2">
                        <label for="status" class="form-label custom-label">Status</label>
                        <select name="status" id="status" class="form-select custom-select">
                            <option value="">All</option>
                            <option value="RETURNED" {{ request('status') == 'RETURNED' ? 'selected' : '' }}>RETURNED
                            </option>
                            <option value="APPROVED" {{ request('status') == 'APPROVED' ? 'selected' : '' }}>APPROVED
                            </option>
                            <option value="DISAPPROVED" {{ request('status') == 'DISAPPROVED' ? 'selected' : '' }}>DISAPPROVED
                            </option>
                            <option
                                value="UNDER EVALUATION"{{ request('status') == 'UNDER EVALUATION' ? 'selected' : '' }}>
                                UNDER EVALUATION</option>
                            <option
                            value="ON-HOLD"{{ request('status') == 'ON-HOLD' ? 'selected' : '' }}>
                            ON-HOLD</option>
                            <option value="DRAFT" {{ request('status') == 'DRAFT' ? 'selected' : '' }}>DRAFT</option>
                        </select>
                    </div>
                    <div class="col-md-2">
                        <label for="representative" class="form-label custom-label">Details</label>
                        <input type="text" name="details" id="details" class="form-control custom-input"
                            value="{{ request('details') }}" placeholder="Details">
                    </div>
                    <div class="col-md-1">
                        <button type="button" id="btn_search" class="btn btn-primary w-100"
                            style="font-family:sans-serif;font-size:14px;">Search</button>
                    </div>
                </div>
            </form>
        </div>
    </div>
    <br>
    
    <div id="wrapper">
        <div class="d-flex flex-column" id="content-wrapper">

            <div class="card shadow">
                <div class="card-body">
                    <div class="row">
                    </div>
                    <div class="table-responsive table mt-2" id="dataTable-1" role="grid" aria-describedby="dataTable_info" style="overflow-x: auto; white-space: nowrap;">
                        <table class="table my-0" id="jqtabledataTable" style="width:100%; table-layout: fixed;">
                            <thead>
                                <tr>
                                    <th class="custom-th" style="width: 50px;">No</th>
                                    <th class="custom-th" style="width: 120px;">Security No.</th>
                                    <th class="custom-th" style="width: 200px;">Business Name</th>
                                    <th class="custom-th" style="width: 150px;">Registration No.</th>
                                    <th class="custom-th" style="width: 150px;">Business Type</th>
                                    <th class="custom-th" style="width: 120px;">TIN</th>
                                    <th class="custom-th" style="width: 150px;">Representative</th>
                                    <th class="custom-th" style="width: 150px;">Evaluator</th>
                                    <th class="custom-th" style="width: 150px;">Date Submitted</th>
                                    <th class="custom-th" style="width: 150px;">Date Returned</th>
                                    <th class="custom-th" style="width: 150px;">Date Approved</th>
                                    <th class="custom-th" style="width: 150px;">Date Issued</th>
                                    <th class="custom-th" style="width: 150px;">Date Expired</th>
                                    <th class="custom-th" style="width: 100px;">Payment</th>
                                    <th class="custom-th" style="width: 150px;">Remark</th>
                                    <th class="custom-th" style="width: 100px;text-align:center;">Status</th>
                                    <th class="custom-th" style="width: 50px;text-align:center;">Action</th>
                                </tr>
                            </thead>

                            <tbody>
                               <!--- @forelse ($businesses as $index => $business)
                                    <tr>
                                        <td class="custom-td">{{ $loop->iteration }}</td>
                                        <td class="custom-td">{{ $business->trustmark_id ?? 'N/A' }}</td>
                                        <td class="custom-td">
                                        @php
                                            $text = $business->business_name ?? 'N/A';
                                            $previewLimit = 25;
                                            $isLong = strlen($text) > $previewLimit;
                                            $shortText = $isLong ? substr($text, 0, $previewLimit) . '...' : $text;
                                        @endphp

                                        <span class="short-text">{{ $shortText }}</span>

                                        @if ($isLong)
                                            <span class="full-text d-none">{{ $text }}</span>
                                            <a href="javascript:void(0);" class="read-toggle text-primary small">Read more</a>
                                        @endif</td>
                                        {{-- <td class="custom-td" title="{{ $business->reg_num }}">
                                            {{ \Illuminate\Support\Str::limit($business->reg_num, 25, '...') ?? 'N/A' }}
                                        </td> --}}
                                        <td class="custom-td">{{ $business->reg_num ?? 'N/A' }}</td>
                                        <td class="custom-td">{{ $business->corporationType->name ?? 'N/A' }}</td>
                                        <td class="custom-td">{{ $business->tin ?? 'N/A' }}</td>
                                        <td class="custom-td">{{ $business->pic_name ?? 'N/A' }}</td>
                                        <td class="custom-td">
                                        @php
                                            $paymentStatus = $business->payment_id === null ? 'Unpaid' : 'Paid';
                                            $paymentBadgeClass = match ($paymentStatus) {
                                                'Paid' => 'badge-bg-approve',     // green-like color
                                                'Unpaid' => 'badge-bg-returned',  // red-like color
                                                default => 'badge-bg-draft',      // fallback color
                                            };
                                        @endphp
                                        <span class="badge {{ $paymentBadgeClass }} px-2 py-1 small text-center d-inline-block" style="min-width: 80px;">
                                            {{ $paymentStatus }}
                                        </span>
                                        </td>
                                        <td class="custom-td">
                                        @php
                                            $text = $business->admin_remarks ?? 'N/A';
                                            $previewLimit = 20;
                                            $isLong = strlen($text) > $previewLimit;
                                            $shortText = $isLong ? substr($text, 0, $previewLimit) . '...' : $text;
                                        @endphp

                                        <span class="short-text">{{ $shortText }}</span>

                                        @if ($isLong)
                                            <span class="full-text d-none">{{ $text }}</span>
                                            <a href="javascript:void(0);" class="read-toggle text-primary small">Read more</a>
                                        @endif</td>
                                        <td class="custom-td text-center align-middle">
                                            @php
                                                $status = $business->status;
                                                $badgeClass = match ($status) {
                                                    'APPROVED' => 'badge-bg-approve',
                                                    'UNDER EVALUATION' => 'badge-bg-evaluation',
                                                    'RETURNED' => 'badge-bg-returned',
                                                    'DISAPPROVED' => 'badge-bg-disapproved',
                                                    default => 'badge-bg-draft',
                                                };
                                            @endphp

                                            <span class="badge {{ $badgeClass }} px-2 py-1 small text-center d-inline-block" style="min-width: 100px;">
                                                {{ $status }}
                                            </span>
                                        </td>
                                        <td style="text-align:center;">
                                            @if (Auth::user()->role == 1)
                                                @if ($business->status == 'UNDER EVALUATION' || $business->status == 'APPROVED')
                                                    <a href="{{ route('business.view', encrypt($business->id)) }}"
                                                        data-bs-toggle="tooltip" data-bs-placement="bottom" title="View">
                                                        <i class="custom-eye-icon fa fa-eye"></i>
                                                    </a>
                                                @elseif ($business->status == 'RETURNED')
                                                    <a href="{{ route('business.edit', encrypt($business->id)) }}"
                                                        data-bs-toggle="tooltip" data-bs-placement="bottom" title="Edit">
                                                        <i class="custom-pencil-icon fa fa-pencil"></i>
                                                    </a>
                                                @elseif ($business->status == 'DISAPPROVED')
                                                <a href="{{ route('business.disapproved_view', encrypt($business->id)) }}"
                                                    data-bs-toggle="tooltip" data-bs-placement="bottom" title="View">
                                                    <i class="custom-eye-icon fa fa-eye"></i>
                                                </a>
                                                @else
                                                    <a href="{{ route('business.create', encrypt($business->id)) }}"
                                                        data-bs-toggle="tooltip" data-bs-placement="bottom"
                                                        title="Continue">
                                                        <i class="custom-pencil-icon fa fa-arrow-right"></i>
                                                    </a>
                                                @endif
                                            @else
                                                @if ($business->status == 'UNDER EVALUATION')
                                                    <a href="{{ route('business.view', encrypt($business->id)) }}"
                                                        data-bs-toggle="tooltip" data-bs-placement="bottom"
                                                        title="Update">
                                                        <i class="custom-pencil-icon fa fa-pencil"></i>
                                                    </a>
                                                @elseif ($business->status == 'DISAPPROVED')
                                                    <a href="{{ route('business.disapproved_view', encrypt($business->id)) }}"
                                                        data-bs-toggle="tooltip" data-bs-placement="bottom" title="View">
                                                        <i class="custom-eye-icon fa fa-eye"></i>
                                                    </a>
                                                @else
                                                    <a href="{{ route('business.view', encrypt($business->id)) }}"
                                                        data-bs-toggle="tooltip" data-bs-placement="bottom"
                                                        title="View">
                                                        <i class="custom-eye-icon fa fa-eye"></i>
                                                    </a>
                                                @endif
                                            @endif
                                        </td>
                                    </tr>
                                @empty
                                    @if (Auth::user()->role == 1)
                                        <tr>
                                            <td class="custom-td text-center" colspan="6">No business found.</td>
                                        </tr>
                                    @else
                                        <tr>
                                            <td class="custom-td text-center" colspan="6">No business found.</td>
                                        </tr>
                                    @endif
                                @endforelse-->
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <style>
        button:not(:disabled), [type=button]:not(:disabled), [type=reset]:not(:disabled), [type=submit]:not(:disabled)
        {
            cursor: pointer;
            margin-right: 5px;
        }
        .ts-wrapper.form-control, .ts-wrapper.form-select
        {
            box-shadow: none;
            display: flex;
            height: 33px !important;
            padding: 0 !important;
        }
        /* .ts-control .item
        {
            align-items: center;
            display: flex;
            margin-top: -5px;
        } */
        .ts-wrapper.single .ts-control, .ts-wrapper.single .ts-control input {
            cursor: pointer;
            margin-top: -2px;
        }
    </style>
    <script>
        $(document).on('click', '.btn-delete', function (e) {
            e.preventDefault();
            
            let deleteUrl = $(this).data('url');
            let id = $(this).data('id');

            Swal.fire({
                title: 'Archive the selected details?',
                html: `
                    <label for="swal-password" style="display:block; margin-bottom:6px; font-weight:bold;">
                        Please enter your system password to continue.
                    </label>
                    <input type="password" id="swal-password" class="swal2-input" placeholder="Enter password">
                `,
                showCancelButton: true,
                confirmButtonText: 'Confirm Archive',
                cancelButtonText: 'Cancel',
                reverseButtons: true, 
                customClass: {
                    cancelButton: 'btn btn-danger',  
                    confirmButton: 'btn btn-primary' 
                },
                buttonsStyling: false, 
                focusConfirm: false,
                preConfirm: () => {
                    const password = document.getElementById('swal-password').value;
                    if (!password) {
                        Swal.showValidationMessage('Password is required');
                    }
                    return password;
                }
            }).then((result) => {
                if (result.isConfirmed) {
                    $.ajax({
                        url: deleteUrl,
                        type: "POST",
                        data: {
                            _token: "{{ csrf_token() }}",
                            lat:$('input[name="latitude"]').val(),
                            long:$('input[name="longitude"]').val(),
                            password: result.value
                        },
                        success: function (response) {
                            Swal.fire('Deleted!', response.message, 'success');
                            datatablefunction();
                        },
                        error: function (xhr) {
                            Swal.fire('Error!', xhr.responseJSON.message || 'Something went wrong.', 'error');
                        }
                    });
                }
            });
        });


    document.addEventListener('DOMContentLoaded', function () {
        document.querySelectorAll('.read-toggle').forEach(function(toggle) {
            toggle.addEventListener('click', function () {
                const td = this.closest('td');
                const shortText = td.querySelector('.short-text');
                const fullText = td.querySelector('.full-text');

                if (fullText.classList.contains('d-none')) {
                    // Show full, hide short
                    fullText.classList.remove('d-none');
                    shortText.classList.add('d-none');
                    this.textContent = 'Show less';
                } else {
                    // Show short, hide full
                    fullText.classList.add('d-none');
                    shortText.classList.remove('d-none');
                    this.textContent = 'Read more';
                }
            });
        });
    });
     $(document).ready(function() {
            datatablefunction();
            $("#btn_search").click(function() {
                datatablefunction();
            });
        })

        function datatablefunction(filters = {}) {
            var DIR = $("#DIR").val();
            const table = $('#jqtabledataTable').DataTable({
                destroy: true,
                processing: true,
                serverSide: true,
                pagingType: "full_numbers",
                //autoWidth: false,   // prevent fixed width
                // scrollX: false,  
                ajax: {
                    url: DIR + 'getmy-task-list',
                    type: 'GET',
                    data: {
                        q: $("#details").val(),
                        type: $('select[name="type"]').val(),
                        payment: $('select[name="payment"]').val(),
                        status: $('select[name="status"]').val(),
                        evaluator_id: $('select[name="evaluator_id"]').val(),
                        year: filters.year || '',
                        month: filters.month || ''
                    },
                    beforeSend: function() {
                        $('#custom-loader').show();
                    },
                    complete: function() {
                        $('#custom-loader').hide();
                    },
                    error: function() {
                        $('#custom-loader').hide();
                    }
                },
                language: {
                    emptyTable: `<div class="py-10 px-5 flex flex-col justify-center items-center text-center">
                <span class="icon-[tabler--search] shrink-0 size-6 text-base-content"></span>
                <div class="max-w-sm mx-auto">
                    <p class="mt-2 text-sm text-base-content/80">No search results</p>
                </div>
            </div>`,
                    info: 'Showing _START_ to _END_ of _TOTAL_',
                    infoEmpty: 'Showing 0 to 0 of 0',
                    infoFiltered: '(filtered from _MAX_ total)',
                    lengthMenu: 'Show _MENU_ entries',
                    paginate: {
                        previous: '‹',
                        next: '›',
                        first: '«',
                        last: '»'
                    }
                },
                dom: '<"top d-flex justify-content-between"l<"ml-auto"p>>rt<"bottom d-flex justify-content-between"ip>',
                pageLength: 10,
                lengthMenu: [10, 20, 30, 50],
                order: [],
                searching: false,
                columnDefs: [{
                        orderable: false,
                        targets: [0, 7]
                    },
                    {
                        className: 'text-start',
                        targets: 0
                    }
                ],
                columns: [{
                        data: "srno"
                    },
                    { data: "trustmark_id" },
                    {
                        data: "business_name",
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
                        data: "reg_num",
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
                        data: "business_type"
                    },
                    {
                        data: "tin"
                    },
                    {
                        data: "representative"
                    },
                    {
                        data: "Evaluator"
                    },
                    {
                        data: "date_submitted"
                    },
                    {
                        data: "date_returned"
                    },
                    {
                        data: "date_approved"
                    },
                    {
                        data: "date_issued"
                    },
                    {
                        data: "expired_date"
                    },
                    {
                        data: "paymnetsttaus"
                    },
                    {
                        data: "remark",
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
                        data: "status"
                    },
                    {
                        data: "action",
                        createdCell: function (td, cellData, rowData, row, col) {
                            $(td).css("padding-left", "20px");
                        }
                    }
                ]
            });
            $(document).on('change', '.toggle-column', function() {
                const column = table.column($(this).data('column'));
                column.visible(this.checked);
            });
            $('.toggle-column').each(function() {
                const column = table.column($(this).data('column'));
                column.visible(this.checked);
            });
        };
        $('#jqtabledataTable').on('click', '.toggle-text', function(e) {
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
        document.addEventListener('DOMContentLoaded', function() {
            new TomSelect('#evaluator_id', {
                placeholder: "Select Evaluator",
                valueField: 'id',
                labelField: 'name',
                searchField: 'name',
                create: false,
                loadThrottle: 400,
                maxOptions: 50,
                preload: false,
                allowEmptyOption: true, 
                plugins: ['clear_button'], 
                load: function(query, callback) {
                    if (!query.length) return callback();

                    $.ajax({
                        url: "{{ route('business.eveluatorsearch') }}",
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
        });
    </script>
    <script>
    $(document).on('click', '.btn-secondary', function() {
        $('#summaryModal').modal('hide');
    });
    $(document).on('click', '.close', function() {
        $('#summaryModal').modal('hide');
    });
    $(document).on('click', '#summaryBtn', function () {
    let evaluator_id = $('#evaluator_id').val();

    if (!evaluator_id) {
        Swal.fire({
            icon: 'warning',
            title: 'Evaluator Required',
            text: 'Please select an evaluator first before viewing the summary.',
            confirmButtonColor: '#3085d6',
        });
        return;
    }

    $.ajax({
        url: "{{ route('monthly.pending.summaryEvaluator_id') }}",
        type: "POST",
        dataType: "json",
        data: {
            evaluator_id: evaluator_id,
            _token: "{{ csrf_token() }}"
        },
        success: function (data) {
            Swal.close(); 
            let tbody = $('#summaryTable tbody');
            tbody.empty();

            if (data.length === 0) {
                Swal.fire({
                    icon: 'info',
                    title: 'No Pending Records',
                    text: 'No pending records found for this evaluator.',
                    confirmButtonColor: '#3085d6',
                });
            } else {
                $.each(data, function (index, item) {
                    tbody.append(`
                        <tr>
                            <td style="font-size: 12px;">
                                <input type="checkbox" class="summary-check" 
                                    data-month="${item.Month}" 
                                    data-evaluator="${evaluator_id}" 
                                    data-pending="${item.Pending}">
                            </td>
                            <td style="font-size: 12px;">${index + 1}</td>
                            <td style="font-size: 12px;">${item.Month}</td>
                            <td style="font-size: 12px;">${item.Pending}</td>
                        </tr>
                    `);
                });

                $('#summaryModal').modal('show');
            }
        },
        error: function (xhr, status, error) {
            Swal.close();
            Swal.fire({
                icon: 'error',
                title: 'Error',
                text: 'Something went wrong while fetching the summary!',
                footer: error
            });
            console.error("AJAX Error:", error);
        }
    });
});
// When user checks a summary checkbox
$(document).on('change', '.summary-check', function () {
    $('.summary-check').not(this).prop('checked', false);

    if ($(this).is(':checked')) {
        const monthText = $(this).data('month');
        const evaluatorId = $(this).data('evaluator');
        const pending = $(this).data('pending');
        const [year, monthName] = monthText.split(' - ');
        const month = new Date(`${monthName} 1, ${year}`).getMonth() + 1;
        console.log(`Selected: Month=${month}, Year=${year}, Evaluator=${evaluatorId}`);
        datatablefunction({
            evaluator_id: evaluatorId,
            year: year,
            month: month
        });
        $('#pendingCount').text(pending);
    } else {
        $('#jqtabledataTable').DataTable().clear().draw();
        $('#pendingCount').text('0');
    }
});



document.getElementById('filter_box').addEventListener('click', function (e) {
    e.preventDefault();
    const card = document.getElementById('filterCard');
    const isHidden = window.getComputedStyle(card).display === 'none';
    card.style.display = isHidden ? 'block' : 'none';
});
</script>

@endsection
