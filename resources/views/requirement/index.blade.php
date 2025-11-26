@extends('layouts.app')
@include('layouts.layout')
@section('content')
<div class="row >
    <div class="d-sm-flex align-items-center justify-content-between mb-4">
    <h1 class="h3 mb-0 text-gray-800">Requirements (Authorized
                        Representative)</h1>
        </div>
        <div class="d-flex justify-content-end mb-2">
            <a href="#" data-bs-toggle="modal" data-bs-target="#requirementModal" title="Add Requirement" class="btn btn-sm btn-primary">
                <i class="fa fa-plus" style="font-size: 20px; cursor: pointer;"></i>
            </a>

        </div>
    </div>
    <ol class="breadcrumb custom-breadcrumb" style="padding-left: 28px;margin-top: -32px;">
        <li class="breadcrumb-item"><a href="{{ route('dashboard') }}"><span>Dashboard</span></a></li>
        <li class="breadcrumb-item"><a href="{{ route('requirement.index') }}"><span>Requirements (Authorized
                    Representative)</span></a></li>
    </ol>
    
    <div class="row d-flex align-items-center justify-content-end" style="padding-bottom: 20px;padding-right: 20px;">
            
            <div class="col-md-3">
                <div class="form-group" id="parrent_fee_id">
                    <label for="status" class="form-label">{{ __('Status') }}</label>
                    <span class="validate-err">{{ $errors->first('status') }}</span>
                    <div class="form-icon-user">
                    <select name="status" id="status" class="form-control select3" required style="width:100%;font-size: 12px;padding: 9px;">
                        <option value="">Select Status</option>
                        <option value="Active">Active</option>
                        <option value="Inactive">Inactive</option>
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
                                                            <th>{{ __('Government Issued ID') }}</th>
                                                            <th>{{ __('With Expiration') }}</th>
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
                </div>
            </div>
            <!-- Edit Modal -->
            <div class="modal fade" id="editRequirementModal" tabindex="-1" aria-labelledby="editRequirementModalLabel" aria-hidden="true">
                <div class="modal-dialog modal-dialog-centered modal-xl">
                    <div class="modal-content">
                        <form id="editReqForm" method="POST">
                            @csrf
                            @method('PUT')
                            <div class="modal-header">
                                <div class="w-100">
                                    <h5 class="modal-title custom-label" id="editRequirementModalLabel"
                                        style="font-family: sans-serif;text-align: left; font-size: 18px; color: rgb(0,0,0); font-weight: bold;">
                                        Government Issued Documents
                                    </h5>
                                </div>
                                <button type="button" class="btn-close" data-bs-dismiss="modal"
                                    aria-label="Close"></button>
                            </div>

                            <div class="modal-body">
                                <div class="row">
                                    <div class="col-md-6">
                                        <div class="mb-3">
                                            <label for="edit-issued-id" class="form-label custom-label">Government Issued ID<span class="required-field">*</span></label>
                                            <input type="text" class="form-control custom-input" name="issued_id" id="edit-issued-id" required style="height: 34px;">
                                        </div>
                                    </div>

                                    <div class="col-md-3">
                                        <div class="mb-3">
                                            <label for="edit-with_expiration" class="form-label custom-label">With Expiration<span class="required-field">*</span></label>
                                            <select class="form-select custom-input" name="with_expiration" id="edit-with_expiration" required>
                                                <option value="1">Yes</option>
                                                <option value="0">No</option>
                                            </select>
                                        </div>
                                    </div>
                                    <div class="col-md-3">
                                        <div class="mb-3">
                                            <label for="edit-status" class="form-label custom-label">Status<span class="required-field">*</span></label>
                                            <select class="form-select custom-input" name="status" id="edit-status" required>
                                                <option value="Active">Active</option>
                                                <option value="Inactive">Inactive</option>
                                            </select>
                                        </div>
                                    </div>
                                </div>

                                <div class="modal-footer">
                                    <button type="button" class="btn btn-secondary" style="font-family: sans-serif; font-size: 12px;"
                                        data-bs-dismiss="modal">Cancel</button>
                                    <button type="submit" class="btn btn-primary"
                                        style="font-family: sans-serif; font-size: 12px;">Save Changes</button>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>
            </div>

            <div class="modal fade" id="requirementModal" tabindex="-1" aria-labelledby="requirementModalLabel"
                aria-hidden="true">
                <div class="modal-dialog modal-dialog-centered modal-xl">
                    <div class="modal-content">
                        <div class="modal-header">
                            <div class="w-100">
                                <h5 class="modal-title custom-label" id="requirementModalLabel"
                                    style="font-family: sans-serif; font-size: 18px; color: rgb(0,0,0); font-weight: bold;">
                                    Government Issued Documents
                                </h5>
                            </div>
                            <button type="button" class="btn-close" data-bs-dismiss="modal"
                                aria-label="Close"></button>
                        </div>
                        <div class="modal-body">
                            <form action="{{ route('requirements.store') }}" method="POST">
                                @csrf
                                <div class="row">
                                    <div class="col-md-6">
                                        <div class="mb-3">
                                            <label for="description" class="form-label custom-label">Government Issued
                                                ID<span class="required-field">*</span></label>
                                            <input type="text" name="issued_id" id="issued_id"
                                                class="form-control custom-input" required style="height: 34px;">
                                        </div>
                                    </div>
                                    <div class="col-md-3">
                                        <div class="mb-3">
                                            <label for="edit-with_expiration" class="form-label custom-label">With Expiration<span class="required-field">*</span></label>
                                            <select class="form-select custom-input" name="with_expiration" id="edit-with_expiration" required>
                                                <option value="1">Yes</option>
                                                <option value="0" selected>No</option>
                                            </select>
                                        </div>
                                    </div>
                                    <div class="col-md-3">
                                        <div class="mb-3">
                                            <label for="status" class="form-label custom-label">Status<span
                                                    class="required-field">*</span></label>
                                            <select name="status" id="status" class="form-select custom-input"
                                                required>
                                                <option value="Active">Active</option>
                                                <option value="Inactive">Inactive</option>
                                            </select>
                                        </div>
                                    </div>
                                </div>
                                <div class="mb-3">

                                </div>

                                <div class="text-end">
                                <button type="button" class="btn btn-secondary"
                                                                style="font-family: sans-serif; font-size: 12px;"
                                                                data-bs-dismiss="modal">Cancel</button>
                                    <button type="submit" class="btn btn-primary"
                                        style="font-family: sans-serif; font-size: 12px;">Save Changes </button>
                                </div>

                            </form>
                        </div>
                    </div>
                </div>
            </div>



        </div>
    </div>
@endsection
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script>
$(document).on('click', '.edit-req-btn', function () {
    let id = $(this).data('id');
    let description = $(this).data('description');
    let status = $(this).data('status');
    let with_expiration = $(this).data('with_expiration');
    $('#edit-issued-id').val(description);
    $('#edit-status').val(status);
    $('#edit-with_expiration').val(with_expiration);
    let formAction = '{{ route("requrement.update", ":id") }}';
    formAction = formAction.replace(':id', id);
    $('#editReqForm').attr('action', formAction);
});
$(document).ready(function() {
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
        dom: "<'row'<'col-sm-12'f>>" +"<'row'<'col-sm-3'l><'col-sm-9'p>>" +"<'row'<'col-sm-12'tr>>" +"<'row'<'col-sm-12'p>>",
        ajax: {
            url: "{{ route('authorized.getList') }}",
            data: function(d) {
                d.status = $('#status').val();
                d.q = $('#q').val();
            }
        },
        pageLength: 10,
        columns: [
            { data: 'no', orderable: false },
            { data: 'description' },
            { data: 'with_expiration' },
            { data: 'status' },
            { data: 'action', orderable: false, searchable: false }
        ]
    });
}

</script>