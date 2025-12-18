/**
 * Safe DataTable initialization that handles empty tables properly
 */
$(document).ready(function() {
    // Find all tables that should be initialized as DataTables
    $('table#dataTable').each(function() {
        const table = $(this);
        
        try {
            // Check if already initialized
            if ($.fn.dataTable.isDataTable(table)) {
                table.DataTable().destroy();
            }
            
            // Fix colspan in empty message rows
            const emptyRow = table.find('tbody tr td[colspan]');
            if (emptyRow.length > 0) {
                const headerColumns = table.find('thead th').length;
                // Update colspan to match number of header columns
                emptyRow.attr('colspan', headerColumns);
                
                // Skip DataTable initialization for empty tables
                console.log("Empty table detected, skipping DataTables initialization");
                return;
            }
            
            // Initialize DataTable for tables with data
            table.DataTable({
                responsive: true,
                paging: true
            });
        } catch (e) {
            console.error("DataTable initialization error for table:", table.attr('id'), e);
        }
    });
});