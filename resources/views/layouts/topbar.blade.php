
<nav class="navbar navbar-expand shadow mb-4 topbar" style="background: #09325d;height: 64px;">
    <div class="container-fluid">
        <button class="btn btn-link d-md-none rounded-circle me-3" id="sidebarToggleTop" type="button">
            <i class="fas fa-bars"></i>
        </button>

        <ul class="navbar-nav ms-auto">
            {{-- Notification --}}
            {{-- <li class="nav-item dropdown no-arrow mx-1">
                <a class="dropdown-toggle nav-link" data-bs-toggle="dropdown" href="#">
                    <span class="badge bg-danger badge-counter">3+</span>
                    <i class="fas fa-bell fa-fw text-white"></i>
                </a>
                <div class="dropdown-menu dropdown-menu-end dropdown-list">
                    <h6 class="dropdown-header">Notification</h6>
                    <a class="dropdown-item d-flex align-items-center" href="#">
                        <div class="me-3">
                            <div class="bg-primary icon-circle"><i class="fas fa-file-alt text-white"></i></div>
                        </div>
                        <div><span class="small text-gray-500">December 12, 2019</span>
                            <p>Appointment has been approved</p>
                        </div>
                    </a>
                </div>
            </li> --}}

            {{-- User Info --}}
            <li class="nav-item dropdown no-arrow">
                <a class="dropdown-toggle nav-link" data-bs-toggle="dropdown" href="#">
                    <span class="d-none d-lg-inline text-white small me-2">
                        @auth
                            {{ Auth::user()->name }}
                        @endauth
                    </span>
                    <img class="border rounded-circle img-profile" src="{{ asset('assets/img/avatars/user.png') }}"
                        style="width: 32px;height: 32px;">
                </a>
                <div class="dropdown-menu dropdown-menu-end shadow">
                    <a class="dropdown-item" href="{{ route('profile.view') }}">
                        <i class="fas fa-user fa-sm fa-fw me-2 text-gray-400"></i> Profile
                    </a>
                    <a href="{{ route('logout') }}" class="dropdown-item">
                        <i class="fas fa-sign-out-alt fa-sm fa-fw me-2 text-gray-400"></i> Logout
                    </a>
                </div>
            </li>
        </ul>
    </div>
</nav>



<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script>
    function confirmLogout() {
        Swal.fire({
            title: 'Are you sure?',
            text: "You will be logged out.",
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#3085d6',
            cancelButtonColor: '#d33',
            confirmButtonText: 'Yes, logout'
        }).then((result) => {
            if (result.isConfirmed) {
                document.getElementById('logout-form').submit();
            }
        });
    }
</script>
