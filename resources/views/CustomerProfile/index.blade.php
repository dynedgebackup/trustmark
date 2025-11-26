@extends('layouts.app')
@include('layouts.layout')
@section('content')
<div class="d-sm-flex align-items-center justify-content-between mb-4">
    <h1 class="h3 mb-0 text-gray-800">{{ __('Customers Profile') }}</h1>

   <!-- <a href="#" 
   data-size="lg" 
   data-url="{{ route('CustomerProfile.store') }}" 
   data-ajax-popup="true" 
   data-bs-toggle="tooltip" 
   title="{{ __('Manage Customers Profile') }}" 
   data-title="Manage Customers Profile"
   class="btn btn-sm btn-primary">
    <i class="fas fa-plus"></i>
</a> -->

</div>
<div class="row d-flex align-items-center justify-content-end" style="padding-bottom: 20px;">
            <div class="col-lg-2 col-md-2 col-sm-2 pdr-20" >
                <div class="btn-box">
                    @php
                        $fromDate = date('Y-m-d', strtotime(date('Y-m-d').' -1 months'));
                    @endphp
                    <label for="Search" class="form-label">{{ __('Registered From') }}</label>
                    <input type="date" name="fromdate" id="fromdate" class="form-control" value="{{$fromDate}}" style="font-size:12px; padding:9px;">
                    
                </div>
            </div>
            <div class="col-lg-2 col-md-2 col-sm-2 pdr-20" >
                <div class="btn-box">
                   <label for="todate" class="form-label">{{ __('Registered To') }}</label>
                    <input type="date" name="todate" id="todate" class="form-control" value="{{date('Y-m-d')}}" style="font-size:12px; padding:9px;">
               
                </div>
            </div>
            <div class="col-md-3">
                <div class="form-group" id="parrent_fee_id">
                    <label for="status" class="form-label">{{ __('Account Status') }}</label>
                    <span class="validate-err">{{ $errors->first('status') }}</span>
                    <div class="form-icon-user">
                    <select name="status" id="status" class="form-control select3" required style="width:100%;font-size: 12px;padding: 9px;">
                        <option value="">Select Status</option>
                        <option value="Verified">Verified</option>
                        <option value="Unverified">Unverified</option>
                    </select>
                    </div>
                    <span class="validate-err" id="err_fee_id"></span>
                </div>
            </div>
            <div class="col-xl-3 col-lg-3 col-md-6 col-sm-12 col-12">
                <div class="btn-box">
                <label for="Search" class="form-label">{{ __('Search') }}</label>
                <input type="text" name="q" id="q" class="form-control" value="" style="font-size:12px; padding:9px;">
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
                        <table id="dataTable1" class="table table-bordered" style="width: 98.5% !important;">
                            <thead>
                                <tr>
                                        <th class="custom-th" style="width: 50px;">{{ __('No.') }}</th>
                                        <th class="custom-th" style="width: 250px;">{{ __('Full Name') }}</th>
                                        <th class="custom-th" style="width: 250px;">{{ __('Email') }}</th>
                                        <th class="custom-th" style="width: 150px;">{{ __('Registered') }}</th>
                                        <th class="custom-th" style="width: 150px;">{{ __('Verified') }}</th>
                                        <th class="custom-th" style="width: 100px;">{{ __('Approved') }}</th>
                                        <th class="custom-th" style="width: 250px;">{{ __('Under-Evaluation') }}</th>
                                        <th class="custom-th" style="width: 100px;">{{ __('Draft') }}</th>
                                        <th class="custom-th" style="width: 100px;">{{ __('Action') }}</th>
                                    </tr>
                                </thead>
                                <tbody>
                                
                                </tbody>
            </table>
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
                        <h5 class="modal-title">Customer Application Reference</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                    </div>
                    <div class="modal-body">
                        <table id="checkRecordsTable" class="table table-bordered table-striped"
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
                                    <th style="width:8%;">Returned</th>
                                    <th style="width:8%;">Approved</th>
                                    <th style="width:8%;">Paid</th>
                                    <th style="width:8%;">Amount</th>
                                    <th style="width:8%;">Channel</th>
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

