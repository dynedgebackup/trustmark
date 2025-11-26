@extends('layouts.app')
@include('layouts.layout')
@section('content')
    <div class="d-sm-flex align-items-center justify-content-between mb-4">
        <h1 class="h3 mb-0 text-gray-800">{{ __('Cron-Job') }}</h1>

        <a href="#" data-size="lg" data-url="{{ route('cron-job.store') }}" data-ajax-popup="true" data-bs-toggle="tooltip"
            title="{{ __('Manage Cron-Job') }}" data-title="Manage Cron-Job" class="btn btn-sm btn-primary">
            <i class="fas fa-plus"></i>
        </a>

    </div>
    <div class="row d-flex align-items-center justify-content-end" style="padding-bottom: 20px;">
        <div class="col-md-3">
            <div class="form-group" id="parrent_reg_no">
                <label for="department_id" class="form-label">{{ __('Department') }}</label>
                <span class="validate-err"></span>
                <div class="form-icon-user">
                    <select name="department_id" id="department_id" class="form-control select3" required
                        style="width:100%;">
                        <option value=""></option>

                    </select>
                </div>
                <span class="validate-err" id="err_app_code"></span>
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
                        <table id="dataTable1" class="table table-bordered" style="width: 98.5% !important;">
                            <thead>
                                <tr>
                                    <th>{{ __('No.') }}</th>
                                    <th>{{ __('Departments') }}</th>
                                    <th>{{ __('URL') }}</th>
                                    <th>{{ __('Description') }}</th>
                                    <th>{{ __('Schedule Type') }}</th>
                                    <th>{{ __('Response') }}</th>
                                    <th>{{ __('Last Executed') }}</th>
                                    <th>{{ __('Cron') }}</th>
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
@endsection
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script>
    $(document).ready(function() {
        select3Ajax("department_id", "parrent_reg_no", "allCronDepartmentAjaxList");
        datatablefunction();
        $("#btn_search").click(function() {
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
            dom: "<'row'<'col-sm-12'f>>" + "<'row'<'col-sm-3'l><'col-sm-9'p>>" + "<'row'<'col-sm-12'tr>>" +
                "<'row'<'col-sm-12'p>>",
            ajax: {
                url: "{{ route('cron-job.getList') }}",
                data: function(d) {
                    d.menu_group_id = $('#Group_id_filter').val();
                    d.status = $('#status').val();
                    d.q = $('#q').val();
                }
            },
            pageLength: 10,
            columns: [{
                    data: 'no',
                    orderable: false
                },
                {
                    data: 'department'
                },
                {
                    data: 'url'
                },
                {
                    data: 'description'
                },
                {
                    data: 'schedule_type'
                },
                {
                    data: 'response'
                },
                {
                    data: 'lastExecuted'
                },
                {
                    data: 'quickRun'
                },
                {
                    data: 'status'
                },
                {
                    data: 'action',
                    orderable: false,
                    searchable: false
                }
            ]
        });
    }
</script>

<script type="text/javascript">
    $(document).ready(function() {
        $(document).on('click', '.activeinactive', function(e) {
            e.preventDefault();
            var id = $(this).data('id');
            var status = $(this).data('status');
            ActiveInactiveUpdate(id, status);
        });

        function ActiveInactiveUpdate(id, is_activeinactive) {
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
                if (result.isConfirmed) {
                    $.ajax({
                        url: '{{ route('scheduleFees.ActiveInactive') }}',
                        type: "POST",
                        data: {
                            "id": id,
                            "is_activeinactive": is_activeinactive,
                            _token: '{{ csrf_token() }}'
                        },
                        success: function(html) {
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
                url: '{{ route('scheduleFees.division.list') }}',
                method: 'POST',
                data: {
                    id: officeId,
                    _token: '{{ csrf_token() }}'
                },
                success: function(response) {
                    $('#divisionDetails').html(response.html);
                    $('.btn_cancel_division').off('click').on('click', function() {
                        $(this).closest('.removedivisiondata').remove();
                    });
                },
                error: function() {
                    console.error('Failed to load division details.');
                }
            });
        }
        $(document).on('click', '.btn-edit-office', function(e) {
            e.preventDefault();

            let officeId = $(this).data('id');
            let editUrl = $(this).data('url');
            $.ajax({
                url: editUrl,
                type: 'GET',
                success: function(res) {
                    $('#ajaxModal .modal-body').html(res);
                    $('#ajaxModal').modal('show');
                    setTimeout(function() {
                        loadDivisionDetails(officeId);
                    }, 300);
                }
            });
        });
    });
</script>

<script>
    const BASE_URL = "{{ url('/') }}/";
</script>
<script src="{{ asset('js/cron-job.js') }}"></script>