@extends('layouts.app')
@include('layouts.layout')
@section('content')
<script src="https://cdnjs.cloudflare.com/ajax/libs/exceljs/4.3.0/exceljs.min.js"></script>
<div class="d-sm-flex align-items-center justify-content-between mb-4">
    <h1 class="h3 mb-0 text-gray-800">{{ __('Returned Applications') }}</h1>

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
                                <th style="width: 20px;  text-align: center; vertical-align: middle;background: #09325d;color: #fff;" rowspan="3">{{ __('No.') }}</th>
                                <th style="width: 200px;  text-align: center; vertical-align: middle;background: #09325d;color: #fff;" rowspan="3">{{ __('SECURITY NO.') }}</th>
                                <th style="width: 120px;  text-align: center; vertical-align: middle;background: #09325d;color: #fff;" rowspan="3">{{ __('BUSINESS NAME') }}</th>
                                <th style="width: 120px;  text-align: center; vertical-align: middle;background: #09325d;color: #fff;" rowspan="3">{{ __('EVALUATOR') }}</th>
                                <th style="width: 120px;  text-align: center; vertical-align: middle;background: #09325d;color: #fff;" rowspan="3">{{ __('REPRESENTATIVE') }}</th>
                                <th style="width: 1600px; text-align: center; vertical-align: middle;background: #09325d;color: #fff;" colspan="12">{{ __('BUSINESS INFORMATION') }}</th>
                                <th style="width: 300px; text-align: center; vertical-align: middle;background: #09325d;color: #fff;" colspan="2" rowspan="2">{{ __('BUSINESS URL') }}</th>
                                <th style="width: 1500px; text-align: center; vertical-align: middle;background: #09325d;color: #fff;" colspan="12">{{ __('AUTHORIZED REPRESENTATIVE') }}</th>
                                <th style="width: 1300px; text-align: center; vertical-align: middle;background: #09325d;color: #fff;" colspan="10">{{ __('ADDRESS INFORMATION') }}</th>
                                <th style="width: 1500px; text-align: center; vertical-align: middle;background: #09325d;color: #fff;" colspan="12">{{ __('ATTACHMENTS') }}</th>
                                <th style="width: 300px; text-align: center; vertical-align: middle;background: #09325d;color: #fff;" colspan="2" rowspan="2">{{ __('ADDITIONAL PERMITS') }}</th>
                                <th style="width: 120px; text-align: center; vertical-align: middle;background: #09325d;color: #fff;" rowspan="3">{{ __('REMARKS') }}</th>
                            </tr>
                            
                            <tr>
                                <th style="width: 320px;background: #4e73df;color: #fff;text-align: center;" colspan="2">{{ __('BUSINESS TYPE') }}</th>
                                <th style="width: 320px;background: #4e73df;color: #fff;text-align: center;" colspan="2">{{ __('BUSINESS NAME') }}</th>
                                <th style="width: 320px;background: #4e73df;color: #fff;text-align: center;" colspan="2">{{ __('TRADE NAME') }}</th>
                                <th style="width: 320px;background: #4e73df;color: #fff;text-align: center;" colspan="2">{{ __('BUSINESS CATEGORY') }}</th>
                                <th style="width: 320px;background: #4e73df;color: #fff;text-align: center;" colspan="2">{{ __('REGISTRATION NO.') }}</th>
                                <th style="width: 320px;background: #4e73df;color: #fff;text-align: center;" colspan="2">{{ __('TIN') }}</th>
                                <th style="width: 320px;background: #4e73df;color: #fff;text-align: center;" colspan="2">{{ __('NAME') }}</th>
                                <th style="width: 320px;background: #4e73df;color: #fff;text-align: center;" colspan="2">{{ __('MOBILE NO.') }}</th>
                                <th style="width: 320px;background: #4e73df;color: #fff;text-align: center;" colspan="2">{{ __('EMAIL') }}</th>
                                <th style="width: 320px;background: #4e73df;color: #fff;text-align: center;" colspan="2">{{ __('GOVERNMENT ISSUED ID') }}</th>
                                <th style="width: 320px;background: #4e73df;color: #fff;text-align: center;" colspan="2">{{ __('GOV-ISSUED ID ATTACHMENT') }}</th>
                                <th style="width: 320px;background: #4e73df;color: #fff;text-align: center;" colspan="2">{{ __('GOV-ISSUED ID EXPIRY') }}</th>
                                <th style="width: 320px;background: #4e73df;color: #fff;text-align: center;" colspan="2">{{ __('COMPLETE ADDRESS') }}</th>
                                <th style="width: 320px;background: #4e73df;color: #fff;text-align: center;" colspan="2">{{ __('BARANGAY') }}</th>
                                <th style="width: 320px;background: #4e73df;color: #fff;text-align: center;" colspan="2">{{ __('MUNICIPALITY | CITY') }}</th>
                                <th style="width: 320px;background: #4e73df;color: #fff;text-align: center;" colspan="2">{{ __('PROVINCE') }}</th>
                                <th style="width: 320px;background: #4e73df;color: #fff;text-align: center;" colspan="2">{{ __('REGION') }}</th>
                                <th style="width: 320px;background: #4e73df;color: #fff;text-align: center;" colspan="2">{{ __('BUSINESS REGISTRATION') }}</th>
                                <th style="width: 320px;background: #4e73df;color: #fff;text-align: center;" colspan="2">{{ __('BIR 2303') }}</th>
                                <th style="width: 320px;background: #4e73df;color: #fff;text-align: center;" colspan="2">{{ __('IRM') }}</th>
                                <th style="width: 320px;background: #4e73df;color: #fff;text-align: center;" colspan="2">{{ __('BMBE') }}</th>
                                <th style="width: 320px;background: #4e73df;color: #fff;text-align: center;" colspan="2">{{ __('BUSINESS CATEGORY(ASSET SIZE)') }}</th>
                                <th style="width: 320px;background: #4e73df;color: #fff;text-align: center;" colspan="2">{{ __('TOTAL ASSET VALUATION') }}</th>
                            </tr>
                            <tr>
                                <th style="background: #09325d;color: #fff;">{{ __('COMPLIANCE?') }}</th>
                                <th style="background: #09325d;color: #fff;">{{ __('REMARKS') }}</th>
                                <th style="background: #09325d;color: #fff;">{{ __('COMPLIANCE?') }}</th>
                                <th style="background: #09325d;color: #fff;">{{ __('REMARKS') }}</th>
                                <th style="background: #09325d;color: #fff;">{{ __('COMPLIANCE?') }}</th>
                                <th style="background: #09325d;color: #fff;">{{ __('REMARKS') }}</th>
                                <th style="background: #09325d;color: #fff;">{{ __('COMPLIANCE?') }}</th>
                                <th style="background: #09325d;color: #fff;">{{ __('REMARKS') }}</th>
                                <th style="background: #09325d;color: #fff;">{{ __('COMPLIANCE?') }}</th>
                                <th style="background: #09325d;color: #fff;">{{ __('REMARKS') }}</th>
                                <th style="background: #09325d;color: #fff;">{{ __('COMPLIANCE?') }}</th>
                                <th style="background: #09325d;color: #fff;">{{ __('REMARKS') }}</th>
                                <th style="background: #09325d;color: #fff;">{{ __('COMPLIANCE?') }}</th>
                                <th style="background: #09325d;color: #fff;">{{ __('REMARKS') }}</th>
                                <th style="background: #09325d;color: #fff;">{{ __('COMPLIANCE?') }}</th>
                                <th style="background: #09325d;color: #fff;">{{ __('REMARKS') }}</th>
                                <th style="background: #09325d;color: #fff;">{{ __('COMPLIANCE?') }}</th>
                                <th style="background: #09325d;color: #fff;">{{ __('REMARKS') }}</th>
                                <th style="background: #09325d;color: #fff;">{{ __('COMPLIANCE?') }}</th>
                                <th style="background: #09325d;color: #fff;">{{ __('REMARKS') }}</th>
                                <th style="background: #09325d;color: #fff;">{{ __('COMPLIANCE?') }}</th>
                                <th style="background: #09325d;color: #fff;">{{ __('REMARKS') }}</th>
                                <th style="background: #09325d;color: #fff;">{{ __('COMPLIANCE?') }}</th>
                                <th style="background: #09325d;color: #fff;">{{ __('REMARKS') }}</th>
                                <th style="background: #09325d;color: #fff;">{{ __('COMPLIANCE?') }}</th>
                                <th style="background: #09325d;color: #fff;">{{ __('REMARKS') }}</th>
                                <th style="background: #09325d;color: #fff;">{{ __('COMPLIANCE?') }}</th>
                                <th style="background: #09325d;color: #fff;">{{ __('REMARKS') }}</th>
                                <th style="background: #09325d;color: #fff;">{{ __('COMPLIANCE?') }}</th>
                                <th style="background: #09325d;color: #fff;">{{ __('REMARKS') }}</th>
                                <th style="background: #09325d;color: #fff;">{{ __('COMPLIANCE?') }}</th>
                                <th style="background: #09325d;color: #fff;">{{ __('REMARKS') }}</th>
                                <th style="background: #09325d;color: #fff;">{{ __('COMPLIANCE?') }}</th>
                                <th style="background: #09325d;color: #fff;">{{ __('REMARKS') }}</th>
                                <th style="background: #09325d;color: #fff;">{{ __('COMPLIANCE?') }}</th>
                                <th style="background: #09325d;color: #fff;">{{ __('REMARKS') }}</th>
                                <th style="background: #09325d;color: #fff;">{{ __('COMPLIANCE?') }}</th>
                                <th style="background: #09325d;color: #fff;">{{ __('REMARKS') }}</th>
                                <th style="background: #09325d;color: #fff;">{{ __('COMPLIANCE?') }}</th>
                                <th style="background: #09325d;color: #fff;">{{ __('REMARKS') }}</th>
                                <th style="background: #09325d;color: #fff;">{{ __('COMPLIANCE?') }}</th>
                                <th style="background: #09325d;color: #fff;">{{ __('REMARKS') }}</th>
                                <th style="background: #09325d;color: #fff;">{{ __('COMPLIANCE?') }}</th>
                                <th style="background: #09325d;color: #fff;">{{ __('REMARKS') }}</th>
                                <th style="background: #09325d;color: #fff;">{{ __('COMPLIANCE?') }}</th>
                                <th style="background: #09325d;color: #fff;">{{ __('REMARKS') }}</th>
                                <th style="background: #09325d;color: #fff;">{{ __('COMPLIANCE?') }}</th>
                                <th style="background: #09325d;color: #fff;">{{ __('REMARKS') }}</th>
                                <th style="background: #09325d;color: #fff;">{{ __('COMPLIANCE?') }}</th>
                                <th style="background: #09325d;color: #fff;">{{ __('REMARKS') }}</th>
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
    $.ajaxSetup({
        headers: {
            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
        }
    });
    let table = $('#dataTable1').DataTable({
        autoWidth: false,
        processing: true,
        serverSide: true,
        searching: false,
        pageLength: 10,
        dom: "<'row'<'col-sm-12'f>>" +
             "<'row'<'col-sm-3'l><'col-sm-9'p>>" +
             "<'row'<'col-sm-12'tr>>" +
             "<'row'<'col-sm-12'p>>",

        ajax: {
            url: "{{ route('returnedApplicationsReport.getList') }}",
            type: "POST",
            data: function(d) {
                d.status = $('#status').val();
                d.fromdate = $('#fromdate').val();
                d.todate = $('#todate').val();
                d.q = $('#q').val();
            }
        },
        columns: [
            { data: 'no', orderable: false, width: "20px" },
            { data: 'SecurityNo', width: "200px" },
            { data: 'BusinessName', width: "120px", render: renderRemarks },
            { data: 'evaluator_name', width: "120px", render: renderRemarks },
            { data: 'Representative', width: "120px", render: renderRemarks },
            { data: 'busn_type_is_compliance', width: "120px" },
            makeRemarkColumn('busn_type_remarks'),
            { data: 'busn_name_is_compliance', width: "120px" },
            makeRemarkColumn('busn_name_remarks'),
            { data: 'busn_trade_is_compliance', width: "120px" },
            makeRemarkColumn('busn_trade_remarks'),
            { data: 'busn_category_is_compliance', width: "120px" },
            makeRemarkColumn('busn_category_remarks'),
            { data: 'busn_regno_is_compliance', width: "120px" },
            makeRemarkColumn('busn_regno_remarks'),
            { data: 'tin_is_compliance', width: "120px" },
            makeRemarkColumn('tin_remarks'),
            { data: 'url_is_compliance', width: "120px" },
            makeRemarkColumn('url_remarks'),
            { data: 'authrep_name_is_compliance', width: "120px" },
            makeRemarkColumn('authrep_name_remarks'),
            { data: 'authrep_mobile_is_compliance', width: "120px" },
            makeRemarkColumn('authrep_mobile_remarks'),
            { data: 'authrep_email_is_compliance', width: "120px" },
            makeRemarkColumn('authrep_email_remarks'),
            { data: 'authrep_govtid_is_compliance', width: "120px" },
            makeRemarkColumn('authrep_govtid_remarks'),
            { data: 'authrep_govtid_doc_is_compliance', width: "120px" },
            makeRemarkColumn('authrep_govtid_doc_remarks'),
            { data: 'authrep_govtid_expiry_is_compliance', width: "120px" },
            makeRemarkColumn('authrep_govtid_expiry_remarks'),
            { data: 'add_comp_is_compliance', width: "120px" },
            makeRemarkColumn('add_comp_remarks'),
            { data: 'add_barangay_is_compliance', width: "120px" },
            makeRemarkColumn('add_barangay_remarks'),
            { data: 'add_muncity_is_compliance', width: "120px" },
            makeRemarkColumn('add_muncity_remarks'),
            { data: 'add_province_is_compliance', width: "120px" },
            makeRemarkColumn('add_province_remarks'),
            { data: 'add_region_is_compliance', width: "120px" },
            makeRemarkColumn('add_region_remarks'),
            { data: 'doc_busnreg_is_compliance', width: "120px" },
            makeRemarkColumn('doc_busnreg_remarks'),
            { data: 'doc_bir_is_compliance', width: "120px" },
            makeRemarkColumn('doc_bir_remarks'),
            { data: 'doc_irm_is_compliance', width: "120px" },
            makeRemarkColumn('doc_irm_remarks'),
            { data: 'doc_bmbe_is_compliance', width: "120px" },
            makeRemarkColumn('doc_bmbe_remarks'),
            { data: 'asset_category_is_compliance', width: "120px" },
            makeRemarkColumn('asset_category_remarks'),
            { data: 'asset_valuation_is_compliance', width: "120px" },
            makeRemarkColumn('asset_valuation_remarks'),
            { data: 'doc_addpermit_is_compliance', width: "120px" },
            makeRemarkColumn('doc_addpermit_remarks'),
            { data: 'Remarks', width: "200px", render: renderRemarks }
        ],
        drawCallback: function() {
            bindReadMoreToggle();
        }
    });
}
function renderRemarks(data) {
    if (!data) return '';
    if (data.length > 25) {
        const shortText = data.substr(0, 25) + '...';
        return `
            <div class="text-container" style="white-space: normal; word-break: break-word;">
                <span class="short-text">${shortText}</span>
                <span class="full-text" style="display:none;">${data}</span>
                <a href="#" class="toggle-text" style="color:blue;">Read more</a>
            </div>`;
    }
    return `<div style="white-space: normal; word-break: break-word;">${data}</div>`;
}
function makeRemarkColumn(fieldName) {
    return {
        data: fieldName,
        render: renderRemarks,
        width: "120px"
    };
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
    const fromdate = $('#fromdate').val();
    const todate = $('#todate').val();
    const q = $('#q').val();

    try {
        const exportUrl = `{{ route('returnedApplicationsReport.exportAll') }}?fromdate=${fromdate}&todate=${todate}&q=${q}`;
        const response = await fetch(exportUrl);
        const result = await response.json();

        const workbook = new ExcelJS.Workbook();
        const ws = workbook.addWorksheet("Returned Applications");
        function safeMerge(range) {
            try { ws.mergeCells(range); } catch (e) {}
        }

        // === ROW 1 ===
        safeMerge('A1:A3'); ws.getCell('A1').value = 'No.';
        safeMerge('B1:B3'); ws.getCell('B1').value = 'SECURITY NO.';
        safeMerge('C1:C3'); ws.getCell('C1').value = 'BUSINESS NAME';
        safeMerge('D1:D3'); ws.getCell('D1').value = 'EVALUATOR';
        safeMerge('E1:E3'); ws.getCell('E1').value = 'REPRESENTATIVE';

        safeMerge('F1:Q1'); 
        ws.getCell('F1').value = 'BUSINESS INFORMATION';
        safeMerge('R1:S2'); 
        ws.getCell('R1').value = 'BUSINESS URL';
        safeMerge('T1:AE1'); 
        ws.getCell('T1').value = 'AUTHORIZED REPRESENTATIVE';
        safeMerge('AF1:AO1'); 
        ws.getCell('AF1').value = 'ADDRESS INFORMATION';
        safeMerge('AP1:BA1'); 
        ws.getCell('AP1').value = 'ATTACHMENTS';
        safeMerge('BB1:BC2'); 
        ws.getCell('BB1').value = 'ADDITIONAL PERMITS';
        safeMerge('BD1:BD3'); 
        ws.getCell('BD1').value = 'REMARKS'

        // === ROW 2 headers ===
        const row2Labels = [
            'BUSINESS TYPE', 'BUSINESS NAME', 'TRADE NAME', 'BUSINESS CATEGORY', 
            'REGISTRATION NO.', 'TIN',
            '',
            'NAME', 'MOBILE NO.', 'EMAIL', 'GOV ID', 'GOV ID ATTACHMENT', 'GOV ID EXPIRY',
            'COMPLETE ADDRESS', 'BARANGAY', 'MUNICIPALITY | CITY', 'PROVINCE', 'REGION',
            'BUSINESS REGISTRATION', 'BIR 2303', 'IRM', 'BMBE', 'BUSINESS CATEGORY(ASSET SIZE)', 'TOTAL ASSET VALUATION',
            ''
        ];
        let row2Values = ['','','','','']; 
        row2Labels.forEach(label => row2Values.push(label, '')); 
        ws.getRow(2).values = row2Values;

        // === ROW 2 ===
            const row2 = ws.getRow(2);
            let col = 6; 
            row2Labels.forEach(label => {
                row2.getCell(col).value = label;
                safeMerge(`${row2.getCell(col).address}:${row2.getCell(col+1).address}`);
                col += 2;
            });
            for (let i = 1; i <= 5; i++) row2.getCell(i).value = '';
            row2.height = 25;

            // === ROW 3 ===
            const row3 = ws.getRow(3);

            col = 6;
            row2Labels.forEach(() => {
                row3.getCell(col).value = 'COMPLIANCE?';
                row3.getCell(col+1).value = 'REMARKS';
                col += 2;
            });
            row3.height = 25;

        // === STYLE HEADERS ===
        [1,2,3].forEach(r => {
            ws.getRow(r).eachCell(cell => {
                cell.alignment = { vertical: 'middle', horizontal: 'center', wrapText: true };
                cell.font = { bold: true, color: { argb: 'FFFFFF' } };
                cell.border = {
                    top: { style: 'thin' },
                    left: { style: 'thin' },
                    bottom: { style: 'thin' },
                    right: { style: 'thin' }
                };
                cell.fill = {
                    type: 'pattern',
                    pattern: 'solid',
                    fgColor: { argb: (r === 1 || r === 3) ? '09325D' : '4E73DF' }
                };
            });
            ws.getRow(r).height = 25;
        });
        result.data.forEach((row, i) => {
            const dataRow = [
                i+1,
                row.SecurityNo || ' ',
                row.BusinessName || ' ',
                row.evaluator_name || ' ',
                row.Representative || ' ',

                // Business Info
                row.busn_type_is_compliance == 1? 'Yes':'No', row.busn_type_remarks || ' ',
                row.busn_name_is_compliance == 1? 'Yes':'No', row.busn_name_remarks || ' ',
                row.busn_trade_is_compliance == 1? 'Yes':'No', row.busn_trade_remarks || ' ',
                row.busn_category_is_compliance == 1? 'Yes':'No', row.busn_category_remarks || ' ',
                row.busn_regno_is_compliance == 1? 'Yes':'No', row.busn_regno_remarks || ' ',
                row.tin_is_compliance == 1? 'Yes':'No', row.tin_remarks || ' ',

                // Business URL
                row.url_is_compliance == 1? 'Yes':'No', row.url_remarks || ' ',

                // Authorized Rep
                row.authrep_name_is_compliance == 1? 'Yes':'No', row.authrep_name_remarks || ' ',
                row.authrep_mobile_is_compliance == 1? 'Yes':'No', row.authrep_mobile_remarks || ' ',
                row.authrep_email_is_compliance == 1? 'Yes':'No', row.authrep_email_remarks || ' ',
                row.authrep_govtid_is_compliance == 1? 'Yes':'No', row.authrep_govtid_remarks || ' ',
                row.authrep_govtid_doc_is_compliance == 1? 'Yes':'No', row.authrep_govtid_doc_remarks || ' ',
                row.authrep_govtid_expiry_is_compliance == 1? 'Yes':'No', row.authrep_govtid_expiry_remarks || ' ',

                // Address
                row.add_comp_is_compliance == 1? 'Yes':'No', row.add_comp_remarks || ' ',
                row.add_barangay_is_compliance == 1? 'Yes':'No', row.add_barangay_remarks || ' ',
                row.add_muncity_is_compliance == 1? 'Yes':'No', row.add_muncity_remarks || ' ',
                row.add_province_is_compliance == 1? 'Yes':'No', row.add_province_remarks || ' ',
                row.add_region_is_compliance == 1? 'Yes':'No', row.add_region_remarks || ' ',

                // Attachments
                row.doc_busnreg_is_compliance == 1? 'Yes':'No', row.doc_busnreg_remarks || ' ',
                row.doc_bir_is_compliance == 1? 'Yes':'No', row.doc_bir_remarks || ' ',
                row.doc_irm_is_compliance == 1? 'Yes':'No', row.doc_irm_remarks || ' ',
                row.doc_bmbe_is_compliance == 1? 'Yes':'No', row.doc_bmbe_remarks || ' ',
                row.asset_category_is_compliance == 1? 'Yes':'No', row.asset_category_remarks || ' ',
                row.asset_valuation_is_compliance == 1? 'Yes':'No', row.asset_valuation_remarks || ' ',

                // Additional Permits
                row.doc_addpermit_is_compliance == 1? 'Yes':'No', row.doc_addpermit_remarks || ' ',
                row.Remarks || ''
            ];

            const newRow = ws.addRow(dataRow);
            newRow.eachCell(cell => {
                cell.border = {
                    top: { style: 'thin' },
                    left: { style: 'thin' },
                    bottom: { style: 'thin' },
                    right: { style: 'thin' }
                };
                cell.alignment = { vertical: 'middle', horizontal: 'center', wrapText: true };
            });
        });

        // === ADJUST COLUMN WIDTHS ===
        const widths = [5,25,20,20,20];
        for (let i = 6; i <= ws.columnCount; i++) widths.push(15);
        ws.columns.forEach((col, i) => col.width = widths[i] || 15);

        // === EXPORT TO FILE ===
        const buf = await workbook.xlsx.writeBuffer();
        const blob = new Blob([buf], { type: "application/vnd.openxmlformats-officedocument.spreadsheetml.sheet" });
        const url = URL.createObjectURL(blob);
        const a = document.createElement("a");
        a.href = url;
        a.download = "Returned-Applications.xlsx";
        a.click();
        URL.revokeObjectURL(url);

    } catch (error) {
        console.error("Excel export failed:", error);
        Swal.fire("Error", "Failed to export Excel file.", "error");
    }
}


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

</script>

