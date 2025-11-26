<!DOCTYPE html>
<html data-bs-theme="light" lang="en">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, shrink-to-fit=no">
    <title>Trustmark</title>
    <link rel="stylesheet" href="{{ asset('assets/bootstrap/css/bootstrap.min.css') }}">
    <link rel="stylesheet" href="{{ asset('assets/css/bss-overrides.css') }}">
    <link rel="stylesheet" href="{{ asset('assets/css/Footer-Basic-icons.css') }}">
    <link rel="stylesheet" href="{{ asset('assets/css/custom.css') }}">
</head>

<body>
    @section('navbar')
    <nav class="navbar navbar-expand-lg navbar-white bg-white">
      <div class="container">
        <div class="col">
          <ul class="brand-logo navbar-left gap-2 justify-content-sm-start justify-content-center">
            <li class="nav-item">
              <a class="navbar-brand d-flex gap-3 m-0 p-0" href="https://trustmark.bahayko.app/">
                <img
                  class="navbar-img"
                  src="{{ asset('assets/img/dti-bagong-text.png') }}"
                  alt="DTI Bagong Logo Text"
                />
              </a>
            </li>
          </ul>
        </div>

        <div class="col">
          <ul class="navbar-right gap-2 justify-content-sm-end justify-content-center">
            <li class="nav-item">
                <a target="_blank" href="https://www.dti.gov.ph/good-governance-program/transparency-seal">
                    <img class="navbar-img" src="{{ asset('assets/img/ph-seal.png') }}"/>
                </a>
            </li>
            <li class="nav-item">
                <a target="_blank" href="https://www.foi.gov.ph/requests/dti">
                    <img class="navbar-img" src="{{ asset('assets/img/freedom-ph.png') }}"/>
                </a>
            </li>
            <li class="nav-item">
                <a target="_blank" href="https://dtiwebfiles.s3.ap-southeast-1.amazonaws.com/Data+Privacy/DTI_NPC_RegistrationCert.pdf">
                    <img class="navbar-img" src="{{ asset('assets/img/dpo.png') }}"/>
                </a>
            </li>
          </ul>
        </div>
      </div>
    </nav>
    <div class="d-flex min-vh-100 align-items-center">
        <div class="container">
            <div class="row">
                <div class="col">
                    <p style="text-align: center;font-size: 32px;font-weight: bold;color: rgb(0,74,172);">CHECK
                        APPLICATION STATUS<br> FOR E-COMMERCE PHILIPPINE TRUSTMARK</p>
                </div>
            </div>
            <div class="row">
                <div class="col" style="text-align: center;">
                    <form method="POST" action="{{ route('business.search') }}">
                        @csrf
                        <input class="p-3" type="search" name="trustmark_id"
                            style="font-size: 28px;width: 100%;border-radius: 10px;"
                            placeholder="Search Reference Number" required>
                        <button class="btn btn-primary px-4 mt-3" type="submit"
                            style="font-size: 28px;font-weight: bold;background: rgb(0,74,172);">SEARCH</button>
                    </form>
                    @if(session('error'))
                        <div class="alert alert-danger mt-3">{{ session('error') }}</div>
                    @endif
                </div>
            </div>
            <div class="row">
                <div class="col">
                    <p class="py-4" style="text-align: center;">ENTER YOUR REFERENCE NUMBER TO VIEW REAL-TIME UPDATES
                        <br>ON YOUR APPLICATION FOR E-COMMERCE PHILIPPINE TRUSTMARK.</p>
                </div>
            </div>
        </div>
    </div>
    <footer class="text-center bg-body" data-bs-theme="light">
        <div class="container py-4 py-lg-5">
            <ul class="list-inline"></ul>
            <p class="text-body mb-0">Copyright © Trix 2025</p>
        </div>
    </footer>
    <script src="{{ asset('assets/bootstrap/js/bootstrap.min.js') }}"></script>
</body>

</html>