@extends('layouts.app')
@include('layouts.layout')
@section('content')
<div class="d-sm-flex align-items-center justify-content-between mb-4">
    <h1 class="h3 mb-0 text-gray-800">{{ __('Municipality | City') }}</h1>

   <a href="#" 
   data-size="lg" 
   data-url="{{ route('municipality.store') }}" 
   data-ajax-popup="true" 
   data-bs-toggle="tooltip" 
   title="{{ __('Manage Municipality | City') }}" 
   data-title="Manage Municipality | City"
   class="btn btn-sm btn-primary">
    <i class="fas fa-plus"></i>
</a>

</div>
<div class="row d-flex align-items-center justify-content-end" style="padding-bottom: 20px;">
            <div class="col-md-3">
                <div class="form-group" id="parrent_reg_no">
                    <label for="office_name" class="form-label">{{ __('Region') }}</label>
                    <span class="validate-err"></span>
                    <div class="form-icon-user">
                        <select name="reg_no_filter" id="reg_no_filter" class="form-control select3" required style="width:100%;">
                            <option value=""></option>
                            
                        </select>
                    </div>
                    <span class="validate-err" id="err_app_code"></span>
                </div>
            </div>
            <div class="col-md-2">
                <div class="form-group" id="parrent_prov_no_filter">
                    <label for="office_name" class="form-label">{{ __('Province') }}</label>
                    <span class="validate-err"></span>
                    <div class="form-icon-user">
                        <select name="prov_no_filter" id="prov_no_filter" class="form-control select3" required style="width:100%;">
                            <option value=""></option>
                            
                        </select>
                    </div>
                    <span class="validate-err" id="err_app_code"></span>
                </div>
            </div>
            <div class="col-md-2">
                <div class="form-group" id="parrent_fee_id">
                    <label for="status" class="form-label">{{ __('Status') }}</label>
                    <span class="validate-err">{{ $errors->first('status') }}</span>
                    <div class="form-icon-user">
                    <select name="status" id="status" class="form-control select3" required style="width:100%;font-size: 12px;padding: 9px;">
                        <option value="">Select Status</option>
                        <option value="1">Active</option>
                        <option value="0">Cancel</option>
                    </select>
                    </div>
                    <span class="validate-err" id="err_fee_id"></span>
                </div>
            </div>
            <div class="col-xl-3 col-lg-3 col-md-6 col-sm-12 col-12">
                <div class="btn-box">
                <label for="Search" class="form-label">{{ __('Search') }}</label>
                <input type="text" name="q" id="q" class="form-control" required value="" style="font-size:12px; padding:9px;">
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
                                        <th>{{ __('No.') }}</th>
                                        <th>{{ __('Region') }}</th>
                                        <th>{{ __('Province Name') }}</th>
                                        <th>{{ __('Municipality | City') }}</th>
                                        <th>{{ __('Status') }}</th>
                                        <th>{{ __('Action') }}</th>
                                    </tr>
                                </thead>
                                <tbody>
                                
                                </tbody>
            </table>
        </div>
    </div>
</div>

<script src="{{ asset('assets/js/select2.min.js') }}"></script>
    <script src="{{ asset('assets/js/common.js') }}"></script>
    <input type="hidden" id="DIR" value="{{ url('/') }}/">
    <meta name="csrf-token" content="{{ csrf_token() }}">
@endsection
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script>
$(document).ready(function() {
    select3Ajax("reg_no_filter","parrent_reg_no","regionAjaxList");
    select3Ajax("prov_no_filter","parrent_prov_no_filter","provincesAjaxList");
    datatablefunction();
    $("#btn_search").click(function(){
 		datatablefunction();
 	});	
     $('#reg_no_filter').on('change', function() {
 		var Region_id =$(this).val();
        RegionId(Region_id);
       
       $("#prov_no_filter").html('<option value="">Please Select</option>');
   });
});
function RegionId(Region_id){
   $("#prov_no_filter").select3({
    placeholder: 'Please Select',
    allowClear: true,
    dropdownParent: $("#parrent_prov_no_filter").parent(),
    ajax: {
        url: DIR+'provincesAjaxList',
        dataType: 'json',
        delay: 250,
        data: function(params) {
            return {
                "id": Region_id, 
                term: params.term || '',
                page: params.page || 1
            }
        },
        cache: true
    }
});
}
function datatablefunction() {
    if ($.fn.DataTable.isDataTable('#dataTable1')) {
        $('#dataTable1').DataTable().destroy();
    }
    $('#dataTable1').DataTable({
        processing: true,
        serverSide: true,
        searching: false,
        dom: "<'row'<'col-sm-12'f>>" +"<'row'<'col-sm-3'l><'col-sm-9'p>>" +"<'row'<'col-sm-12'tr>>" +"<'row'<'col-sm-12'p>>",
        ajax: {
            url: "{{ route('municipality.getList') }}",
            data: function(d) {
                d.reg_no = $('#reg_no_filter').val();
                d.prov_no = $('#prov_no_filter').val();
                d.status = $('#status').val();
                d.q = $('#q').val();
            }
        },
        pageLength: 10,
        columns: [
            { data: 'no', orderable: false },
            { data: 'reg_region' },
            { data: 'prov_desc' },
            { data: 'mun_desc' },
            { data: 'status' },
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