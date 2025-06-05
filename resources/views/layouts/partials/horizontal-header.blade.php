<header class="ishorizontal-topbar">
    <div class="navbar-header">
        <div class="d-flex">
            <!-- LOGO -->
            <div class="navbar-brand-box">
                <a href="{{ route('dashboard') }}" class="logo logo-dark">
                    <span class="logo-sm">
                        <img src="{{ asset('assets/borex/images/logo-dark-sm.png') }}" alt="" height="22">
                    </span>
                    <span class="logo-lg">
                        <img src="{{ asset('assets/borex/images/logo-dark.png') }}" alt="" height="22">
                    </span>
                </a>

                <a href="{{ route('dashboard') }}" class="logo logo-light">
                    <span class="logo-sm">
                        <img src="{{ asset('assets/borex/images/logo-light-sm.png') }}" alt="" height="22">
                    </span>
                    <span class="logo-lg">
                        <img src="{{ asset('assets/borex/images/logo-light.png') }}" alt="" height="22">
                    </span>
                </a>
            </div>

            <button type="button" class="btn btn-sm px-3 font-size-16 d-lg-none header-item" data-bs-toggle="collapse" data-bs-target="#topnav-menu-content">
                <i class="fa fa-fw fa-bars"></i>
            </button>

            <div class="d-none d-sm-block ms-2 align-self-center">
                <h4 class="page-title">@yield('page-title', 'Dashboard')</h4>
            </div>
        </div>

        <div class="d-flex">
            <!-- Search -->
            @include('layouts.partials.header.search')
            
            <!-- Language -->
            @include('layouts.partials.header.language')
            
            <!-- Apps -->
            @include('layouts.partials.header.apps')
            
            <!-- Notifications -->
            @include('layouts.partials.header.notifications')
            
            <!-- Settings -->
            @include('layouts.partials.header.settings')
            
            <!-- User Profile -->
            @include('layouts.partials.header.user-profile')
        </div>
    </div>
    
    <!-- Navigation horizontale -->
    <div class="topnav">
        <div class="container-fluid">
            <nav class="navbar navbar-light navbar-expand-lg topnav-menu">
                <div class="collapse navbar-collapse" id="topnav-menu-content">
                    <ul class="navbar-nav">
                        <li class="nav-item">
                            <a class="nav-link" href="{{ route('dashboard') }}">
                                <i class="icon nav-icon" data-eva="grid-outline"></i>
                                <span>Dashboard</span>
                            </a>
                        </li>

                        <li class="nav-item dropdown">
                            <a class="nav-link dropdown-toggle arrow-none" href="#" id="topnav-timesheet" role="button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                <i class="icon nav-icon" data-eva="clock-outline"></i>
                                <span>Feuilles de Temps</span>
                                <div class="arrow-down"></div>
                            </a>
                            <div class="dropdown-menu" aria-labelledby="topnav-timesheet">
                                <a href="{{ route('timesheet.create') }}" class="dropdown-item">Nouvelle Feuille</a>
                                <a href="{{ route('timesheet.index') }}" class="dropdown-item">Mes Feuilles</a>
                                <a href="{{ route('timesheet.pending') }}" class="dropdown-item">En Attente</a>
                            </div>
                        </li>

                        @can('validate-timesheets')
                        <li class="nav-item dropdown">
                            <a class="nav-link dropdown-toggle arrow-none" href="#" id="topnav-validation" role="button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                <i class="icon nav-icon" data-eva="checkmark-circle-outline"></i>
                                <span>Validation</span>
                                <div class="arrow-down"></div>
                            </a>
                            <div class="dropdown-menu" aria-labelledby="topnav-validation">
                                <a href="{{ route('timesheet.validation.pending') }}" class="dropdown-item">À Valider</a>
                                <a href="{{ route('timesheet.validation.history') }}" class="dropdown-item">Historique</a>
                            </div>
                        </li>
                        @endcan

                        <li class="nav-item dropdown">
                            <a class="nav-link dropdown-toggle arrow-none" href="#" id="topnav-absences" role="button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                <i class="icon nav-icon" data-eva="calendar-outline"></i>
                                <span>Absences</span>
                                <div class="arrow-down"></div>
                            </a>
                            <div class="dropdown-menu" aria-labelledby="topnav-absences">
                                <a href="{{ route('absence.request') }}" class="dropdown-item">Demander</a>
                                <a href="{{ route('absence.my') }}" class="dropdown-item">Mes Absences</a>
                            </div>
                        </li>
                    </ul>
                </div>
            </nav>
        </div>
    </div>
</header>