@extends('layouts.app')
@include('layouts.layout')
@section('content')
<style>
    .table-label {
        text-align: center; /* default for others */
    }
    .table-label.name {
        text-align: left;
        padding-left: 10px; /* optional */
    }
</style>
    <div class="d-sm-flex align-items-center justify-content-between mb-4">
        <h1 class="h3 mb-0 text-gray-800">{{ __('Manage Refund Payment') }}</h1>
    </div>

    <div class="row d-flex" style="padding-bottom: 20px;">
         <div class="col-md-2 col-auto float-end ms-2" style="padding-top: 16px;"><br>
           <!-- <a href="#" class="btn btn-sm btn-success quickRun" style="padding:9px;">
                <span class="btn-inner--icon" style="color: white;">Sync</span>
            </a>  -->
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
                                    <th>Transaction Reference No.</th>
                                    <th>Amount</th>
                                    <th>Date</th>
                                    <th>Payment Status</th>
                                    <th>Action</th>
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

<input type="hidden" id="role_id" value="{{ Auth::user()->role }}">



<!-- Sync Modal -->
<div class="modal fade" id="syncModal" tabindex="-1" aria-labelledby="syncModalLabel" aria-hidden="true">
  <div class="modal-dialog modal-dialog-centered modal-lg">
    <div class="modal-content">

      <!-- Modal Header -->
      <div class="modal-header">
        <h5 class="modal-title" id="refundModalLabel">{{ __('Manage Payment Refund') }}</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>

      <!-- Modal Body -->
      <div class="modal-body">
        <form>
            <div class="row d-flex" >
                <!-- Transaction Reference -->
               <div class="col-md-6">
                    <label for="transaction_id" class="form-label">
                      {{ __('Transaction Reference Number') }} <span class="text-danger">*</span>
                    </label>
                    <input type="text"
                       id="transaction_id"
                       class="form-control"
                       placeholder="Enter transaction reference"
                       readonly
                       style="font-size:12px; padding:9px;">
                </div>
               
                <div class="col-md-6">
                    <label for="transaction_id" class="form-label">
                      {{ __('Security No.') }} <span class="text-danger">*</span>
                    </label>
                    <input type="text"
                       id="security_no"
                       class="form-control"
                       placeholder="Enter Security No."
                       readonly
                       style="font-size:12px; padding:9px;">
                </div>
            </div>
            <div class="row d-flex" >
               <div class="col-md-12">
                    <h5 class="mt-4">Fee Details</h5>
                    <table class="table">
                        <thead>
                            <tr>
                                <th class="table-heading">Description</th>
                                <th class="table-heading">Amount</th>
                                <th class="table-heading">Refund</th>
                                <th class="table-heading">Remaining</th>
                            </tr>
                        </thead>
                        <tbody class="text-end" id="FeeDetails"></tbody>
                    </table>
                </div>
            </div>
            <div class="row d-flex" >
                <!-- Refund Type Pill Radios -->
                <div class="col-md-6">
                <!-- <label class="form-label">{{ __('Refund Type') }} <span class="text-danger">*</span></label> -->
                <div class="flex gap-2">
                  <label class="cursor-pointer px-4 py-2 rounded-full transition">
                    <input type="radio" name="refundType" value="full" class="hidden" id="fullRefund" disabled>
                    Full Refund
                  </label>
                  <label class="cursor-pointer px-4 py-2 rounded-full transition">
                    <input type="radio" name="refundType" value="partial"  class="hidden" id="partialRefund" disabled>
                    Partial Refund
                  </label>
                </div>
                </div>

                <!-- Partial Refund Amount -->
                <div class="col-md-12">
                <label for="refund_amount" class="form-label">
                    {{ __('Refund Amount') }}<span class="required-field">*</span>
                </label>
                <input type="number"
                       id="refund_amount"
                       class="form-control"
                       placeholder="Enter refund amount"
                       min="0"
                       step="0.01"
                >
                </div>

                <div class="col-md-12">
                <label for="reason" class="form-label">
                    {{ __('Reason') }}<span class="required-field">*</span>
                </label>
                <input type="text"
                       id="reason"
                       class="form-control"
                       placeholder="Enter reason"
                >
                </div>

                <div class="col-md-12">
                <label for="password" class="form-label">
                    {{ __('Password') }}<span class="required-field">*</span>
                </label>
                <input type="password"
                       id="password"
                       class="form-control"
                       placeholder="Enter password">
                </div>
            </div>

        </form>
      </div>

      <!-- Modal Footer -->
      <div class="modal-footer">
        <button type="button" class="btn btn-light" data-bs-dismiss="modal">Close</button>
        <button type="button" id="refundAmount" class="btn btn-primary">Refund</button>
      </div>
    </div>
  </div>
</div>

