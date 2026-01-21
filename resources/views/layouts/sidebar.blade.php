<nav class="navbar align-items-start sidebar sidebar-dark accordion bg-gradient-primary p-0 navbar-dark"
    style="width: 192px;background: rgba(78,115,223,0);">
    <div class="container-fluid d-flex flex-column p-0">
        <a class="navbar-brand custom-navbar-brand d-flex justify-content-center align-items-center sidebar-brand m-0"
            href="{{ route('dashboard') }}">
            <img class="custom-navbar-logo" src="{{ asset('assets/img/DTI-BP-white.png') }}" alt="Logo">
        </a>

        <hr class="sidebar-divider my-0">
        <ul class="navbar-nav text-light" id="accordionSidebar" style="margin-top: 0px;">
            <!-- <li class="custom-nav-item nav-item">
                <a class="custom-nav-link nav-link d-flex align-items-center gap-2"
                    href="{{ config('app.portal_url') }}">
                    <i class="custom-icon fa fa-home"></i>
                    <span class="custom-span">Home</span>
                </a>
            </li> -->
            <li class="custom-nav-item nav-item">
                <a class="custom-nav-link nav-link d-flex align-items-center gap-2"
                href="#">
                    <i class="custom-icon fa fa-home"></i>
                    <span class="custom-span">Home</span>
                </a>
                <!-- <a class="custom-nav-link nav-link d-flex align-items-center gap-2"
                href="{{ route('sso.redirect.to.app2') }}" target="_blank">
                    <i class="custom-icon fa fa-home"></i>
                    <span class="custom-span">Home</span>
                </a> -->
            </li>

            <li class="custom-nav-item nav-item">
                <a class="custom-nav-link nav-link d-flex align-items-center gap-2 {{ request()->routeIs('dashboard') ? 'active' : '' }}"
                    href="{{ route('dashboard') }}">
                    <i class="custom-icon fa fa-tachometer-alt"></i>
                    <span class="custom-span">Dashboard</span>
                </a>
            </li>

            @if (Auth::check() && in_array(Auth::user()->role, [1]))
                {{-- <li
                    class="custom-nav-item nav-item {{ request()->routeIs('business.create') || request()->routeIs('business.auto_store') ? 'd-none' : '' }}">
                    <button data-bs-toggle="modal" data-bs-target="#modal-1" type="button"
                        class="nav-link custom-nav-link d-flex align-items-center gap-2 w-100 {{ request()->routeIs('business.auto_store') ? 'active' : '' }}"
                        style="background: none; border: none;">
                        <i class="custom-icon fa fa-file-alt"></i>
                        <span class="custom-span">New Registration</span>
                    </button>
                </li> --}}

                <li class="custom-nav-item nav-item">
                    <a data-bs-toggle="modal" data-bs-target="#modal-1"
                        class="custom-nav-link nav-link d-flex align-items-center gap-2 {{ request()->routeIs('business.auto_store') ? 'active' : '' }}"
                        href="{{ route('business.auto_store') }}">
                        <i class="custom-icon fa fa-file-alt"></i>
                        <span class="custom-span">New Registration</span>
                    </a>
                </li>

                <li class="custom-nav-item nav-item">
                    <a class="custom-nav-link nav-link d-flex align-items-center gap-2 {{ request()->routeIs('business.index') ? 'active' : '' }}"
                        href="{{ route('business.index') }}">
                        <i class="custom-icon fa fa-list"></i>
                        <span class="custom-span">My Application</span>
                    </a>
                </li>
            @endif


            @if (Auth::check() && in_array(Auth::user()->role, [2]))
            @php
                $isApplicationsActive = request()->routeIs('business.*');
               $adminUser = DB::table('user_admins')
                ->select('id', 'is_admin')
                ->where('user_id',Auth::user()->id)
                ->where('is_admin',1)
                ->count();
            
                @endphp
            @if ($adminUser)
                <li class="nav-item custom-nav-item">
                    <a class="nav-link custom-nav-link d-flex justify-content-between align-items-center gap-2 {{ $isApplicationsActive ? '' : 'collapsed' }}"
                        data-bs-toggle="collapse" href="#applicationsSubMenu" role="button"
                        aria-expanded="{{ $isApplicationsActive ? 'true' : 'false' }}" aria-controls="applicationsSubMenu">
                        <i class="custom-icon fa fa-book"></i>
                        <span class="custom-span">Applications</span>
                        <i class="fas fa-caret-down toggle-arrow ms-auto {{ $isApplicationsActive ? 'rotate' : '' }}"></i>
                    </a>
                    <div class="collapse {{ $isApplicationsActive ? 'show' : '' }}" id="applicationsSubMenu">
                        <ul class="nav flex-column ms-3">
                            <li class="nav-item">
                            <a class="custom-nav-link nav-link {{ request()->routeIs('business.index') ? 'active' : '' }}"
                            href="{{ route('business.index') }}">
                            Manage Pending
                            </a>
                            </li>
                        </ul>
                    </div>
                </li>
                @endif
                <li class="custom-nav-item nav-item">
                    <a class="custom-nav-link nav-link d-flex align-items-center gap-2 {{ request()->routeIs('business.mytasklist') ? 'active' : '' }}"
                        href="{{ route('business.mytasklist') }}">
                        <i class="custom-icon fa fa-building"></i>
                        <span class="custom-span">My TaskList</span>
                    </a>
                </li>

                @php
                    $isMasterActive = request()->routeIs('requirement.*') || request()->routeIs('ApplicationStatusCannedMessage.*')
                     || request()->routeIs('scheduleFees.*') || request()->routeIs('businessCategory.*') || request()->routeIs('feesDescription.*') || request()->routeIs('onlineplatforms.*');
                @endphp
                @if ($adminUser)
                <li class="nav-item custom-nav-item">
                    <a class="nav-link custom-nav-link d-flex justify-content-between align-items-center gap-2 {{ $isMasterActive ? '' : 'collapsed' }}"
                        data-bs-toggle="collapse" href="#masterSubMenu" role="button"
                        aria-expanded="{{ $isMasterActive ? 'true' : 'false' }}" aria-controls="masterSubMenu">
                        <i class="custom-icon fa fa-book"></i>
                        <span class="custom-span">Master Data</span>
                        <i class="fas fa-caret-down toggle-arrow ms-auto {{ $isMasterActive ? 'rotate' : '' }}"></i>
                    </a>

                    <div class="collapse {{ $isMasterActive ? 'show' : '' }}" id="masterSubMenu">
                        <ul class="nav flex-column ms-3">
                            <li class="nav-item">
                            <a class="custom-nav-link nav-link {{ request()->routeIs('requirement.index') ? 'active' : '' }}"
                            href="{{ route('requirement.index') }}">
                                Requirements<span class="desc">(Authorized Representative)</span>
                            </a>
                            </li>
                            <li class="nav-item">
                            <a class="custom-nav-link nav-link {{ request()->routeIs('ApplicationStatusCannedMessage.index') ? 'active' : '' }}"
                            href="{{ route('ApplicationStatusCannedMessage.index') }}">
                            Application Status<span class="desc">(Canned Message)</span>
                            </a>
                            </li>
                            <li class="nav-item">
                                <a class="custom-nav-link nav-link {{ request()->routeIs('scheduleFees.index') ? 'active' : '' }}"
                                    href="{{ route('scheduleFees.index') }}">
                                    Schedule of Fees
                                </a>
                            </li>
                            <li class="nav-item">
                                <a class="custom-nav-link nav-link {{ request()->routeIs('businessCategory.index') ? 'active' : '' }}"
                                    href="{{ route('businessCategory.index') }}">
                                    Business Category
                                </a>
                            </li>
                            <li class="nav-item">
                                <a class="custom-nav-link nav-link {{ request()->routeIs('feesDescription.index') ? 'active' : '' }}"
                                    href="{{ route('feesDescription.index') }}">
                                    Fees Description
                                </a>
                            </li>
                              <li class="nav-item">
                                <a class="custom-nav-link nav-link {{ request()->routeIs('onlineplatforms.index') ? 'active' : '' }}"
                                    href="{{ route('onlineplatforms.index') }}">
                                   Online Platforms
                                </a>
                            </li>
                        </ul>
                    </div>
                </li>
                @endif
                @php
                    $isMasterActive = request()->routeIs('user.*') || $isMasterActive = request()->routeIs('audittrail.*') || $isMasterActive = request()->routeIs('evaluator.*') || $isMasterActive = request()->routeIs('CustomerProfile.*');
                @endphp
                @if ($adminUser)
                <li class="nav-item custom-nav-item">
                    <a class="nav-link custom-nav-link d-flex justify-content-between align-items-center gap-2 {{ $isMasterActive ? '' : 'collapsed' }}"
                        data-bs-toggle="collapse" href="#userSubMenu" role="button"
                        aria-expanded="{{ $isMasterActive ? 'true' : 'false' }}" aria-controls="userSubMenu">
                        <i class="custom-icon fas fa-user-shield"></i>
                        <span class="custom-span">User</span>
                        <i class="fas fa-caret-down toggle-arrow ms-auto {{ $isMasterActive ? 'rotate' : '' }}"></i>
                    </a>

                    <div class="collapse {{ $isMasterActive ? 'show' : '' }}" id="userSubMenu">
                        <ul class="nav flex-column">
                            <li class="nav-item">
                                <a class="custom-nav-link nav-link {{ request()->routeIs('user.index') ? 'active' : '' }}"
                                    href="{{ route('user.index') }}">
                                    Manage
                                </a>
                            </li>
                        </ul>
                    </div>
                    <div class="collapse {{ $isMasterActive ? 'show' : '' }}" id="userSubMenu">
                        <ul class="nav flex-column">
                            <li class="nav-item">
                                <a class="custom-nav-link nav-link {{ request()->routeIs('audittrail.index') ? 'active' : '' }}"
                                    href="{{ route('audittrail.index') }}">
                                    Audit Trail<span class="desc">(System Logs)</span>
                                </a>
                            </li>
                        </ul>
                    </div>
                    <div class="collapse {{ $isMasterActive ? 'show' : '' }}" id="userSubMenu">
                        <ul class="nav flex-column">
                            <li class="nav-item">
                                <a class="custom-nav-link nav-link {{ request()->routeIs('evaluator.index') ? 'active' : '' }}"
                                    href="{{ route('evaluator.index') }}">
                                    Evaluator<span class="desc">(and Admins)</span>
                                </a>
                            </li>
                        </ul>
                    </div>
                    <div class="collapse {{ $isMasterActive ? 'show' : '' }}" id="userSubMenu">
                        <ul class="nav flex-column">
                            <li class="nav-item">
                                <a class="custom-nav-link nav-link {{ request()->routeIs('CustomerProfile.index') ? 'active' : '' }}"
                                    href="{{ route('CustomerProfile.index') }}">
                                   Customers<span class="desc">(Profile)</span>
                                </a>
                            </li>
                        </ul>
                    </div>
                </li>
                @endif
                @php
                    $isMasterActive = request()->routeIs('region.*') || $isMasterActive = request()->routeIs('provinces.*') || $isMasterActive = request()->routeIs('barangay.*') || $isMasterActive = request()->routeIs('municipality.*');
                @endphp
                @if ($adminUser)
                <li class="nav-item custom-nav-item">
                    <a class="nav-link custom-nav-link d-flex justify-content-between align-items-center gap-2 {{ $isMasterActive ? '' : 'collapsed' }}"
                        data-bs-toggle="collapse" href="#locationSubMenu" role="button"
                        aria-expanded="{{ $isMasterActive ? 'true' : 'false' }}" aria-controls="locationSubMenu">
                        <i class="custom-icon fa fa-map-marker"></i>
                        <span class="custom-span">Location</span>
                        <i class="fas fa-caret-down toggle-arrow ms-auto {{ $isMasterActive ? 'rotate' : '' }}"></i>
                    </a>
                    
                    <div class="collapse {{ $isMasterActive ? 'show' : '' }}" id="locationSubMenu">
                        <ul class="nav flex-column ms-3">
                            <li class="nav-item">
                                <a class="custom-nav-link nav-link {{ request()->routeIs('barangay.index') ? 'active' : '' }}"
                                    href="{{ route('barangay.index') }}">
                                    Barangay
                                </a>
                            </li>
                           
                        </ul>
                    </div>
                    <div class="collapse {{ $isMasterActive ? 'show' : '' }}" id="locationSubMenu">
                        <ul class="nav flex-column ms-3">
                            <li class="nav-item">
                                <a class="custom-nav-link nav-link {{ request()->routeIs('municipality.index') ? 'active' : '' }}"
                                    href="{{ route('municipality.index') }}">
                                    Municipality | City
                                </a>
                            </li>
                           
                        </ul>
                    </div>
                    <div class="collapse {{ $isMasterActive ? 'show' : '' }}" id="locationSubMenu">
                        <ul class="nav flex-column ms-3">
                            <li class="nav-item">
                                <a class="custom-nav-link nav-link {{ request()->routeIs('provinces.index') ? 'active' : '' }}"
                                    href="{{ route('provinces.index') }}">
                                    Provinces
                                </a>
                            </li>
                           
                        </ul>
                    </div>
                    <div class="collapse {{ $isMasterActive ? 'show' : '' }}" id="locationSubMenu">
                        <ul class="nav flex-column ms-3">
                            <li class="nav-item">
                                <a class="custom-nav-link nav-link {{ request()->routeIs('region.index') ? 'active' : '' }}"
                                    href="{{ route('region.index') }}">
                                    Region
                                </a>
                            </li>
                           
                        </ul>
                    </div>
                    
                    
                    
                </li>
                @endif
                @php
                    $isMasterActive = request()->routeIs('department.*') || request()->routeIs('documents.*');
                @endphp

                @if ($adminUser)
                <li class="nav-item custom-nav-item">
                    <a class="nav-link custom-nav-link d-flex justify-content-between align-items-center gap-2 {{ $isMasterActive ? '' : 'collapsed' }}"
                        data-bs-toggle="collapse" href="#securitySubMenu" role="button"
                        aria-expanded="{{ $isMasterActive ? 'true' : 'false' }}" aria-controls="securitySubMenu">
                        <i class="custom-icon fa fa-key"></i>
                        <span class="custom-span">Security</span>
                        <i class="fas fa-caret-down toggle-arrow ms-auto {{ $isMasterActive ? 'rotate' : '' }}"></i>
                    </a>
                    
                    <div class="collapse {{ $isMasterActive ? 'show' : '' }}" id="securitySubMenu">
                        <ul class="nav flex-column ms-3">
                            <li class="nav-item">
                                <a class="custom-nav-link nav-link {{ request()->routeIs('department.index') ? 'active' : '' }}"
                                    href="{{ route('department.index') }}">
                                    Department
                                </a>
                            </li>
                           
                        </ul>
                    </div>
                    <div class="collapse {{ $isMasterActive ? 'show' : '' }}" id="securitySubMenu">
                        <ul class="nav flex-column ms-3">
                            <li class="nav-item">
                                <a class="custom-nav-link nav-link {{ request()->routeIs('documents.index') ? 'active' : '' }}"
                                    href="{{ route('documents.index') }}">
                                    Documents
                                </a>
                            </li>
                           
                        </ul>
                    </div>
                    
                </li>
                @endif
                @php
                    $isMasterActive = request()->routeIs('MenuGroup.*') || request()->routeIs('MenuModule.*') || request()->routeIs('updatepaymet.*') || request()->routeIs('refund.*');
                @endphp

                @if ($adminUser)
                <li class="nav-item custom-nav-item">
                    <a class="nav-link custom-nav-link d-flex justify-content-between align-items-center gap-2 {{ $isMasterActive ? '' : 'collapsed' }}"
                        data-bs-toggle="collapse" href="#SettingSubMenu" role="button"
                        aria-expanded="{{ $isMasterActive ? 'true' : 'false' }}" aria-controls="SettingSubMenu">
                        <i class="custom-icon fa fa-cog"></i>
                        <span class="custom-span">Setting</span>
                        <i class="fas fa-caret-down toggle-arrow ms-auto {{ $isMasterActive ? 'rotate' : '' }}"></i>
                    </a>
                    
                    <div class="collapse {{ $isMasterActive ? 'show' : '' }}" id="SettingSubMenu">
                        <ul class="nav flex-column ms-3">
                            <li class="nav-item">
                                <a class="custom-nav-link nav-link {{ request()->routeIs('MenuGroup.index') ? 'active' : '' }}"
                                    href="{{ route('MenuGroup.index') }}">
                                    Menu Group
                                </a>
                            </li>
                           
                        </ul>
                    </div>
                    <div class="collapse {{ $isMasterActive ? 'show' : '' }}" id="SettingSubMenu">
                        <ul class="nav flex-column ms-3">
                            <li class="nav-item">
                                <a class="custom-nav-link nav-link {{ request()->routeIs('MenuModule.index') ? 'active' : '' }}"
                                    href="{{ route('MenuModule.index') }}">
                                    Menu Module
                                </a>
                            </li>
                           
                        </ul>
                    </div>
                    <div class="collapse {{ $isMasterActive ? 'show' : '' }}" id="SettingSubMenu">
                        <ul class="nav flex-column ms-3">
                            <li class="nav-item">
                                <a class="custom-nav-link nav-link {{ request()->routeIs('cron-job.index') ? 'active' : '' }}"
                                    href="{{ route('cron-job.index') }}">
                                    Cron-Job
                                </a>
                            </li>
                           
                        </ul>
                    </div>
                     <div class="collapse {{ $isMasterActive ? 'show' : '' }}" id="SettingSubMenu">
                        <ul class="nav flex-column ms-3">
                            <li class="nav-item">
                                <a class="custom-nav-link nav-link {{ request()->routeIs('updatepaymet.index') ? 'active' : '' }}"
                                    href="{{ route('updatepaymet.index') }}">
                                    Payment Concerns
                                </a>
                            </li>
                           
                        </ul>
                    </div>
                    <div class="collapse {{ $isMasterActive ? 'show' : '' }}" id="SettingSubMenu">
                        <ul class="nav flex-column ms-3">
                            <li class="nav-item">
                                <a class="custom-nav-link nav-link {{ request()->routeIs('refund.index') ? 'active' : '' }}"
                                    href="{{ route('refund.index') }}">
                                    Payment Refund
                                </a>
                            </li>
                           
                        </ul>
                    </div>
                    
                </li>
                @endif
                @php
                    $isMasterActive = request()->routeIs('Income.*') || request()->routeIs('dailyreport.*') || request()->routeIs('archivedApplicationsReport.*') || request()->routeIs('returnedApplicationsReport.*') || request()->routeIs('EvaluatorKpi.*');
                @endphp

                
                <li class="nav-item custom-nav-item">
                    <a class="nav-link custom-nav-link d-flex justify-content-between align-items-center gap-2 {{ $isMasterActive ? '' : 'collapsed' }}"
                        data-bs-toggle="collapse" href="#ReportSubMenu" role="button"
                        aria-expanded="{{ $isMasterActive ? 'true' : 'false' }}" aria-controls="ReportSubMenu">
                        <i class="custom-icon fa fa-file"></i>
                        <span class="custom-span">Report</span>
                        <i class="fas fa-caret-down toggle-arrow ms-auto {{ $isMasterActive ? 'rotate' : '' }}"></i>
                    </a>
                    
                    <div class="collapse {{ $isMasterActive ? 'show' : '' }}" id="ReportSubMenu">
                        <ul class="nav flex-column ms-3">
                            <li class="nav-item">
                                <a class="custom-nav-link nav-link {{ request()->routeIs('Income.index') ? 'active' : '' }}"
                                    href="{{ route('Income.index') }}">
                                    Income
                                </a>
                            </li>
                            <li class="nav-item">
                                <a class="custom-nav-link nav-link {{ request()->routeIs('dailyreport.index') ? 'active' : '' }}"
                                    href="{{ route('dailyreport.index') }}">
                                    Daily Report
                                </a>
                            </li>
                            <li class="nav-item">
                                <a class="custom-nav-link nav-link {{ request()->routeIs('returnedApplicationsReport.index') ? 'active' : '' }}"
                                    href="{{ route('returnedApplicationsReport.index') }}">
                                    Returned Applications
                                </a>
                            </li>
                            <li class="nav-item">
                                <a class="custom-nav-link nav-link {{ request()->routeIs('archivedApplicationsReport.index') ? 'active' : '' }}"
                                    href="{{ route('archivedApplicationsReport.index') }}">
                                    Archived Applications
                                </a>
                            </li>
                            <li class="nav-item">
                                <a class="custom-nav-link nav-link {{ request()->routeIs('EvaluatorKpi.index') ? 'active' : '' }}"
                                    href="{{ route('EvaluatorKpi.index') }}">
                                    Evaluator KPI
                                </a>
                            </li>
                           
                        </ul>
                    </div>
                    
                    
                </li>
            @endif

        </ul>
    </div>
