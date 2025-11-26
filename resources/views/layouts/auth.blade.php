<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1" />
    <title>@yield('title', 'Register')</title>
    {{-- <link rel="stylesheet" href="{{ asset('css/custom-register.css') }}"> --}}
    <link rel="stylesheet" href="{{ asset('assets/css/custom.css') }}?v=1.0">
    <link rel="stylesheet" href="{{ asset('assets/bootstrap/css/bootstrap.min.css') }}">
    <link rel="stylesheet" href="https://fonts.googleapis.com/css?family=Nunito:200,200i,300,300i,400,400i,600,600i,700,700i,800,800i,900,900i&amp;display=swap">
    <link rel="stylesheet" href="assets/fonts/fontawesome-all.min.css">
    <link rel="stylesheet" href="assets/css/Drag--Drop-Upload-Form.css">
    <link rel="stylesheet" href="assets/css/Drag-Drop-File-Input-Upload.css">
    <link rel="stylesheet" href="assets/css/Login-Form-Basic-icons.css">
    <link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />

    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>

    <script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>

    <style>
        .sticky-footer {
            position: fixed;
            bottom: 0;
            padding: 0.5rem 0 !important;
            width: 100%;
        }
    </style>
</head>

<body>

    <div class="register-container">
        @yield('content')
    </div>

    <footer class="bg-white sticky-footer d-none">
        <div class="container my-auto">
            <div class="text-center my-auto copyright" style="margin-top: 0px;"><span><br>Copyright © Trix
                    2025<br><br></span></div>
        </div>
    </footer>

    <script src="assets/bootstrap/js/bootstrap.min.js"></script>
    <script src="assets/js/bs-init.js"></script>
    <script src="assets/js/account-type-toogle.js"></script>
    <script src="assets/js/Multi-step-form-script.js"></script>
    <script src="assets/js/theme.js"></script>
</body>

</html>
