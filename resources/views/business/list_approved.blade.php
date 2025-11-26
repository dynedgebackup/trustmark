@extends('layouts.app')

@section('content')
    <div class="d-flex justify-content-between align-items-center mb-4" style="margin-top: 15px;">
        <h3 class="page-title">DASHBOARD</h3>
    </div>

    <ol class="breadcrumb custom-breadcrumb">
        <li class="breadcrumb-item"><a href="{{ route('dashboard') }}"><span>Dashboard</span></a></li>
        <!-- <li class="breadcrumb-item"><a href="{{ route('business.index') }}"><span>Business Registry</span></a></li> -->
    </ol>

    <div id="wrapper">
        <div class="d-flex flex-column" id="content-wrapper">
            @include('dashboard_count_common')
           <!--  <div class="row" style="margin-bottom: 15px;">
                <div class="col">
                    <div class="card">

                        <div class="row mb-3">
                            <div class="col text-center">
                                <form action="{{ route('business.list-approved') }}" method="GET"
                                    class="d-inline-flex align-items-end gap-2">
                                    <div>
                                        <label for="max-date" class="form-label mb-0" style="font-size: 12px;">Submit
                                            Date</label>
                                        <input type="date" id="max-date" name="submit_date"
                                            value="{{ request('submit_date') }}" class="form-control"
                                            style="font-size: 12px;">
                                    </div>
                                    <div>
                                        <button type="submit" class="btn btn-primary"
                                            style="font-size: 12px;">Search</button>
                                    </div>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>
            </div> -->

            <div class="card shadow">
                <div class="card-body">
                    <div class="row">
                        <h6 class="text-primary fw-bold m-0" style="font-size: 12px;">Approved Applications</h6>
                    </div>
                    <div class="table-responsive table mt-2" id="dataTable-1" role="grid"
                        aria-describedby="dataTable_info">
                        <table class="table my-0" id="dataTableapproved" style="width:100%; table-layout: fixed;">
                            <thead>
                                <tr>
                                    <th class="custom-th" style="width: 50px;">No</th>
                                    <th class="custom-th" style="width: 120px;">Security No.</th>
                                    <th class="custom-th" style="width: 200px;">Business Name</th>
                                    <th class="custom-th" style="width: 150px;">Registration No.</th>
                                    <th class="custom-th" style="width: 150px;">Business Type</th>
                                    <th class="custom-th" style="width: 120px;">TIN</th>
                                    <th class="custom-th" style="width: 150px;">Representative</th>
                                    <th class="custom-th" style="width: 100px;">Payment</th>
                                    <th class="custom-th" style="width: 100px;text-align:center;">Status</th>
                                    <th class="custom-th" style="width: 50px;text-align:center;">Action</th>
                                </tr>
                            </thead>
                            <tbody>
                               <!-- @forelse ($businesses as $index => $business)
                                    <tr>
                                        <td class="custom-td">{{ $loop->iteration }}</td>
                                        <td class="custom-td">{{ $business->trustmark_id }}</td>
                                        <td class="custom-td" style="white-space: normal; word-wrap: break-word;">
                                            @php
                                                $text = $business->business_name ?? 'N/A';
                                                $previewLimit = 25;
                                                $isLong = strlen($text) > $previewLimit;
                                                $shortText = $isLong ? substr($text, 0, $previewLimit) . '...' : $text;
                                            @endphp

                                            <span class="short-text">{{ $shortText }}</span>

                                            @if ($isLong)
                                                <span class="full-text d-none">{{ $text }}</span>
                                                <a href="javascript:void(0);" class="read-toggle text-primary small">Read
                                                    more</a>
                                            @endif
                                        </td>
                                        <td class="custom-td" title="{{ $business->reg_num }}">
                                            {{ \Illuminate\Support\Str::limit($business->reg_num, 25, '...') }}
                                        </td>
                                        <td class="custom-td">{{ $business->corporationType->name ?? ' ' }}</td>
                                        <td class="custom-td">{{ $business->tin }}</td>
                                        <td class="custom-td">{{ $business->pic_name }}</td>
                                        <td class="custom-td">
                                            @php
                                                $paymentStatus = $business->payment_id === null ? 'Unpaid' : 'Paid';
                                                $paymentBadgeClass = match ($paymentStatus) {
                                                    'Paid' => 'badge-bg-approve', // green-like color
                                                    'Unpaid' => 'badge-bg-returned', // red-like color
                                                    default => 'badge-bg-draft', // fallback color
                                                };
                                            @endphp
                                            <span
                                                class="badge {{ $paymentBadgeClass }} px-2 py-1 small text-center d-inline-block"
                                                style="min-width: 80px;">
                                                {{ $paymentStatus }}
                                            </span>
                                        </td>
                                        <td class="custom-td text-center align-middle">
                                            @php
                                                $status = $business->status;
                                                $badgeClass = match ($status) {
                                                    'APPROVED' => 'badge-bg-approve',
                                                    'UNDER EVALUATION' => 'badge-bg-evaluation',
                                                    'RETURNED' => 'badge-bg-returned',
                                                    default => 'badge-bg-draft',
                                                };
                                            @endphp

                                            <span
                                                class="badge {{ $badgeClass }} px-2 py-1 small text-center d-inline-block"
                                                style="min-width: 80px;">
                                                {{ $status }}
                                            </span>
                                        </td>
                                        <td style="text-align:center;">
                                            <a href="{{ route('business.view', encrypt($business->id)) }}"
                                                data-bs-toggle="tooltip" data-bs-placement="bottom" title="View">
                                                <i class="custom-eye-icon fa fa-eye"></i>
                                            </a>
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
    <script>
        
        $(document).ready(function(){
         datatablefunction();
          $("#btn_search").click(function(){
        datatablefunction();
        }); 
    })
    function  datatablefunction() {
    var DIR =$("#DIR").val();
    const table = $('#dataTableapproved').DataTable({
        destroy: true,
        processing: true,
        serverSide: true,
        pagingType: "full_numbers",
        ajax: {
            url: DIR +'get-list-approved',
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
            { data: "business_type" },
            { data: "tin" },
            { data: "representative" },
            { data: "paymnetsttaus" },
            // { data: "no_of_days" },
            { data: "status" },
            { data: "action" }
        ]
    });
   };
    $('#dataTableapproved').on('click', '.toggle-text', function(e){
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
    </script>
@endsection
