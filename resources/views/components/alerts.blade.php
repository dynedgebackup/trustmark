@if(session('success'))
<script>
    document.addEventListener('DOMContentLoaded', function () {
        Swal.fire({
            icon: 'success',
            title: 'Success',
            text: "{{ session('success') }}",
            showConfirmButton: true,
            confirmButtonColor: '#28a745',
        });
    });
</script>
@endif

@if(session('error'))
<script>
    document.addEventListener('DOMContentLoaded', function () {
        Swal.fire({
            icon: 'error',
            title: 'Error',
            text: "{{ session('error') }}",
            showConfirmButton: true,
            confirmButtonColor: '#dc3545',
        });
    });
</script>
@endif
