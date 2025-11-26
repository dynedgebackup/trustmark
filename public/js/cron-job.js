function quickRunCron(cid) {
    const swalWithBootstrapButtons = Swal.mixin({
        customClass: {
            confirmButton: 'btn btn-success',
            cancelButton: 'btn btn-danger'
        },
        buttonsStyling: false
    })
    swalWithBootstrapButtons.fire({
        title: 'Are you sure?',
        text: "Are you sure want to run this cron job?",
        icon: 'warning',
        showCancelButton: true,
        confirmButtonText: 'Yes',
        cancelButtonText: 'No',
        reverseButtons: true
    }).then((result) => {
        if (result.isConfirmed) {
            // showLoader();
            $.ajax({
                url: BASE_URL + "cron-job/quickRunCron",
                type: "POST",
                data: {
                    id: cid,
                    _token: $("#_csrf_token").val(),
                },
                success: function (html) {
                    // hideLoader();
                    Swal.fire({
                        position: 'center',
                        icon: 'success',
                        title: 'Cron Run Successfully.',
                        showConfirmButton: false,
                        timer: 1500
                    });
                    datatablefunction();
                }
            });
        }
    })
}


$(document).on('click', '.quickRun', function () {
    var cid = $(this).attr('cid');
    console.log("Quick Run clicked, cid =", cid);
    quickRunCron(cid);
});
