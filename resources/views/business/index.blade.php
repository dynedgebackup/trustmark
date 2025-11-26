@extends('layouts.app')

<style>
    /* Hide DataTables search bar */
    div.dataTables_filter {
        display: none;
    }
    button:not(:disabled), [type=button]:not(:disabled), [type=reset]:not(:disabled), [type=submit]:not(:disabled)
        {
            cursor: pointer;
            margin-right: 5px;
        }
        table.dataTable tbody td {
            padding: 8px 0px;
            font-size: 12px;
        }

</style>

@section('content')
    <div class="d-flex justify-content-between align-items-center mb-4" style="margin-top: 15px;">
        @if (Auth::user()->role == 1)
            <h3 class="page-title">My Applications</h3>
        @else
            <h3 class="page-title">Manage Pending</h3>
        @endif
    </div>

    <ol class="breadcrumb custom-breadcrumb">
        <li class="breadcrumb-item"><a href="{{ route('dashboard') }}"><span>Dashboard</span></a></li>
        <li class="breadcrumb-item"><a href="{{ route('business.index') }}"><span>Business Registry</span></a></li>
    </ol>
    @if (Auth::user()->role != 1)
        
        <div class="row d-flex align-items-center justify-content-end">
            <div class="col-md-3" style="margin-top: -86px;padding: 0px;">
                <button type="button" id="summaryBtn" class="btn btn-primary" style="background: #09325d;float: inline-end;">
                Summary
                </button>
                <button type="button" id="saveBtn" class="btn btn-primary" style="background: #09325d;float: inline-end;">
                Bulk Assigment
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
									
                        <label><input type="checkbox" id="chk_srno" class="toggle-column" data-column="0" checked> #</label>
                        <label><input type="checkbox" id="chk_srno" class="toggle-column" data-column="1" checked> No.</label>
                        <label><input type="checkbox" id="chk_arpno" class="toggle-column" data-column="2" checked> Security No.</label>
                        <label><input type="checkbox" id="chk_taxpayer" class="toggle-column" data-column="3" checked> Business Name</label>
                        <label><input type="checkbox" id="chk_Address" class="toggle-column" data-column="4" checked> Registration No. </label>   
                        <label><input type="checkbox" id="chk_Address" class="toggle-column" data-column="5" > TIN</label>
                        <label><input type="checkbox" id="chk_Address" class="toggle-column" data-column="6" checked> Business Type</label>
                        <label><input type="checkbox" id="chk_arpno" class="toggle-column" data-column="7" > Representative</label>
                        <label><input type="checkbox" id="chk_taxpayer" class="toggle-column" data-column="8" > Submitted</label>
                        <label><input type="checkbox" id="chk_Address" class="toggle-column" data-column="9" > No. of Days</label>   
                        <label><input type="checkbox" id="chk_Address" class="toggle-column" data-column="10" checked> Remarks</label>
                        <label><input type="checkbox" id="chk_arpno" class="toggle-column" data-column="11" checked> Status</label>
                        <label><input type="checkbox" id="chk_taxpayer" class="toggle-column" data-column="12" checked disabled> Action</label>
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
        @else
        <div class="row d-flex align-items-center justify-content-end">
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
                        <label><input type="checkbox" id="chk_Address" class="toggle-column" data-column="4" > TIN</label>
                        <label><input type="checkbox" id="chk_Address" class="toggle-column" data-column="5" checked> Business Type</label>
                        <label><input type="checkbox" id="chk_arpno" class="toggle-column" data-column="6" > Representative</label>
                        <label><input type="checkbox" id="chk_taxpayer" class="toggle-column" data-column="7" > Submitted</label>
                        <label><input type="checkbox" id="chk_Address" class="toggle-column" data-column="8" > No. of Days</label>   
                        <label><input type="checkbox" id="chk_Address" class="toggle-column" data-column="9" checked> Remarks</label>
                        <label><input type="checkbox" id="chk_arpno" class="toggle-column" data-column="10" checked> Status</label>
                        <label><input type="checkbox" id="chk_taxpayer" class="toggle-column" data-column="11" checked disabled> Action</label>
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
    @endif
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
    </style>
    {{-- Filter Form --}}
    @if (Auth::user()->role != 1)
        <div class="card shadow" id="filterCard">
            <div class="card-body">
                <form method="GET" action="{{ route('business.index') }}" class="mb-4">
                    <div class="row g-2 align-items-end">
                        <div class="col-md-3">
                             <input type="hidden" id ="latitude" name="latitude" value="">
                             <input type="hidden" id ="longitude" name="longitude"  value="">
                            <label for="type" class="form-label custom-label">Business Type</label>
                            <select name="type" id="type" class="form-select custom-select">
                                <option value="">All</option>
                                @foreach ($types as $type)
                                    <option value="{{ $type->id }}" {{ request('type') == $type->id ? 'selected' : '' }}>
                                        {{ $type->name }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-xl-2 col-lg-2 col-md-6 col-sm-12 col-12">
                            <div class="btn-box">
                            <label for="Search" class="form-label custom-label">{{ __('From Date') }}</label>
                            <input type="date" name="fromdate" id="fromdate" class="form-control custom-input" value="{{ date('Y-m-d') }}"  style="font-size:12px;">
                            </div>
                        </div>
                        <div class="col-xl-2 col-lg-2 col-md-6 col-sm-12 col-12">
                            <div class="btn-box">
                            <label for="Search" class="form-label custom-label">{{ __('To Date') }}</label>
                            <input type="date" name="todate" id="todate" class="form-control custom-input" value="{{ date('Y-m-d') }}"   style="font-size:12px; ">
                            </div>
                        </div>
                        <!-- <div class="col-md-2">
                            <label for="payment" class="form-label custom-label">Payment</label>
                            <select name="payment" id="payment" class="form-select custom-select">
                                <option value="">All</option>
                                <option value="Unpaid" {{ request('payment') == 'Unpaid' ? 'selected' : '' }}>Unpaid</option>
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
                                <option value="DRAFT" {{ request('status') == 'DRAFT' ? 'selected' : '' }}>DRAFT</option>
                            </select>
                        </div> -->
                        <div class="col-md-4">
                            <label for="representative" class="form-label custom-label">Details</label>
                            <input type="text" name="details" id="details" class="form-control custom-input"
                                value="{{ request('details') }}" placeholder="Details">
                        </div>
                        <div class="col-md-1">
                            <button type="button" id="btn_search"  class="btn btn-primary w-100"
                                style="font-family:sans-serif;font-size:14px;">Search</button>
                        </div>
                    </div>
                </form>
            </div>
        </div>
        <br>
        @else
        <div class="card shadow" id="filterCard">
            <div class="card-body">
                <form method="GET" action="{{ route('business.index') }}" class="mb-4">
                    <div class="row d-flex align-items-center justify-content-end">
                        <div class="col-md-4">
                            <label for="representative" class="form-label custom-label">Details</label>
                            <input type="text" name="details" id="details" class="form-control custom-input"
                                value="{{ request('details') }}" placeholder="Details">
                        </div>
                        <div class="col-md-1" style="padding-top: 24px;">
                        <a href="#" class="btn btn-sm btn-primary" id="btn_search" style="padding:9px;background: #09325d;">
                            <span class="btn-inner--icon"><i class="fas fa-search"></i></span>
                        </a>
                        <a href="#" class="btn btn-sm btn-danger" id="btn_clear" style="padding:9px;">
                            <span class="btn-inner--icon"><i class="fas fa-trash "></i></span>
                        </a>
                            
                        </div>
                    </div>
                </form>
            </div>
        </div>
        <br>

    @endif

    <div id="wrapper">
        <div class="d-flex flex-column" id="content-wrapper">

            <div class="card shadow">
                <div class="card-body">
                    <div class="row">
                    </div>
                    <div class="table-responsive table mt-2" id="dataTable-1" role="grid" aria-describedby="dataTable_info" style="overflow-x: auto; white-space: nowrap;">
                        <table class="table my-0" id="dataTable" style="width:100%; table-layout: fixed;">
                            <thead>
                                <tr>
                                    @if (Auth::user()->role != 1)
                                        <th class="custom-th" style="width: 50px;"><input type="checkbox" id="checkAll"></th>
                                    @endif
                                    <th class="custom-th" style="width: 50px;">No</th>
                                    <th class="custom-th" style="width: 120px;">Security No.</th>
                                    <th class="custom-th" style="width: 200px;">Business Name</th>
                                    <th class="custom-th" style="width: 150px;">Registration No.</th>
                                    
                                    <th class="custom-th" style="width: 120px;">TIN</th>
                                    <th class="custom-th" style="width: 150px;">Business Type</th>
                                    <th class="custom-th" style="width: 150px;">Representative</th>
                                    <!-- <th class="custom-th" style="width: 100px;">Payment</th> -->
                                    <th class="custom-th" style="width: 100px;">Submitted</th>
                                    <th class="custom-th" style="width: 100px;">No. of Days</th>
                                    <th class="custom-th" style="width: 150px;">Remarks</th>
                                    <th class="custom-th" style="width: 100px;text-align:center;">Status</th>
                                    <th class="custom-th" style="width: 50px;text-align:center;">Action</th>
                                </tr>
                            </thead>

                            <tbody>
                               
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <!-- Bulk Assigment -->
<div class="modal fade" id="business_informationModal" tabindex="-1" aria-labelledby="business_informationModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
        <div class="modal-header">
            <h5 class="modal-title" id="business_informationModalLabel">Manage Bulk Assigment</h5>
            <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
        </div>
        <div class="modal-body">
            <form id="business_informationForm">
            @csrf
                <div class="row">
                    <div class="col-12 col-sm-6 col-md-12">
                        <div class="mb-3">
                        <input type="hidden" id="selectedIds" value="">
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
<input type="hidden" id="role_id" value="{{ Auth::user()->role }}">
<script>
$(document).on('click', '.custom-eye-btn', function(e) {
    let busnId = $(this).data('busn-id');
    let trustmarkId = $(this).data('trustmark-id');
    let status = $(this).data('status');
    $.ajax({
        url: "{{ route('business.submit_userlogs') }}",
        type: "POST",
        data: {
            _token: "{{ csrf_token() }}",
            busn_id: busnId,
            action_id: 13,
            action_name: "view",
            trustmark_id: trustmarkId,
            status: status
        },
        success: function(res) {
            console.log("Log saved:", res);
        },
        error: function(xhr) {
            console.error("Log failed:", xhr.responseText);
        }
    });
});
</script>

    <script>
       $(document).on('click', '.btn-delete-business', function (e) {
    e.preventDefault();

    const deleteUrl = $(this).data('url');
    const id = $(this).data('id');

    Swal.fire({
        title: 'Are you sure?',
        html: `<p>Permanently delete this draft application?</p>`,
        icon: 'warning',
        showCancelButton: true,
        confirmButtonText: 'Yes, delete it',
        cancelButtonText: 'Cancel',
        reverseButtons: true,
        customClass: {
            confirmButton: 'btn btn-primary',
            cancelButton: 'btn btn-danger'
        },
        buttonsStyling: false,
        focusCancel: true,
    }).then((result) => {
        if (result.isConfirmed) {
            $.ajax({
                url: deleteUrl,
                type: "POST",
                data: {
                    id: id,
                    _token: $('meta[name="csrf-token"]').attr('content')
                },
                beforeSend: function() {
                    Swal.fire({
                        title: 'Deleting...',
                        text: 'Please wait while we process your request.',
                        allowOutsideClick: false,
                        didOpen: () => Swal.showLoading()
                    });
                },
                success: function (response) {
                    Swal.fire({
                        title: 'Deleted!',
                        text: response.message || 'The draft application has been deleted successfully.',
                        icon: 'success',
                        timer: 1500,
                        showConfirmButton: false
                    });
                    if (typeof datatablefunction === 'function') {
                        datatablefunction(); // Refresh table if defined
                    }
                },
                error: function (xhr) {
                    Swal.fire({
                        title: 'Error!',
                        text: xhr.responseJSON?.message || 'Something went wrong while deleting.',
                        icon: 'error'
                    });
                }
            });
        }
    });
});


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
        $(document).ready(function () {
            $("#checkAll").on("change", function () {
                $(".row-check").prop("checked", this.checked);
            });
            $(document).on("change", ".row-check", function () {
                if ($(".row-check:checked").length === $(".row-check").length) {
                    $("#checkAll").prop("checked", true);
                } else {
                    $("#checkAll").prop("checked", false);
                }
            });
        });
        $(document).ready(function () {
        // Save button outside modal
        $("#saveBtn").on("click", function () {
            var checkedRows = $(".row-check:checked");

            if (checkedRows.length === 0) {
                Swal.fire({
                    icon: "warning",
                    title: "No row selected",
                    text: "Please select at least one row before saving.",
                    confirmButtonColor: "#3085d6",
                    confirmButtonText: "OK"
                });
            } else {
                // collect all ids
                var ids = [];
                checkedRows.each(function () {
                    ids.push($(this).val());
                });

                // set hidden input + count
                $("#selectedIds").val(ids.join(","));
                $("#checkedCount").text(ids.length);
                $("#business_informationModal").modal("show");
            }
        });
        $("#saveBusinessInformation").on("click", function () {
            var ids = $("#selectedIds").val();
            var evaluator_id = $("#evaluator_id").val();
            var evaluator_name = $("#evaluator_id option:selected").text();
            if (!evaluator_id) {
                Swal.fire({
                    icon: "warning",
                    title: "Required!",
                    text: "Please select an evaluator before updating.",
                    confirmButtonColor: "#3085d6"
                });
                return; 
            }
            Swal.fire({
                title: "Are you sure you want to proceed?",
                text: "Evaluator: " + evaluator_name,
                icon: "warning",
                showCancelButton: true,
                confirmButtonText: "Yes",
                cancelButtonText: "No",
                confirmButtonColor: "#3085d6",  
                cancelButtonColor: "#d33",     
                reverseButtons: true            
            }).then((result) => {
                if (result.isConfirmed) {
                    $.ajax({
                        url: "{{ route('business.bulkassigment') }}",
                        method: "POST",
                        data: {
                            ids: ids,
                            evaluator_id: evaluator_id,
                            _token: "{{ csrf_token() }}"
                        },
                        success: function (response) {
                            Swal.fire({
                                icon: "success",
                                title: "Updated!",
                                text: "Selected rows updated successfully.",
                                confirmButtonColor: "#3085d6"
                            }).then(() => {
                                $("#business_informationModal").modal("hide");
                                datatablefunction();
                            });
                        },
                        error: function () {
                            Swal.fire({
                                icon: "error",
                                title: "Error",
                                text: "Something went wrong while updating.",
                            });
                        }
                    });
                }
            });
        });
    });



    document.addEventListener('DOMContentLoaded', function () {
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

    $(document).ready(function(){
         datatablefunction();
          $("#btn_search").click(function(){
        datatablefunction();
        }); 
    })

   function  datatablefunction() {
    var DIR =$("#DIR").val();
    var role_id = $("#role_id").val();
    if(role_id!=1){
            const table = $('#dataTable').DataTable({
            destroy: true,
            processing: true,
            serverSide: true,
            pagingType: "full_numbers",
            ajax: {
                url: DIR +'application/getList',
                type: 'GET',
                data: {
                    q: $("#details").val(),
                    manufacturertype:$("#manufacturertype").val(),
                    apptype:$("#apptype").val(),
                    fromdate:$("#fromdate").val(),
                    todate:$("#todate").val(),
                },
                beforeSend: function () {
                    $('#custom-loader').show();
                },
                complete: function () {
                    $('#custom-loader').hide();
                },
                error: function () {
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
            columnDefs: [
                { orderable: false, targets: [0, 7] },
                { className: 'text-start', targets: 0 }
            ],
            columns: [
                { data: "checkbox" },
                { data: "srno" },
                { data: "trustmark_id" },
                { data: "business_name",
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
                { data: "tin" },
                { data: "business_type" },
                { data: "representative" },
                { data: "date_submitted" },
                { data: "no_of_days" },
                { data: "remarks" },
                { data: "status" },
                {
                    data: "action",
                    createdCell: function (td, cellData, rowData, row, col) {
                        $(td).css({
                            "padding-left": "20px",
                            "vertical-align": "middle"
                        });
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
     }else{
        const table = $('#dataTable').DataTable({
            destroy: true,
            processing: true,
            serverSide: true,
            pagingType: "full_numbers",
            ajax: {
                url: DIR +'application/getList',
                type: 'GET',
                data: {
                    q: $("#details").val(),
                    manufacturertype:$("#manufacturertype").val(),
                    apptype:$("#apptype").val(),
                    fromdate:$("#fromdate").val(),
                    todate:$("#todate").val(),
                },
                beforeSend: function () {
                    $('#custom-loader').show();
                },
                complete: function () {
                    $('#custom-loader').hide();
                },
                error: function () {
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
            columnDefs: [
                { orderable: false, targets: [0, 6] },
                { className: 'text-start', targets: 0 }
            ],
            columns: [
                { data: "srno" },
                { data: "trustmark_id" },
                { data: "business_name",
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
                { data: "tin" },
                { data: "business_type" },
                { data: "representative" },
                { data: "date_submitted" },
                { data: "no_of_days" },
                { data: "remarks" },
                { data: "status" },
                {
                    data: "action",
                    createdCell: function (td, cellData, rowData, row, col) {
                        $(td).css({
                            "padding-left": "20px",
                            "vertical-align": "middle"
                        });
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
     }
   };
    $('#dataTable').on('click', '.toggle-text', function(e){
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
    $(document).on('click', '.btn-secondary', function() {
        $('#summaryModal').modal('hide');
    });
    $(document).on('click', '.close', function() {
        $('#summaryModal').modal('hide');
    });
    $(document).on('click', '#summaryBtn', function () {
    $.ajax({
        url: "{{ route('monthly.pending.summary') }}",
        type: "GET",
        dataType: "json",
        success: function (data) {
            let tbody = $('#summaryTable tbody');
            tbody.empty();

            if (data.length === 0) {
                tbody.append('<tr><td colspan="3" class="text-center">No pending records found</td></tr>');
            } else {
                $.each(data, function (index, item) {
                    tbody.append(`
                        <tr>
                            <td style="font-size: 12px;">${index + 1}</td>
                            <td style="font-size: 12px;">${item.Month}</td>
                            <td style="font-size: 12px;">${item.Pending}</td>
                        </tr>
                    `);
                });
            }

            $('#summaryModal').modal('show');
        },
        error: function (xhr, status, error) {
            console.error("AJAX Error:", error);
        }
    });
});

document.getElementById('filter_box').addEventListener('click', function (e) {
    e.preventDefault();
    const card = document.getElementById('filterCard');
    const isHidden = window.getComputedStyle(card).display === 'none';
    card.style.display = isHidden ? 'block' : 'none';
});
</script>
@endsection
