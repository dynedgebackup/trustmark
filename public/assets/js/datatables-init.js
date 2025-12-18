$(document).ready(function() {
    // Don't automatically initialize all tables with id="dataTable"
    // Let individual pages decide when to initialize
    
    // Instead, initialize only tables specifically marked for auto-init
    $('.datatable-auto-init').DataTable({
        responsive: true,
        paging: true
    });
});