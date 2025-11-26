@if ($errors->any())
    <div class="alert alert-danger">
        <ul>
            @foreach ($errors->all() as $error)
                <li>{{ $error }}</li>
            @endforeach
        </ul>
    </div>
@endif

@if(session('otp_error'))
    <div class="alert alert-danger alert-dismissible fade show" role="alert">
        {{ session('otp_error') }}
        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
    </div>
@endif


@extends('layouts.auth')

@section('title', 'Forgot Password')
<div id="wrapper">
    <div class="d-flex flex-column" id="content-wrapper">
        <div id="content" style="background: rgba(228,227,232,0.36);">
            <nav class="navbar navbar-expand shadow mb-4 topbar" style="background: #09325d;height: 64px;">
                <div class="container-fluid"><button class="btn btn-link d-md-none rounded-circle me-3"
                        id="sidebarToggleTop" type="button"><i class="fas fa-bars"></i></button><img
                        class="custom-navbar-logo" src="{{ asset('assets/img/DTI-BP-white.png') }}">
                </div>
            </nav>
            <div class="container-fluid">
                <div class="d-flex justify-content-between align-items-center mb-4" style="margin-top: 15px;">
                    <h3 class="text-dark" style="font-family:sans-serif;font-size:20px;margin:0;"><span
                            style="color: rgba(var(--bs-dark-rgb), var(--bs-text-opacity));font-size: 20px;font-weight: bold;">OTP has been sent to {{ $user->email }}</span>
                    </h3>
                </div>
                <form action="{{ route('verify.otp') }}" method="POST" enctype="multipart/form-data"
                    autocomplete="off">
                    @csrf
                    <div class="row" style="margin-bottom: 15px;">
                        <div class="col">
                            <div class="card">
                                <div class="card-header">
                                    <h6 class="text-primary fw-bold m-0" style="font-size: 12px;">Account Password Reset</h6>
                                </div>
                                <div class="card-body">
                                    <input type="hidden" name="email" value="{{ $user->email }}">
                                    <div class="row" style="margin-bottom:10px;">
                                        <div class="col">
                                            <div class="mb-3">
                                                <label class="form-label custom-label">Enter OTP</label>
                                                <input class="form-control custom-input" type="text" name="otp" maxlength="6" pattern="\d{6}" placeholder="6-digit OTP" required>
                                                <div id="otp-error" class="text-danger mt-1" style="font-size: 13px;"></div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="d-flex gap-2" style="margin-bottom: 160px;">
                        <button class="btn btn-primary" type="submit"
                            style="font-family: sans-serif; font-size: 13px;">
                            Verify OTP
                        </button>

                        <a href="{{ route('login') }}" class="btn btn-secondary"
                            style="font-family: sans-serif; font-size: 13px;">
                            Go to Login
                        </a>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>


<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>

<style>
    .custom-navbar-logo {
        width: 120px;
        height: 64px;
        /* margin-left: -60px; */
        border-radius: 8px;
    }
</style>
