<div class="vertical-menu">
    <!-- LOGO -->
    <div class="navbar-brand-box">
        <a href="#" class="logo logo-dark">
            <span class="logo-sm">
                <img src="{{ asset('assets/borex/images/logo-dark-sm.png') }}" alt="" height="22">
            </span>
            <span class="logo-lg">
                <img src="{{ asset('assets/borex/images/logo-dark.png') }}" alt="" height="22">
            </span>
        </a>

        <a href="#" class="logo logo-light">
            <span class="logo-lg">
                <img src="{{ asset('assets/borex/images/logo-light.png') }}" alt="" height="22">
            </span>
            <span class="logo-sm">
                <img src="{{ asset('assets/borex/images/logo-light-sm.png') }}" alt="" height="22">
            </span>
        </a>
    </div>

    <button type="button" class="btn btn-sm px-3 header-item vertical-menu-btn topnav-hamburger">
        <span class="hamburger-icon">
            <span></span>
            <span></span>
            <span></span>
        </span>
    </button>

    <div data-simplebar class="sidebar-menu-scroll">
        <!--- Sidemenu -->
        <div id="sidebar-menu">
            <!-- Left Menu Start -->
            <ul class="metismenu list-unstyled" id="side-menu">

                <!-- DASHBOARDS -->
                <li>
                    <a href="#" onclick="return false;">
                        <i class="icon nav-icon" data-eva="grid-outline"></i>
                        <span class="menu-item" data-key="t-dashboards">Dashboards</span>
                    </a>
                </li>

                <!-- ENTREPRISE -->
                <li class="menu-title" data-key="t-entreprise">ENTREPRISE</li>

                <li>
                    <a href="{{ route('entreprise.presentation') }}">
                        <i class="icon nav-icon" data-eva="monitor-outline"></i>
                        <span class="menu-item" data-key="t-presentation">Présentation</span>
                    </a>
                </li>

                <!-- BUDGET -->
                <li class="menu-title" data-key="t-budget">BUDGET</li>

                <li>
                    <a href="{{ route('budget.annees-financieres') }}">
                        <i class="icon nav-icon" data-eva="calendar-outline"></i>
                        <span class="menu-item" data-key="t-annees-financieres">Années Financières</span>
                    </a>
                </li>

                <li>
                    <a href="{{ route('rh.poste.list') }}">
                        <i class="icon nav-icon" data-eva="people-outline"></i>
                        <span class="menu-item" data-key="t-annees-financieres">Postes</span>
                    </a>
                </li>

                <li>
                    <a href="#" onclick="return false;">
                        <i class="icon nav-icon" data-eva="people-outline"></i>
                        <span class="menu-item" data-key="t-employes">Employés</span>
                    </a>
                </li>

                <!-- FEUILLES DE TEMPS -->
                <li class="menu-title" data-key="t-feuilles-temps">FEUILLES DE TEMPS</li>

                <li>
                    <a href="#" onclick="return false;">
                        <i class="icon nav-icon" data-eva="clock-outline"></i>
                        <span class="menu-item" data-key="t-mes-absences">Mes Absences</span>
                    </a>
                </li>

                <li>
                    <a href="#" onclick="return false;">
                        <i class="icon nav-icon" data-eva="clock-outline"></i>
                        <span class="menu-item" data-key="t-heures-regulieres">Heures régulières</span>
                    </a>
                </li>

                <li>
                    <a href="javascript: void(0);" class="has-arrow">
                        <i class="icon nav-icon" data-eva="settings-2-outline"></i>
                        <span class="menu-item" data-key="t-configurations">Configurations</span>
                    </a>
                    <ul class="sub-menu" aria-expanded="false">
                        <li><a href="#" onclick="return false;" data-key="t-feuilles-temps">Feuilles de temps</a>
                        </li>
                        <li><a href="#" onclick="return false;" data-key="t-code-travail">Code de travail</a></li>
                    </ul>
                </li>

                <!-- RAPPORT -->
                <li class="menu-title" data-key="t-rapport">RAPPORT</li>

                <li>
                    <a href="#" onclick="return false;">
                        <i class="icon nav-icon" data-eva="file-remove-outline"></i>
                        <span class="menu-item" data-key="t-feuilles-absentes">Feuilles absentes</span>
                    </a>
                </li>

                <li>
                    <a href="#" onclick="return false;">
                        <i class="icon nav-icon" data-eva="activity-outline"></i>
                        <span class="menu-item" data-key="t-activites">Activités</span>
                    </a>
                </li>

            </ul>
        </div>
        <!-- Sidebar -->

        <div class="p-3 px-4 sidebar-footer">
            <p class="mb-1 main-title">
                <script>
                    document.write(new Date().getFullYear())
                </script> &copy; Easy days Opérations.
            </p>
            <p class="mb-0">GCS Technologie</p>
        </div>
    </div>
</div>