</nav>



<style>
    .sidebar .nav-item:last-child {
        margin-bottom: 0rem !important;
    }
    .nav-item span.desc {
        clear: both;
        font-size: 8px;
        display: block !important;
    }
    .custom-navbar-brand {
        height: 64px !important;
        width: 224.8px;
        background-color: #09325d;
    }

    .custom-navbar-logo {
        width: 120px;
        height: 64px;
        margin-left: -60px;
        border-radius: 8px;
    }


    .custom-nav-item {
        background: rgb(255, 255, 255);
    }

    .custom-nav-link {
        display: flex;
        align-items: center;
        justify-content: flex-start;
        padding: 8px 12px;
        border-radius: 4px;
        width: 100%;
        gap: 8px;
        /* Optional: add spacing between icon and text */
    }

    /* Active link = Blue background */
    .custom-nav-link.active {
        background-color: #09325d;
    }

    /* Active icon and text = White */
    .custom-nav-link.active .custom-icon,
    .custom-nav-link.active .custom-span {
        color: white !important;
    }

    /* Inactive icon and text = Black */
    .custom-icon,
    .custom-span,
    .toggle-arrow {
        white-space: nowrap;
        color: #09325D !important;
    }


    .toggle-arrow {
        transition: transform 0.3s ease;
        font-size: 14px;
        color: #09325D !important;
        margin-left: 70px;
    }

    .nav-link[aria-expanded="true"] .toggle-arrow {
        transform: rotate(180deg);
    }

    /* Dropdown userSubMenu inside sidebar */
    #userSubMenu {
        background-color: #ffffff;
        padding-left: 10px;
        width: 50px;
        max-width: 50px;
        position: relative;
        z-index: 1;
    }

    #userSubMenu .nav-link {
        padding: 8px 12px;
        font-size: 12px;
        font-family: sans-serif;
        white-space: nowrap;
        color: #09325D;
        width: 200px;
        max-width: 200px;
        height: 40px;
        display: block !important;
        align-items: center;
        white-space: normal;
        /* Allow text wrapping */
        word-break: break-word;
        /* Break long words if needed */
        line-height: 1.3;
    }

    #userSubMenu .nav-link.active {
        background-color: #09325d;
        color: white !important;
        border-radius: 4px;
        width: 200px;
        max-width: 200px;
        font-size: 12px;
        font-family: sans-serif;
        height: 40px;
        display: flex;
        align-items: center;
    }

    /* Dropdown masterSubMenu inside sidebar */
    #masterSubMenu {
        background-color: #ffffff;
        padding-left: 10px;
        width: 50px;
        max-width: 50px;
        position: relative;
        z-index: 1;
    }

    #masterSubMenu .nav-link {
        padding: 8px 12px;
        font-size: 12px;
        font-family: sans-serif;
        white-space: nowrap;
        color: #09325D;
        width: 200px;
        max-width: 200px;
        height: 40px;
        display: block !important;
        align-items: center;
        white-space: normal;
        /* Allow text wrapping */
        word-break: break-word;
        /* Break long words if needed */
        line-height: 1.3;
    }
    .ms-3
    {
        margin-left: 0rem !important;
    }
    #masterSubMenu .nav-link.active {
        background-color: #09325d;
        color: white !important;
        border-radius: 4px;
        width: 200px;
        max-width: 200px;
        font-size: 12px;
        font-family: sans-serif;
        height: 40px;
        /* Same as above */
        display: block !important;
        align-items: center;
    }
    /* Dropdown locationSubMenu inside sidebar */
    #locationSubMenu {
        background-color: #ffffff;
        padding-left: 10px;
        width: 50px;
        max-width: 50px;
        position: relative;
        z-index: 1;
    }

    #locationSubMenu .nav-link {
        padding: 8px 12px;
        font-size: 12px;
        font-family: sans-serif;
        white-space: nowrap;
        color: #09325D;
        width: 200px;
        max-width: 200px;
        height: 40px;
        display: flex;
        align-items: center;
        white-space: normal;
        /* Allow text wrapping */
        word-break: break-word;
        /* Break long words if needed */
        line-height: 1.3;
    }

    #locationSubMenu .nav-link.active {
        background-color: #09325d;
        color: white !important;
        border-radius: 4px;
        width: 200px;
        max-width: 200px;
        font-size: 12px;
        font-family: sans-serif;
        height: 40px;
        /* Same as above */
        display: flex;
        align-items: center;
    }
    /* Dropdown locationSubMenu inside sidebar */
    #securitySubMenu {
        background-color: #ffffff;
        padding-left: 10px;
        width: 50px;
        max-width: 50px;
        position: relative;
        z-index: 1;
    }

    #securitySubMenu .nav-link {
        padding: 8px 12px;
        font-size: 12px;
        font-family: sans-serif;
        white-space: nowrap;
        color: #09325D;
        width: 200px;
        max-width: 200px;
        height: 40px;
        display: flex;
        align-items: center;
        white-space: normal;
        /* Allow text wrapping */
        word-break: break-word;
        /* Break long words if needed */
        line-height: 1.3;
    }

    #securitySubMenu .nav-link.active {
        background-color: #09325d;
        color: white !important;
        border-radius: 4px;
        width: 200px;
        max-width: 200px;
        font-size: 12px;
        font-family: sans-serif;
        height: 40px;
        /* Same as above */
        display: flex;
        align-items: center;
    }
    /* Dropdown SettingSubMenu inside sidebar */
    #SettingSubMenu {
        background-color: #ffffff;
        padding-left: 10px;
        width: 50px;
        max-width: 50px;
        position: relative;
        z-index: 1;
    }

    #SettingSubMenu .nav-link {
        padding: 8px 12px;
        font-size: 12px;
        font-family: sans-serif;
        white-space: nowrap;
        color: #09325D;
        width: 200px;
        max-width: 200px;
        height: 40px;
        display: flex;
        align-items: center;
        white-space: normal;
        /* Allow text wrapping */
        word-break: break-word;
        /* Break long words if needed */
        line-height: 1.3;
    }

    #SettingSubMenu .nav-link.active {
        background-color: #09325d;
        color: white !important;
        border-radius: 4px;
        width: 200px;
        max-width: 200px;
        font-size: 12px;
        font-family: sans-serif;
        height: 40px;
        /* Same as above */
        display: flex;
        align-items: center;
    }
    /* Dropdown ReportSubMenu inside sidebar */
    #ReportSubMenu {
        background-color: #ffffff;
        padding-left: 10px;
        width: 50px;
        max-width: 50px;
        position: relative;
        z-index: 1;
    }

    #ReportSubMenu .nav-link {
        padding: 8px 12px;
        font-size: 12px;
        font-family: sans-serif;
        white-space: nowrap;
        color: #09325D;
        width: 200px;
        max-width: 200px;
        height: 40px;
        display: flex;
        align-items: center;
        white-space: normal;
        /* Allow text wrapping */
        word-break: break-word;
        /* Break long words if needed */
        line-height: 1.3;
    }

    #ReportSubMenu .nav-link.active {
        background-color: #09325d;
        color: white !important;
        border-radius: 4px;
        width: 200px;
        max-width: 200px;
        font-size: 12px;
        font-family: sans-serif;
        height: 40px;
        /* Same as above */
        display: flex;
        align-items: center;
    }
    /* Dropdown applicationsSubMenu inside sidebar */
    #applicationsSubMenu {
        background-color: #ffffff;
        padding-left: 10px;
        width: 50px;
        max-width: 50px;
        position: relative;
        z-index: 1;
    }

    #applicationsSubMenu .nav-link {
        padding: 8px 12px;
        font-size: 12px;
        font-family: sans-serif;
        white-space: nowrap;
        color: #09325D;
        width: 200px;
        max-width: 200px;
        height: 40px;
        display: flex;
        align-items: center;
        white-space: normal;
        /* Allow text wrapping */
        word-break: break-word;
        /* Break long words if needed */
        line-height: 1.3;
    }

    #applicationsSubMenu .nav-link.active {
        background-color: #09325d;
        color: white !important;
        border-radius: 4px;
        width: 200px;
        max-width: 200px;
        font-size: 12px;
        font-family: sans-serif;
        height: 40px;
        /* Same as above */
        display: flex;
        align-items: center;
    }
</style>
