@extends('layouts.app')
@include('layouts.layout')
@section('content')
<script src="https://cdnjs.cloudflare.com/ajax/libs/exceljs/4.3.0/exceljs.min.js"></script>
<div class="d-sm-flex align-items-center justify-content-between mb-4">
    <h1 class="h3 mb-0 text-gray-800">{{ __('Evaluator KPI') }}</h1>

    <a href="#" onclick="loadDataForExcelSheet()" id="exportExcelData" class="btn btn-sm btn-primary action-item">
        <i class="fa fa-file-excel-o"></i> Export Excel
    </a>

</div>
<div class="row d-flex align-items-center justify-content-end" style="padding-bottom: 20px;">
            <div class="col-md-3">
                <div class="form-group" id="parrent_reg_no">
                    <label for="user_id_filter" class="form-label">{{ __('Evaluator') }}</label>
                    <span class="validate-err"></span>
                    <div class="form-icon-user">
                        <select name="user_id_filter" id="user_id_filter" class="form-control select3" required style="width:100%;">
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
                                        <th>{{ __('Evaluator') }}</th>
                                        <th>{{ __('Date') }}</th>
                                        <th>{{ __('Approved') }}</th>
                                        <th>{{ __('Returned') }}</th>
                                        <th>{{ __('Disapproved') }}</th>
                                        <th>{{ __('On-Hold') }}</th>
                                        <th>{{ __('Acrhived') }}</th>
                                        <th>{{ __('Re-Activated') }}</th>
                                        <th>{{ __('Action') }}</th>
                                    </tr>
                                </thead>
                                <tbody>
                                
                                </tbody>
            </table>
        </div>
    </div>
</div>

<div class="modal fade" id="evaluatorModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-xl">
        <div class="modal-content">
            <div class="modal-header" style="background:#09325d;">
                <h5 class="modal-title text-white">View Evaluator KPI</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>

            <div class="modal-body">
                <table class="table table-bordered">
                    <thead>
                        <tr>
                            <th>No.</th>
                            <th>Security No.</th>
                            <th>Business Name</th>
                            <th>Process</th>
                            <th>Date Processed</th>
                        </tr>
                    </thead>
                    <tbody id="evaluatorDataBody">
                        <tr>
                            <td colspan="5" class="text-center">Loading...</td>
                        </tr>
                    </tbody>
                </table>
            </div>

        </div>
    </div>
</div>

@endsection
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script>
$(document).ready(function() {
    select3Ajax("user_id_filter","parrent_reg_no","userAjaxList");
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
            url: "{{ route('EvaluatorKpi.getList') }}",
            data: function(d) {
                d.user_id_filter = $('#user_id_filter').val();
                d.fromdate = $('#fromdate').val();
                d.todate = $('#todate').val();
                d.q = $('#q').val();
            }
        },
        pageLength: 10,
        columns: [
            { data: 'no', orderable: false },
            { data: 'Evaluator' },
            { data: 'Date' },
            { data: 'Approved' },
            { data: 'Returned' },
            { data: 'Disapproved' },
            { data: 'On-Hold' },
            { data: 'acrhived' },
            { data: 'Re-Activated' },
            { data: 'action' }
        ]
    });
}
async function loadDataForExcelSheet() {
    const fee_id = $('#fees_id_filter').val();
    const fromdate = $('#fromdate').val();
    const todate = $('#todate').val();
    const q = $('#q').val();

    try {
        const exportUrl = `{{ route('EvaluatorKpi.exportAll') }}?fee_id=${fee_id}&fromdate=${fromdate}&todate=${todate}&q=${q}`;
        const response = await fetch(exportUrl);
        const result = await response.json();

        const workbook = new ExcelJS.Workbook();
        const worksheet = workbook.addWorksheet("Evaluator Kpi Report");
        const headerRow = worksheet.addRow([
            "No.", "Evaluator", "Date", "Approved",
            "Returned", "Disapproved", "On-Hold", "Acrhived", "Re-Activated"
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
                row.Evaluator,
                row.LastDate,
                row.Approved,
                row.Returned,
                row.Disapproved,
                row['On-Hold'],        
                row['Archived'],   
                row['Re-Activated']
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
        a.download = "Evaluator_KIP_Report.xlsx";
        a.click();
        window.URL.revokeObjectURL(url);
    } catch (error) {
        console.error("Export failed:", error);
        alert("Failed to export data.");
    }
}
$(document).on("click", ".viewEvaluatorBtn", function (e) {
    e.preventDefault();
    let id = $(this).data("id");

    $("#evaluatorModal").modal("show");
    $("#evaluatorDataBody").html(
        '<tr><td colspan="5" class="text-center">Loading...</td></tr>'
    );

    let url = "{{ route('EvaluatorKpi.getEvaluatorBusinessList', ':id') }}";
    url = url.replace(':id', id);

    $.ajax({
        url: url,
        type: "GET",
        success: function (res) {
            $("#evaluatorDataBody").html(res.html);
        }
    });
});


</script>

