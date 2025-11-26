@extends('layouts.app')

@section('content')
    <div class="d-flex justify-content-between align-items-center mb-4" style="margin-top: 15px;">
        <h3 class="page-title">DASHBOARD</h3>
    </div>

    <ol class="breadcrumb custom-breadcrumb">
        <li class="breadcrumb-item"><a href="{{ route('dashboard') }}"><span>Dashboard</span></a></li>
        <!-- <li class="breadcrumb-item"><a href="{{ route('business.index') }}"><span>Business Registry</span></a></li> -->
    </ol>
    <style>
        /* Hide DataTables search bar */
        div.dataTables_filter {
            display: none;
        }

        .d-none {
            display: none;
        }
    </style>
    <div id="wrapper">
        <div class="d-flex flex-column" id="content-wrapper">
             @include('dashboard_count_common')
            @if (Auth::user()->role != 1)
                <div class="row" style="margin-bottom: 15px;background: #fff;padding: 20px;">
                    <div class="col">
                        <div class="row d-flex align-items-center justify-content-end">
                        <form action="{{ route('dashboard') }}" method="GET"
                                        class="d-flex align-items-center justify-content-end gap-2">
                            <div class="col-xl-2 col-lg-2 col-md-6 col-sm-12 col-12">
                                <div class="btn-box">
                                <label for="Search" class="form-label custom-label">{{ __('From Date') }}</label>
                                <input type="date" name="fromdate" id="fromdate" class="form-control custom-input" value="{{ $displayStartDate }}"  style="font-size:12px;">
                                </div>
                            </div>
                            <div class="col-xl-2 col-lg-2 col-md-6 col-sm-12 col-12">
                                <div class="btn-box">
                                <label for="Search" class="form-label custom-label">{{ __('To Date') }}</label>
                                <input type="date" name="todate" id="todate" class="form-control custom-input" value="{{ $displayEndDate }}"   style="font-size:12px; ">
                                </div>
                            </div>
                            <div class="col-md-4">
                            <label for="representative" class="form-label custom-label">Details</label>
                                <input type="text" name="details" id="details" class="form-control custom-input"
                                    value="{{ request('details') }}" placeholder="Details">
                            </div>
                            <button type="button" id="btn_search" class="btn btn-primary"
                                                style="font-size: 12px;margin-top: 27px;">Search</button>
                            </form>
                                
                        </div>
                    </div>
                </div>
            @endif

            <div class="card shadow">
                <div class="card">
                     <div class="card-header">
                                <h6 class="text-primary fw-bold m-0" style="font-size: 12px;">Under-Evaluations Applications</h6>
                     </div>
                <div class="card-body">
                    <div class="row">
                    </div>
                    <div class="table-responsive table mt-2" id="dataTable-1" role="grid"
                        aria-describedby="dataTable_info">
                        <table class="table my-0" id="jqtabledataTable" style="width:100%; table-layout: fixed;">
                            <thead>
                                <tr>
                                    <th class="custom-th" style="width: 50px;">No</th>
                                    <!-- <th class="custom-th" style="width: 120px;">Security No.</th> -->
                                    <th class="custom-th" style="width: 200px;">Business Name</th>
                                    <th class="custom-th" style="width: 150px;">Registration No.</th>
                                    <th class="custom-th" style="width: 120px;">TIN</th>
                                    <th class="custom-th" style="width: 150px;">Business Type</th>
                                    <th class="custom-th" style="width: 150px;">Submitted Date</th>
                                    <th class="custom-th" style="width: 150px;">No of days</th>
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
    </div>
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            document.querySelectorAll('.read-toggle').forEach(function(toggle) {
                toggle.addEventListener('click', function() {
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
    const table = $('#jqtabledataTable').DataTable({
        destroy: true,
        processing: true,
        serverSide: true,
        pagingType: "full_numbers",
        ajax: {
            url: DIR +'get-list-under',
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
        dom: "<'flex justify-start items-center px-4 py-2' l>" + "rt" + "<'flex justify-between items-center px-4 py-2'<'text-sm text-gray-700' i><'flex gap-2' p>>",
        pageLength: 10,
        lengthMenu: [10, 20, 30, 50],
        order: [],
        searching: false,
        columnDefs: [
            { orderable: false, targets: [0, 7] },
            { className: 'text-start', targets: 0 }
        ],
        columns: [
            { data: "srno" },
            // { data: "trustmark_id" },
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
            { data: "date_submitted" },
            { data: "no_of_days" },
            { data: "status" },
            { data: "action" }
        ]
    });
   };
    $('#jqtabledataTable').on('click', '.toggle-text', function(e){
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
@endsection
