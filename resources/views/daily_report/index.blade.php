@extends('layouts.app')
@include('layouts.layout')
@section('content')
<script src="https://cdnjs.cloudflare.com/ajax/libs/exceljs/4.3.0/exceljs.min.js"></script>
<div class="d-sm-flex align-items-center justify-content-between mb-4">
    <h1 class="h3 mb-0 text-gray-800">{{ __('Daily Report') }}</h1>

    <a href="#" onclick="loadDataForExcelSheet()" id="exportExcelData" class="btn btn-sm btn-primary action-item">
        <i class="fa fa-file-excel-o"></i> Export Excel
    </a>

</div>
<div class="row d-flex align-items-center justify-content-end" style="padding-bottom: 20px;">
            <div class="col-xl-2 col-lg-2 col-md-6 col-sm-12 col-12">
                <div class="btn-box">
                <label for="Search" class="form-label">{{ __('From Date') }}</label>
                <input type="date" name="fromdate" id="fromdate" class="form-control" value="{{ date('Y-m-d') }}"  style="font-size:12px; padding:9px;">
                </div>
            </div>
            <div class="col-xl-2 col-lg-2 col-md-6 col-sm-12 col-12">
                <div class="btn-box">
                <label for="Search" class="form-label">{{ __('To Date') }}</label>
                <input type="date" name="todate" id="todate" class="form-control" value="{{ date('Y-m-d') }}"   style="font-size:12px; padding:9px;">
                </div>
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
                    </div>
            <div class="col-xl-2 col-lg-2 col-md-6 col-sm-12 col-12">
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
                <div class="table-responsive" style="overflow-x:auto !important;">
                    <table id="dataTable1" class="table table-bordered" style="width:100%; table-layout: fixed;">
                        <thead>
                            <tr>
                                <th style="width: 20px;">{{ __('No.') }}</th>
                                <th style="width: 200px;">{{ __('Security No.') }}</th>
                                <th style="width: 120px;">{{ __('Business Name') }}</th>
                                <th style="width: 120px;">{{ __('Registration No.') }}</th>
                                <th style="width: 120px;">{{ __('Business Type') }}</th>
                                <th style="width: 120px;">{{ __('TIN') }}</th>
                                <th style="width: 120px;">{{ __('Representative') }}</th>
                                <th style="width: 120px;">{{ __('Payment') }}</th>
                                <th style="width: 120px;">{{ __('Remarks') }}</th>
                                <th style="width: 120px;">{{ __('Status') }}</th>
                                <th style="width: 120px;">{{ __('Email Address') }}</th>
                                <th style="width: 120px;">{{ __('Contact No.') }}</th>
                                <th style="width: 120px;">{{ __('Evaluator') }}</th>
                                <th style="width: 120px;">{{ __('Date Submitted') }}</th>
                                <th style="width: 120px;">{{ __('Date Approved') }}</th>
                                <th style="width: 120px;">{{ __('Date Issued') }}</th>
                                <th style="width: 120px;">{{ __('Date Disapproved') }}</th>
                                <th style="width: 120px;">{{ __('Date Returned') }}</th>
                                <th style="width: 120px;">{{ __('Date Created') }}</th>
                                <th style="width: 120px;">{{ __('Channel') }}</th>
                                <th style="width: 120px;">{{ __('Complete Address') }}</th>
                                <th style="width: 120px;">{{ __('Barangay') }}</th>
                                <th style="width: 120px;">{{ __('Municipality/City') }}</th>
                                <th style="width: 120px;">{{ __('Province') }}</th>
                                <th style="width: 120px;">{{ __('Region') }}</th>
                                <th style="width: 120px;">{{ __('With BMBE (Yes/No)') }}</th>
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
$(document).ready(function() {
    select3Ajax("fees_id_filter","parrent_reg_no","getFeesAjaxList");
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
        autoWidth: false, // prevent auto shrink
        columns: [
            { data: 'no', orderable: false, width: "20px" },
            { data: 'SecurityNo', width: "200px" },
            { data: 'BusinessName', width: "120px" },
            { data: 'RegistrationNo', width: "120px" },
            { data: 'BusinessType', width: "120px" },
            { data: 'TIN', width: "120px" },
            { data: 'Representative', width: "120px" },
            { data: 'Payment', width: "120px" },
            { data: 'Remarks', width: "120px" },
            { data: 'Status', width: "120px" },
            { data: 'EmailAddress', width: "120px" },
            { data: 'ContactNo', width: "120px" },
            { data: 'Evaluator', width: "120px" },
            { data: 'DateSubmitted', width: "120px" },
            { data: 'DateApproved', width: "120px" },
            { data: 'DateIssued', width: "120px" },
            { data: 'Channel', width: "120px" },
            { data: 'CompleteAddress', width: "120px" },
            { data: 'Barangay', width: "120px" },
            { data: 'unicipality_City', width: "120px" },
            { data: 'Province', width: "120px" },
            { data: 'Region', width: "120px" },
            { data: 'BMBE', width: "120px" }
        ],
        processing: true,
        serverSide: true,
        searching: false,
        dom: "<'row'<'col-sm-12'f>>" +"<'row'<'col-sm-3'l><'col-sm-9'p>>" +"<'row'<'col-sm-12'tr>>" +"<'row'<'col-sm-12'p>>",
        ajax: {
            url: "{{ route('dailyreport.getList') }}",
            data: function(d) {
                d.status = $('#status').val();
                d.fromdate = $('#fromdate').val();
                d.todate = $('#todate').val();
                d.q = $('#q').val();
            }
        },
        pageLength: 10,
        columns: [
            { data: 'no', orderable: false },
            { data: 'SecurityNo' },
            { data: 'BusinessName' },
            { data: 'RegistrationNo' },
            { data: 'BusinessType' },
            { data: 'TIN' },
            { data: 'Representative' },
            { data: 'Payment' },
            makeRemarkColumn('Remarks'),
            { data: 'Status' },
            makeRemarkColumn('EmailAddress'),
            { data: 'ContactNo' },
            { data: 'Evaluator' },
            { data: 'DateSubmitted' },
            { data: 'DateApproved' },
            { data: 'DateIssued' },
            { data: 'Datedisapproved' },
            { data: 'Datereturned' },
            { data: 'Datecreated_at' },
            { data: 'Channel' },
            { data: 'Complete_Address' },
            { data: 'Barangay' },
            { data: 'Municipality_City' },
            { data: 'Province' },
            { data: 'Region' },
            { data: 'witbemb' }
        ],
        drawCallback: function() {
            bindToggleEvents();
        }
    });
}
function renderRemarks(data) {
    if (!data) return '';

    if (data.length > 25) {
        const shortText = data.substr(0, 25) + '...';

        return `
            <div class="remark-box" style="white-space: normal; word-break: break-word;">
                <span class="short-text">${shortText}</span>
                <span class="full-text" style="display:none;">${data}</span>
                <a href="#" class="toggle-text" style="color:blue; font-size:12px;">Read more</a>
            </div>
        `;
    }

    return `<div style="white-space: normal; word-break: break-word;">${data}</div>`;
}
function makeRemarkColumn(fieldName) {
    return {
        data: fieldName,
        render: renderRemarks,
        width: "150px"
    };
}
function bindToggleEvents() {
    $('.toggle-text').off('click').on('click', function(e) {
        e.preventDefault();

        let container = $(this).closest('.remark-box');
        let shortText = container.find('.short-text');
        let fullText = container.find('.full-text');

        if (fullText.is(':visible')) {
            fullText.hide();
            shortText.show();
            $(this).text('Read more');
        } else {
            shortText.hide();
            fullText.show();
            $(this).text('Read less');
        }
    });
}

