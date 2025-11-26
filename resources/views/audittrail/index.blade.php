@extends('layouts.app')
@include('layouts.layout')
@section('content')
<div class="d-sm-flex align-items-center justify-content-between mb-4">
    <h1 class="h3 mb-0 text-gray-800">{{ __('Audit Trail (System Logs)') }}</h1>

   

</div>
<div class="row d-flex align-items-center justify-content-end" style="padding-bottom: 20px;">
            <div class="col-lg-2 col-md-2 col-sm-2 pdr-20" >
                <div class="btn-box">
                    @php
                        $fromDate = date('Y-m-d', strtotime(date('Y-m-d').' -1 months'));
                    @endphp
                    <label for="Search" class="form-label">{{ __('From Date') }}</label>
                    <input type="date" name="fromdate" id="fromdate" class="form-control" value="{{$fromDate}}" style="font-size:12px; padding:9px;">
                    
                </div>
            </div>
            <div class="col-lg-2 col-md-2 col-sm-2 pdr-20" >
                <div class="btn-box">
                   <label for="todate" class="form-label">{{ __('To Date') }}</label>
                    <input type="date" name="todate" id="todate" class="form-control" value="{{date('Y-m-d')}}" style="font-size:12px; padding:9px;">
               
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
                                    <th>No</th>
                                    <th>Date | Time</th>
                                    <th>User Name</th>
                                    <th>Action</th>
                                    <th>Audit Description</th>
                                    <th>Status</th>
                                    <th>Remarks</th>
                                    <th>Location</th>
                                    </tr>
                                </thead>
                                <tbody>
                                
                                </tbody>
            </table>
        </div>
    </div>
</div>


@endsection
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
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
        dom: "<'row'<'col-sm-12'f>>" +"<'row'<'col-sm-3'l><'col-sm-9'p>>" +"<'row'<'col-sm-12'tr>>" +"<'row'<'col-sm-12'p>>",
        ajax: {
            url: "{{ route('audittrail.getList') }}",
            data: function(d) {
                d.fromdate = $('#fromdate').val();
                d.todate   = $('#todate').val(); // note: remove duplicate lines in your original
                d.q        = $('#q').val();
            }
        },
        pageLength: 10,
        columns: [
            { data: 'no', orderable: false },
            { data: 'date_time' },
            {
                data: 'user_name',
                render: function(data) {
                    if (!data) return '';
                    if (data.length > 25) {
                        const shortText = data.substr(0,25) + '...';
                        return `<div class="text-container" style="white-space: normal; word-break: break-word;">
                                    <span class="short-text">${shortText}</span>
                                    <span class="full-text" style="display:none;">${data}</span>
                                    <a href="#" class="toggle-text" style="color:blue;">Read more</a>
                                </div>`;
                    }
                    return `<div style="white-space: normal; word-break: break-word;">${data}</div>`;
                }
            },
            { data: 'action_name' },
            {
                data: 'audit_description',
                render: function(data) {
                    if (!data) return '';
                    if (data.length > 25) {
                        const shortText = data.substr(0,25) + '...';
                        return `<div class="text-container" style="white-space: normal; word-break: break-word;">
                                    <span class="short-text">${shortText}</span>
                                    <span class="full-text" style="display:none;">${data}</span>
                                    <a href="#" class="toggle-text" style="color:blue;">Read more</a>
                                </div>`;
                    }
                    return `<div style="white-space: normal; word-break: break-word;">${data}</div>`;
                }
            },
            { data: 'status' },
            {
                data: 'remarks',
                render: function(data) {
                    if (!data) return '';
                    if (data.length > 25) {
                        const shortText = data.substr(0,25) + '...';
                        return `<div class="text-container" style="white-space: normal; word-break: break-word;">
                                    <span class="short-text">${shortText}</span>
                                    <span class="full-text" style="display:none;">${data}</span>
                                    <a href="#" class="toggle-text" style="color:blue;">Read more</a>
                                </div>`;
                    }
                    return `<div style="white-space: normal; word-break: break-word;">${data}</div>`;
                }
            },
            { data: 'location' }
        ],
        drawCallback: function() {
            // reliable delegated handlers bound to tbody
            $('#dataTable1 tbody').off('click', '.viewlocation').on('click', '.viewlocation', function() {
                const latitude  = $(this).attr('latitude');
                const longitude = $(this).attr('longitude');
                openGoogleMap(latitude, longitude);
            });

            $('#dataTable1 tbody').off('click', '.toggle-text').on('click', '.toggle-text', function(e) {
                e.preventDefault();
                const $container = $(this).closest('.text-container');
                const $short = $container.find('.short-text');
                const $full  = $container.find('.full-text');

                if ($full.is(':visible')) {
                    $full.hide(); $short.show(); $(this).text('Read more');
                } else {
                    $short.hide(); $full.show(); $(this).text('Read less');
                }
            });
        }
    });
    function openGoogleMap(latitude, longitude){
                const url = `https://www.google.com/maps?q=`+latitude+`,`+longitude;
                window.open(url, '_blank');
            }
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