@endsection
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script>
            let Table;

            $(document).ready(function () {
                $(document).on('click', '.btn-edit-business', function(e) {
                    e.preventDefault();

                    var businessId = $(this).data('id'); // <-- fixed
                    if(!businessId) {
                        console.error("Business ID missing from button");
                        return;
                    }

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
                                ajax: '{{ route('CustomerProfile.check-business-Registration-records', ':id') }}'
                                        .replace(':id', businessId),
                                columnDefs: getColumnDefs(),
                                columns: getColumns()
                            });
                        } else {
                            Table.ajax.url('{{ route('CustomerProfile.check-business-Registration-records', ':id') }}'
                                            .replace(':id', businessId)).load();
                        }
                    });
                });

                $('#checkRecordsTable')
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
            });
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
                    { width: '8%', targets: 9 },
                    { width: '8%', targets: 10 },
                    { width: '8%', targets: 11 },
                    { width: '8%', targets: 12 },
                    { width: '8%', targets: 13 },
                    { width: '5%', targets: 14 },
                    { width: '3%', targets: 15 }
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
                    {
                        data: 'BusinessName',
                        render: renderWithReadMore
                    },
                    {
                        data: 'RegistrationNo',
                        render: renderWithReadMore
                    },
                    {
                        data: 'Evaluator',
                        render: renderWithReadMore
                    },
                    
                    { data: 'BusinessType' },
                    { data: 'TIN' },
                    { data: 'Representative' },
                    { data: 'Submitted' },
                    { data: 'Returned' },
                    { data: 'Approved' },
                    { data: 'Paid' },
                    { data: 'Amount' },
                    { data: 'Channel' },
                    {
                        data: 'Remarks',
                        render: renderWithReadMore
                    },
                    {
                        data: 'Status',
                        render: function (data) {
                            let badgeClass = '';
                            switch (data) {
                                case 'APPROVED': badgeClass = 'badge-bg-approve'; break;
                                case 'UNDER EVALUATION': badgeClass = 'badge-bg-evaluation'; break;
                                case 'RETURNED': badgeClass = 'badge-bg-returned'; break;
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
        </script>
<script>
 


    $(document).on("click", ".delete-btn", function () {
    let id = $(this).data("id");
    let name = $(this).data("name");

    Swal.fire({
        title: "Delete UN-VERIFIED account?",
        text: "Customer Name: " + name + " ?",
        icon: "warning",
        showCancelButton: true,
        confirmButtonColor: "#d33",
        cancelButtonColor: "#3085d6",
        confirmButtonText: "Yes, delete it!",
        cancelButtonText: "Cancel"
    }).then((result) => {
        if (result.isConfirmed) {
            $.ajax({
                url: "{{ route('CustomerProfile.destroy', ':id') }}".replace(':id', id),
                type: "DELETE",
                data: {
                    _token: "{{ csrf_token() }}"
                },
                success: function () {
                    Swal.fire(
                        "Deleted!",
                        "Account has been deleted.",
                        "success"
                    );
                    $('#dataTable1').DataTable().ajax.reload();
                },
                error: function () {
                    Swal.fire(
                        "Error!",
                        "Something went wrong, please try again.",
                        "error"
                    );
                }
            });
        }
    });
});

$(document).ready(function() {
    select3Ajax("app_code_filter","parrent_appTypeapp_code_filter","AppcodeAjaxList");
    datatablefunction();
    $("#btn_search").click(function(){
 		datatablefunction();
 	});	
});
function datatablefunction() {
    if ($.fn.DataTable.isDataTable('#dataTable1')) {
        $('#dataTable1').DataTable().destroy();
    }
    $('#dataTable1').DataTable({
        processing: true,
        serverSide: true,
        searching: false,
        autoWidth: false,
        columnDefs: [
            { targets: 0, width: '50px' },
            { targets: 1, width: '250px' },
            { targets: 2, width: '250px' },
            { targets: 3, width: '150px' },
            { targets: 4, width: '150px' },
            { targets: 5, width: '100px' },
            { targets: 6, width: '250px' },
            { targets: 7, width: '100px' },
            { targets: 8, width: '100px' }
        ],
        dom: "<'row'<'col-sm-12'f>>" +"<'row'<'col-sm-3'l><'col-sm-9'p>>" +"<'row'<'col-sm-12'tr>>" +"<'row'<'col-sm-12'p>>",
        ajax: {
            url: "{{ route('CustomerProfile.getList') }}",
            data: function(d) {
                d.fromdate = $('#fromdate').val();
                d.todate = $('#todate').val();
                d.todate = $('#todate').val();
                d.status = $('#status').val();
                d.q = $('#q').val();
            }
        },
        pageLength: 10,
        columns: [
            { data: 'no', orderable: false },
            { data: 'name' },
            { data: 'email' },
            { data: 'created_at' },
            { data: 'email_verified_at' },
            { data: 'approved', orderable: false },
            { data: 'under_evaluation', orderable: false },
            { data: 'draft', orderable: false },
            { data: 'action', orderable: false, searchable: false }
        ]
    });
}

</script>

<script type="text/javascript">
    $(document).ready(function () {
        $(document).on('click', '.activeinactive', function(e) {
            e.preventDefault();
            var id = $(this).data('id');
            var status = $(this).data('status');
            ActiveInactiveUpdate(id, status);
        });
        function ActiveInactiveUpdate(id,is_activeinactive){
    const swalWithBootstrapButtons = Swal.mixin({
        customClass: {
            confirmButton: 'btn btn-success',
            cancelButton: 'btn btn-danger'
        },
        buttonsStyling: false
    })
    swalWithBootstrapButtons.fire({
        title: 'Are you sure?',
        text: "You wont to Active/Inactive?",
        icon: 'warning',
        showCancelButton: true,
        confirmButtonText: 'Yes',
        cancelButtonText: 'No',
        reverseButtons: true
    }).then((result) => {
        if(result.isConfirmed)
        {
            $.ajax({
                url: '{{ route("scheduleFees.ActiveInactive") }}',
                type: "POST", 
                data: {
                    "id": id,
                    "is_activeinactive": is_activeinactive,  
                    _token: '{{ csrf_token() }}'
                },
                success: function(html){
                    Swal.fire({
                        position: 'center',
                        icon: 'success',
                        title: 'Update Successfully.',
                        showConfirmButton: false,
                        timer: 1500
                    })
                    window.location.reload();
                }
            })
        }
    })
    }

    function loadDivisionDetails(officeId) {
        if (!officeId) return;

        $.ajax({
            url: '{{ route("scheduleFees.division.list") }}',
            method: 'POST',
            data: {
                id: officeId,
                _token: '{{ csrf_token() }}'
            },
            success: function (response) {
                $('#divisionDetails').html(response.html);
                $('.btn_cancel_division').off('click').on('click', function () {
                    $(this).closest('.removedivisiondata').remove();
                });
            },
            error: function () {
                console.error('Failed to load division details.');
            }
        });
    }
    $(document).on('click', '.btn-edit-office', function (e) {
    e.preventDefault();

    let officeId = $(this).data('id'); 
    let editUrl = $(this).data('url');
    $.ajax({
        url: editUrl,
        type: 'GET',
        success: function (res) {
            $('#ajaxModal .modal-body').html(res);
            $('#ajaxModal').modal('show');
            setTimeout(function () {
                loadDivisionDetails(officeId);
            }, 300);
        }
    });
});

       
});


</script>