function bindReadMoreToggle() {
    $('#dataTable1').off('click', '.toggle-text').on('click', '.toggle-text', function(e) {
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
}
async function loadDataForExcelSheet() {
    const status = $('#status').val();
    const fromdate = $('#fromdate').val();
    const todate = $('#todate').val();
    const q = $('#q').val();

    try {
        const exportUrl = `{{ route('dailyreport.exportAll') }}?status=${status}&fromdate=${fromdate}&todate=${todate}&q=${q}`;
        const response = await fetch(exportUrl);
        const result = await response.json();
        const workbook = new ExcelJS.Workbook();
        const worksheet = workbook.addWorksheet("Daily Report");
        const headerRow = worksheet.addRow([
            "No.", "Security No.", "Business Name", "Registration No.",
            "Business Type", "TIN", "Representative", "Payment", "Remarks", "Status", "Email Address"
            , "Contact No.", "Evaluator", "Date Submitted", "Date Approved", "Date Issued" , "Date Disapproved" , "Date Returned" , "Date Created","Channel","Complete Address","Barangay","Municipality/City","Province","Region","With BMBE (Yes/No)"
        ]);
        headerRow.eachCell((cell) => {
            cell.fill = {
                type: 'pattern',
                pattern: 'solid',
                fgColor: { argb: '1E90FF' } 
            };
            cell.font = {
                color: { argb: 'FFFFFF' },
                bold: true
            };
            cell.border = {
                top:    { style: 'thin' },
                left:   { style: 'thin' },
                bottom: { style: 'thin' },
                right:  { style: 'thin' }
            };
            cell.alignment = { vertical: 'middle', horizontal: 'center' };
        });
        result.data.forEach((row, index) => {
            const newRow = worksheet.addRow([
                index + 1,
                row.SecurityNo,
                row.BusinessName,
                row.RegistrationNo,
                row.BusinessType,
                row.TIN,
                row.Representative,
                row.Payment,
                row.Remarks,
                row.Status,
                row.EmailAddress,
                row.ContactNo,
                row.Evaluator,
                row.DateSubmitted,
                row.DateApproved,
                row.DateIssued,
                row.Datedisapproved,
                row.Datereturned,
                row.Datecreated_at,
                row.Channel,
                row.Complete_Address,
                row.Barangay,
                row.Municipality_City,
                row.Province,
                row.Region,
                row.withBMBE
            ]);
            newRow.eachCell(cell => {
                cell.border = {
                    top:    { style: 'thin' },
                    left:   { style: 'thin' },
                    bottom: { style: 'thin' },
                    right:  { style: 'thin' }
                };
            });
        });
        worksheet.columns.forEach(column => {
            let maxLength = 10;
            column.eachCell({ includeEmpty: true }, cell => {
                const value = cell.value ? cell.value.toString() : '';
                maxLength = Math.max(maxLength, value.length);
            });
            column.width = maxLength + 2;
        });
        const buffer = await workbook.xlsx.writeBuffer();
        const blob = new Blob([buffer], { type: "application/vnd.openxmlformats-officedocument.spreadsheetml.sheet" });
        const url = window.URL.createObjectURL(blob);

        const a = document.createElement("a");
        a.href = url;
        a.download = "Daily_Report.xlsx";
        a.click();
        window.URL.revokeObjectURL(url);
    } catch (error) {
        console.error("Export failed:", error);
        alert("Failed to export data.");
    }
}
</script>

