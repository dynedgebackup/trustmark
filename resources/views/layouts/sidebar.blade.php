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

            

            @if (Auth::check() && in_array(Auth::user()->role, [1]))
            <li class="custom-nav-item nav-item">
                <a class="custom-nav-link nav-link d-flex align-items-center gap-2 {{ request()->routeIs('dashboard') ? 'active' : '' }}"
                    href="{{ route('dashboard') }}">
                    <i class="custom-icon fa fa-tachometer-alt"></i>
                    <span class="custom-span">Dashboard</span>
                </a>
            </li>
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
                $isAdmin = DB::table('user_admins')
                    ->where('user_id', Auth::id())
                    ->where('is_admin', 1)
                    ->exists();

                $menus = DB::table('menu_permissions as mp')
                    ->join('menu_groups as mg', 'mg.id', '=', 'mp.menu_group_id')
                    ->leftJoin('menu_modules as mm', 'mm.id', '=', 'mp.menu_module_id')
                    ->where('mp.user_id', Auth::id())
                    ->select(
                        'mg.id as group_id',
                        'mg.name as group_name',
                        'mg.slug as group_slug',
                        'mg.icon as group_icon',
                        'mm.name as module_name',
                        'mm.slug as module_slug',
                        'mm.icon as module_icon',
                        'mm.description'
                    )
                    ->orderBy('mg.id')
                    ->get()
                    ->groupBy('group_id');
            @endphp

           
            @foreach ($menus as $groupId => $items)
            @php
                $group = $items->first();
                $modules = $items->whereNotNull('module_slug');
                $hasModules = $modules->count() > 0;

                // active logic
                $isGroupActive = $hasModules
                    ? $modules->contains(fn ($m) => request()->is($m->module_slug.'*'))
                    : request()->is($group->group_slug.'*');
            @endphp
            @if($hasModules)

                <li class="nav-item custom-nav-item">
                    <a class="nav-link custom-nav-link d-flex justify-content-between align-items-center gap-2
                        {{ $isGroupActive ? '' : 'collapsed' }}"
                        data-bs-toggle="collapse"
                        href="#submenu-{{ $groupId }}"
                        aria-expanded="{{ $isGroupActive ? 'true' : 'false' }}"
                    >
                        <i class="custom-icon {{ $group->group_icon }}"></i>
                        <span class="custom-span">{{ $group->group_name }}</span>
                        <i class="fas fa-caret-down toggle-arrow ms-auto {{ $isGroupActive ? 'rotate' : '' }}"></i>
                    </a>

                    <div class="collapse userSubMenu  {{ $isGroupActive ? 'show' : '' }}"  id="submenu-{{ $groupId }}" style="margin: 1px;">
                        <ul class="nav flex-column ms-3">

                            @foreach ($modules as $module)
                                @php
                                    $isActive = request()->is($module->module_slug)
                                            || request()->is($module->module_slug.'/*');
                                @endphp

                                <li class="nav-item" style="padding-left: 2px;">
                                    <a class="nav-link custom-nav-link {{ $isActive ? 'active' : '' }}"
                                    href="{{ url($module->module_slug) }}">
                                        <!-- <i class="{{ $module->module_icon }} me-2"></i> -->
                                        {{ $module->module_name }}
                                        @if($module->description)
                                            <span class="desc">({{ $module->description }})</span>
                                        @endif
                                    </a>
                                </li>
                            @endforeach

                        </ul>
                    </div>
                </li>
                @else

                    <li class="custom-nav-item nav-item">
                        <a class="custom-nav-link nav-link d-flex align-items-center gap-2
                            {{ $isGroupActive ? 'active' : '' }}"
                            href="{{ url($group->group_slug) }}">
                            <i class="custom-icon {{ $group->group_icon }}"></i>
                            <span class="custom-span">{{ $group->group_name }}</span>
                        </a>
                    </li>

                @endif
            @endforeach
            @endif

        </ul>
    </div>
</nav>



<style>
    .sidebar-dark .nav-item .nav-link {
        color: #000 !important;
        padding: 10px;
        font-size: 12px;
    }
    .sidebar .nav-item .nav-link.active {
        font-weight: 700;
        color: #fff !important;
        padding: 10px
    }
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