<script>
    var pid='';
    var amt='';
     
    $(document).ready(function(){
        const $fullRefundRadio = $("#fullRefund");
        const $partialRefundRadio = $("#partialRefund");
        const $refundAmountInput = $("#refund_amount");
        // Set initial state: Full Refund selected by default
        $refundAmountInput.prop("readonly", true)
                          .addClass("bg-gray-100 cursor-not-allowed");

        // Function to toggle readonly state
        function toggleRefundAmount() {
          if ($partialRefundRadio.is(":checked")) {
            $refundAmountInput.prop("readonly", false)
                              .removeClass("bg-gray-100 cursor-not-allowed")
                              .addClass("bg-white");
          } else {
            $refundAmountInput.prop("readonly", true)
                              .removeClass("bg-white")
                              .addClass("bg-gray-100 cursor-not-allowed")
                              .val($("#amt_"+pid).val()); // optionally clear the input
          }
        }

        // Attach event listeners
        $fullRefundRadio.on("change", toggleRefundAmount);
        $partialRefundRadio.on("change", toggleRefundAmount);


         datatablefunction();
          $("#btn_search").click(function(){
            datatablefunction();
        }); 

        $(document).on('click', '.quickRun', function () {
            var DIR =$("#DIR").val();
            pid = $(this).attr('pid');
            $.ajax({
                url: DIR  + "payment/getFeeDetails",
                dataType: 'html',
                type: 'POST',  
                data: {
                    _token: $("#_csrf_token").val(),
                    pid :pid
                    
                },
                success: function (html) {
                    $("#FeeDetails").html(html);
                    var transId = $("#transId_"+pid).val();
                    $("#transaction_id").val(transId);
                    var security_no = $("#security_no"+pid).val();
                    $("#security_no").val(security_no) 
                    amt = $("#amt_"+pid).val();
                    $('#syncModal').modal('show');
                    $("#refund_amount").val('');
                    $("#password").val('');
                    
                    commonfuncation();
                }
            });
            
        });

        $(document).on('click', '#refundAmount', function () {
            var transId = $("#transaction_id").val();
            var reason = $("#reason").val();
            var partialRefund = $("#partialRefund").is(":checked");

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
            }else if($("#refund_amount").val()==''){
                Swal.fire({
                    position: 'center',
                    icon: 'warning',
                    title: 'Oops!',
                    text: "Please enter Refund Amount.",
                    showConfirmButton: true,
                    confirmButtonText: 'OK',
                    allowOutsideClick: false,
                    allowEscapeKey: false
                });
            }else if($("#password").val()==''){
                Swal.fire({
                    position: 'center',
                    icon: 'warning',
                    title: 'Oops!',
                    text: "Please enter password.",
                    showConfirmButton: true,
                    confirmButtonText: 'OK',
                    allowOutsideClick: false,
                    allowEscapeKey: false
                });
            }else if(reason==''){
                Swal.fire({
                    position: 'center',
                    icon: 'warning',
                    title: 'Oops!',
                    text: "Please enter reason.",
                    showConfirmButton: true,
                    confirmButtonText: 'OK',
                    allowOutsideClick: false,
                    allowEscapeKey: false
                });
            }else{
                updatePayment();
            }
            
        });
    })

    function commonfuncation(){
         // When refund input changes
        $(document).on('input', '.refund-input', function() {
            let totalAmount = 0;
            let totalRefund = 0;
            let totalRemaining = 0;

            // Loop through each refund field
            $('.refund-input').each(function() {
                const id = $(this).attr('id').split('_')[1];
                let refund = parseFloat($(this).val()) || 0;
                const amount = parseFloat($('#amount_' + id).data('original')) || 0;

                 // Prevent refund greater than original amount
                if (refund > amount) {
                    refund = amount;
                    $(this).val(refund.toFixed(2));
                }

                // Calculate remaining (prevent negatives)
                const remaining = Math.max(amount - refund, 0);

                // Update remaining cell
                $('#remaining_' + id).text(remaining.toFixed(2));

                // Update totals
                totalAmount += amount;
                totalRefund += refund;
                totalRemaining += remaining;
            });

            // Update total row
            $('#final_refund').text(totalRefund.toFixed(2));
            $('#final_remaining').text(totalRemaining.toFixed(2));
            $('#refund_amount').val(totalRefund.toFixed(2));
            

            if (totalRefund === 0) {
                // No refund
                $('#fullRefund').prop('checked', false);
                $('#partialRefund').prop('checked', false);
            } else if (Math.abs(totalRefund - totalAmount) < 0.01) {
                // Full refund (tolerate small rounding)
                $('#fullRefund').prop('checked', true);
                $('#partialRefund').prop('checked', false);
            } else if (totalRefund > 0 && totalRefund < totalAmount) {
                // Partial refund
                $('#fullRefund').prop('checked', false);
                $('#partialRefund').prop('checked', true);
            }

        });
    }
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
                    url: DIR + 'payment/refund/getList',
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
                    { orderable: false, targets: [0,8] },
                    { className: 'text-start', targets: 0 }
                ],
                columns: [
                    { data: "srno" },
                    { data: "trustmark_id" },
                    { data: "business_name" },
                    { data: "transaction_id" },
                    { data: "transId" },
                    { data: "final_total_amount" },
                    { data: "date" },
                    { data: "payment_status" },
                    { data: "action" }
                ]
            });
        }
    };

    function updatePayment() {
        let feeData = [];

        $('.refund-input').each(function() {
            const id = $(this).attr('id').split('_')[1]; // extract fee id
            const refund = parseFloat($(this).val()) || 0;
            const amount = parseFloat($('#amount_' + id).data('original')) || 0;
            const remaining = amount - refund;

            feeData.push({
                fee_id: id,
                refund_amount: refund,
                remaining_amount: remaining
            });
        });

        const swalWithBootstrapButtons = Swal.mixin({
            customClass: {
                confirmButton: 'btn btn-success',
                cancelButton: 'btn btn-danger'
            },
            buttonsStyling: false
        })
        swalWithBootstrapButtons.fire({
            title: 'Are you sure?',
            text: "Are you sure want to refund?",
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
                    url: DIR  + "payment/refundAmount",
                    dataType: 'json',
                    type: 'POST',  
                    data: {
                        transId: $("#transaction_id").val(),
                        partialRefund: $("#partialRefund").is(":checked"),
                        fullRefund: $("#fullRefund").is(":checked"),
                        _token: $("#_csrf_token").val(),
                        refund_amount: $("#refund_amount").val(),
                        reason: $("#reason").val(),
                        user_password: $("#password").val(),
                        pid :pid,
                        feeData:feeData
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
