@extends('layouts.app')
@include('layouts.layout')
@section('content')
    <div class="d-sm-flex align-items-center justify-content-between mb-4">
        <h1 class="h3 mb-0 text-gray-800">{{ __('Manage Payment Concerns') }}</h1>
    </div>

    <div class="row d-flex" style="padding-bottom: 20px;">
         <div class="col-md-2 col-auto float-end ms-2" style="padding-top: 16px;"><br>
            <a href="#" class="btn btn-sm btn-success quickRun" style="padding:9px;">
                <span class="btn-inner--icon" style="color: white;">Sync</span>
            </a>
        </div>

        <div class="col-md-3">
            <div class="form-group" id="parrent_reg_no">
                <label for="department_id" class="form-label">{{ __('From Date') }}</label>
                <input type="date" name="fromdate" id="fromdate" class="form-control custom-input" value="{{ date('Y-m-d') }}"  style="font-size:12px;">
            </div>
        </div>
        <div class="col-md-3">
            <div class="form-group" id="parrent_reg_no">
                <label for="department_id" class="form-label">{{ __('To Date') }}</label>
                <input type="date" name="todate" id="todate" class="form-control custom-input" value="{{ date('Y-m-d') }}"   style="font-size:12px; ">
            </div>
        </div>
        <div class="col-xl-3 col-lg-3 col-md-6 col-sm-12 col-12">
            <div class="btn-box">
                <label for="Search" class="form-label">{{ __('Search') }}</label>
                <input type="text" name="q" id="q" class="form-control" required value=""
                    style="font-size:12px; padding:9px;">
            </div>
        </div>
        <div class="col-auto float-end ms-2" style="padding-top: 16px;"><br>
            <a href="#" class="btn btn-sm btn-primary" id="btn_search" style="padding:9px;">
                <span class="btn-inner--icon"><i class="fas fa-search"></i></span>
            </a>
            <a href="#" class="btn btn-sm btn-danger" id="btn_clear" style="padding:9px;">
                <span class="btn-inner--icon"><i class="fas fa-trash "></i></span>
            </a>
        </div>
    </div>
    <div id="wrapper">
        <div class="d-flex flex-column" id="content-wrapper">

            <div class="card shadow">
                <div class="card-body">
                    <div class="table-responsive table mt-2" id="dataTable-1" role="grid"
                        aria-describedby="dataTable_info">
                        <table id="dataTable" class="table table-bordered" style="width: 98.5% !important;">
                            <thead>
                                <tr>
                                    <tr>
                                    <th>No</th>
                                    <th>Security No.</th>
                                    <th>Business Name</th>
                                    <th>Merchant Reference No.</th>
                                    <th>Amount</th>
                                    <th>Date</th>
                                    <th>Payment Status</th>
                                </tr>
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

<!-- Sync Modal -->
<div class="modal fade" id="syncModal" tabindex="-1" aria-labelledby="syncModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">

            <div class="modal-header">
                <h5 class="modal-title" id="syncModalLabel">{{ __('Manage Payment Concerns') }}</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>

            <div class="modal-body">
                <div class="row">
                    <div class="col-md-12">
                        <div class="form-group">
                            <label for="transaction_id" class="form-label">
                                {{ __('Transaction Reference Number') }}
                                <span class="text-danger">*</span>
                            </label>
                            <div class="form-icon-user">
                                <input type="text" 
                                       name="transaction_id" 
                                       id="transaction_id" 
                                       class="form-control"
                                       required 
                                       style="font-size:12px; padding:9px;">
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="modal-footer">
                <button type="button" class="btn btn-light" data-bs-dismiss="modal">Close</button>
                <button type="button" id="syncStatus" class="btn btn-primary">Sync</button>
            </div>

        </div>
    </div>
</div>



<input type="hidden" id="role_id" value="{{ Auth::user()->role }}">
<script>
    var cid='';
    $(document).ready(function(){
         datatablefunction();
          $("#btn_search").click(function(){
            datatablefunction();
        }); 

        $(document).on('click', '.quickRun', function () {
            $('#syncModal').modal('show');
        });

        $(document).on('click', '#syncStatus', function () {
            var transId = $("#transaction_id").val();
            if(transId==''){
                Swal.fire({
                    position: 'center',
                    icon: 'warning',
                    title: 'Oops!',
                    text: "Please enter transaction reference number.",
                    showConfirmButton: true,
                    confirmButtonText: 'OK',
                    allowOutsideClick: false,
                    allowEscapeKey: false
                });
            }else{
                updatePaymentStatus();
            }
            
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
                    url: DIR + 'payment/update-status/getList',
                    type: 'POST',
                    data: function (d) {
                        d.q = $("#q").val();
                        d.fromdate = $("#fromdate").val();
                        d.todate = $("#todate").val();
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
                searching: false, // Ensure this disables the search box
                columnDefs: [
                    { orderable: false, targets: [0] },
                    { className: 'text-start', targets: 0 }
                ],
                columns: [
                    { data: "srno" },
                    { data: "trustmark_id" },
                    { data: "business_name" },
                    { data: "transaction_id" },
                    { data: "final_total_amount" },
                    { data: "date" },
                    { data: "payment_status" }
                ]
            });
        }
    };

    function updatePaymentStatus() {
        const swalWithBootstrapButtons = Swal.mixin({
            customClass: {
                confirmButton: 'btn btn-success',
                cancelButton: 'btn btn-danger'
            },
            buttonsStyling: false
        })
        swalWithBootstrapButtons.fire({
            title: 'Are you sure?',
            text: "Are you sure want to sync?",
            icon: 'warning',
            showCancelButton: true,
            confirmButtonText: 'Yes',
            cancelButtonText: 'No',
            reverseButtons: true
        }).then((result) => {
            if (result.isConfirmed) {

                var DIR =$("#DIR").val();
                // showLoader();
                $.ajax({
                    url: DIR  + "payment/sync-status",
                    dataType: 'json',
                    type: 'POST',  
                    data: {
                        transId: $("#transaction_id").val(),
                        _token: $("#_csrf_token").val(),
                    },
                    success: function (data) {
                        if(data.status){
                            $("#transaction_id").val('');
                            // hideLoader();
                            $('#syncModal').modal('hide');
                            Swal.fire({
                                position: 'center',
                                icon: 'success',
                                title: data.message,
                                showConfirmButton: false,
                                timer: 1500
                            });
                            datatablefunction();
                        }else{
                            Swal.fire({
                                position: 'center',
                                icon: 'warning',
                                title: 'Oops!',
                                text: data.message,
                                showConfirmButton: true,
                                confirmButtonText: 'OK',
                                allowOutsideClick: false,
                                allowEscapeKey: false
                            });
                        }
                    }
                });
            }
        })
    }
</script>

@endsection
