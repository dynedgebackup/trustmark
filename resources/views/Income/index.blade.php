@extends('layouts.app')
@include('layouts.layout')
@section('content')
<script src="https://cdnjs.cloudflare.com/ajax/libs/exceljs/4.3.0/exceljs.min.js"></script>
<div class="d-sm-flex align-items-center justify-content-between mb-4">
    <h1 class="h3 mb-0 text-gray-800">{{ __('Income') }}</h1>

    <a href="#" onclick="loadDataForExcelSheet()" id="exportExcelData" class="btn btn-sm btn-primary action-item">
        <i class="fa fa-file-excel-o"></i> Export Excel
    </a>

</div>
<div class="row d-flex align-items-center justify-content-end" style="padding-bottom: 20px;">
            <div class="col-md-3">
                <div class="form-group" id="parrent_reg_no">
                    <label for="fees_id_filter" class="form-label">{{ __('Payment Description') }}</label>
                    <span class="validate-err"></span>
                    <div class="form-icon-user">
                        <select name="fees_id_filter" id="fees_id_filter" class="form-control select3" required style="width:100%;">
                            <option value=""></option>
                            
                        </select>
                    </div>
                    <span class="validate-err" id="err_app_code"></span>
                </div>
            </div>
            
            
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
                    <div class="table-responsive table mt-2" id="dataTable-1" role="grid"
                        aria-describedby="dataTable_info">
                        <table id="dataTable1" class="table table-bordered" style="width: 98.5% !important;">
                            <thead>
                                <tr>
                                        <th>{{ __('No.') }}</th>
                                        <th>{{ __('Business Name') }}</th>
                                        <th>{{ __('Security No.') }}</th>
                                        <th>{{ __('Payment Descpription') }}</th>
                                        <th>{{ __('Transaction ID') }}</th>
                                        <th>{{ __('OR Number') }}</th>
                                        <th>{{ __('Amount') }}</th>
                                        <th>{{ __('Payment Channel') }}</th>
                                        <th>{{ __('Date') }}</th>
                                        <th>{{ __('Payment By') }}</th>
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
        processing: true,
        serverSide: true,
        searching: false,
        dom: "<'row'<'col-sm-12'f>>" +"<'row'<'col-sm-3'l><'col-sm-9'p>>" +"<'row'<'col-sm-12'tr>>" +"<'row'<'col-sm-12'p>>",
        ajax: {
            url: "{{ route('Income.getList') }}",
            data: function(d) {
                d.fee_id = $('#fees_id_filter').val();
                d.fromdate = $('#fromdate').val();
                d.todate = $('#todate').val();
                d.q = $('#q').val();
            }
        },
        pageLength: 10,
        columns: [
            { data: 'no', orderable: false },
            { data: 'BusinessName' },
            { data: 'SecurityNo' },
            { data: 'PaymentDescription' },
            { data: 'TransactionID' },
            { data: 'OR_Number' },
            { data: 'Amount' },
            { data: 'payment_channel' },
            { data: 'Date' },
            { data: 'PaymentBy' }
        ]
    });
}
async function loadDataForExcelSheet() {
    const fee_id = $('#fees_id_filter').val();
    const fromdate = $('#fromdate').val();
    const todate = $('#todate').val();
    const q = $('#q').val();

    try {
        const exportUrl = `{{ route('Income.exportAll') }}?fee_id=${fee_id}&fromdate=${fromdate}&todate=${todate}&q=${q}`;
        const response = await fetch(exportUrl);
        const result = await response.json();

        const workbook = new ExcelJS.Workbook();
        const worksheet = workbook.addWorksheet("Income Report");
        const headerRow = worksheet.addRow([
            "No.", "Business Name", "Security No.", "Payment Description",
            "Transaction ID", "OR Number", "Amount", "Payment Channel", "Date", "Payment By"
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
                row.BusinessName,
                row.SecurityNo,
                row.PaymentDescription,
                row.TransactionID,
                row.OR_Number,
                row.Amount,
                row.payment_channel,
                row.Date,
                row.PaymentBy
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
        a.download = "Income_Report.xlsx";
        a.click();
        window.URL.revokeObjectURL(url);
    } catch (error) {
        console.error("Export failed:", error);
        alert("Failed to export data.");
    }
}
</script>

