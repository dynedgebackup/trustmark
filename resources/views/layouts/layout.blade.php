<!-- Global Modal -->
<div class="modal fade" id="commonModal" tabindex="-1" role="dialog">
  <div class="modal-dialog modal-lg" role="document">
    <div class="modal-content" style="color:#fff;">
      <div class="modal-header" >
        <h5 class="modal-title" style="font-family: sans-serif; font-size: 18px; color: rgb(0,0,0); font-weight: bold;"></h5> <!-- THIS is what you're targeting -->
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>
      <div class="modal-body body"></div>
    </div>
  </div>
</div>
<style>
    .form-label {
        margin-top: 1rem;
        color: #3a3b45;
        .5rem;
        font-weight: bold;
       font-size: 12px;
    }
    .select3-container--default .select3-results>.select3-results__options {
        max-height: 200px;
        overflow-y: auto;
        color: #000;
        font-size: 12px;
    }
    .table>thead {
        vertical-align: bottom;
        font-size: 12px;
    }
    .table>tbody {
        vertical-align: inherit;
        font-size: 12px;
        /* border-bottom-color: #ccc; */
    }
    
  .dataTables_wrapper {
    clear: both;
    padding-left: 10px;
}
.btn-primary
Specificity: (0,1,0)
 {
    color: #fff;
    background: #09325d !important;
}
.btn-primary {
    color: #fff;
    background: #09325d !important;
}

input:focus, input:not(:placeholder-shown)
Specificity: (0,1,1)
 {
    color: black;
    font-size: 12px !important;
    padding: 9px !important;
}
input, button, select, optgroup, textarea
 {
    font-size: 12px !important;
    padding: 9px !important;
    margin: 0;
    font-family: inherit;
    font-size: inherit;
    line-height: inherit;
}
.table-responsive {
    overflow-x:hidden !important;
}
table.dataTable>thead>tr>th, table.dataTable>thead>tr>td {
      padding: 10px;
      border-bottom: 1px solid rgba(0, 0, 0, 0.3);
      border-top: 1px solid rgba(0, 0, 0, 0.3);
      /* border-left: none !important;
    border-right: none !important; */
}
table.dataTable tbody th, table.dataTable tbody td
 {
    padding: 8px 10px;
    border: 1px solid #eee !important;
    border-left: none !important;
    border-right: none !important;
}
</style>


<!-- CSS -->
<link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />

<!-- JS -->
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
<script>
$(document).on('click', '[data-ajax-popup="true"]', function(e) {
    e.preventDefault();
    var url = $(this).data('url');
    var title = $(this).data('title') || '';

    $('#commonModal .modal-title').text(title); // use ID for better targeting

    $.ajax({
        url: url,
        success: function(result) {
            $('#commonModal .body').html(result);
            $('#commonModal').modal('show');
        },
        error: function(xhr) {
            $('#commonModal .body').html('<div class="alert alert-danger">Failed to load content.</div>');
            $('#commonModal').modal('show');
        }
    });
});

</script>