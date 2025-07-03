<div class="vertical-menu">
    <!-- LOGO -->
    <div class="navbar-brand-box d-flex justify-content-center align-items-center">
        <a href="#" class="logo logo-dark">
            <span class="logo-sm">
                <img src="{{ asset('assets/borex/images/logo-gcs.png') }}" alt="" height="22">
            </span>
            <span class="logo-lg">
                <img src="{{ asset('assets/borex/images/logo-gcs.png') }}" alt="" height="22">
            </span>
        </a>

        <a href="#" class="logo logo-light">
            <span class="logo-lg">
                <img src="{{ asset('assets/borex/images/logo-gcs.png') }}" alt="" height="22">
            </span>
            <span class="logo-sm">
                <img src="{{ asset('assets/borex/images/logo-gcs.png') }}" alt="" height="22">
            </span>
            <span class="menu-item text-white">ChronoTemps</span>
        </a>
    </div>

    <button type="button" class="btn btn-sm px-3 header-item vertical-menu-btn topnav-hamburger" disabled>
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
                    <a href="{{ route('dashboard') }}">
                        <i class="icon nav-icon" data-eva="grid-outline"></i>
                        <span class="menu-item" data-key="t-dashboards">Dashboards</span>
                    </a>
                </li>

                <!-- ENTREPRISE -->
                @can('Voir Module PRESENTATION')
                    <li class="menu-title" data-key="t-entreprise">ENTREPRISE</li>
                    <li>
                        <a href="{{ route('entreprise.presentation') }}">
                            <i class="icon nav-icon" data-eva="monitor-outline"></i>
                            <span class="menu-item" data-key="t-presentation">Présentation</span>
                        </a>
                    </li>
                @endcan

                <!-- BUDGET -->
                @can('Voir Module ANNEE_FINANCIERE')
                    <li class="menu-title" data-key="t-budget">BUDGET</li>
                    <li>
                        <a href="{{ route('budget.annees-financieres') }}">
                            <i class="icon nav-icon" data-eva="calendar-outline"></i>
                            <span class="menu-item" data-key="t-annees-financieres">Années Financières</span>
                        </a>
                    </li>
                @endcan


                <!-- RH -->
                @can('Voir Module RH')
                    <li class="menu-title" data-key="t-budget">RH</li>
                    @can('Voir Employé')
                        <li>
                            <a href="{{ route('rh-employe.list') }}">
                                <i class="icon nav-icon" data-eva="people-outline"></i>
                                <span class="menu-item" data-key="t-employes">Employés</span>
                            </a>
                        </li>
                    @endcan
                @endcan



                <!-- FEUILLES DE TEMPS -->
                <li class="menu-title" data-key="t-feuilles-temps">FEUILLES DE TEMPS</li>

                <li>
                    <a href="{{ route('absence.list') }}">
                        <i class="icon nav-icon" data-eva="clock-outline"></i>
                        <span class="menu-item" data-key="t-mes-absences">Mes Absences</span>
                    </a>
                </li>

                <li>
                    <a href="{{ route('feuille-temps.list') }}">
                        <i class="icon nav-icon" data-eva="clock-outline"></i>
                        <span class="menu-item" data-key="t-heures-regulieres">Mes feuilles de temps</span>
                    </a>
                </li>

                {{-- Configuration --}}
                @can('Voir Module CONFIGURATION')
                    <li>
                        <a href="javascript: void(0);" class="has-arrow">
                            <i class="icon nav-icon" data-eva="settings-2-outline"></i>
                            <span class="menu-item" data-key="t-configurations">Configurations</span>
                        </a>
                        <ul class="sub-menu" aria-expanded="false">
                            <li><a href="#" onclick="return false;" data-key="t-feuilles-temps">Feuilles de temps</a>
                            </li>
                            <li><a href="{{ route('rhfeuilledetempsconfig.codes-travail.codetravails') }}"
                                    data-key="t-code-travail">Code de travail</a></li>
                            <li><a href="{{ route('rhfeuilledetempsconfig.categories.categories') }}"
                                    data-key="t-categorie">Catégorie code</a></li>
                        </ul>
                    </li>
                @endcan

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

                <!-- Autorisation -->
                @can('Voir Module AUTORISATION')
                    <li class="menu-title" data-key="t-autorisations">Autorisation</li>
                    <li>
                        <a href="javascript: void(0);" class="has-arrow">
                            <i class="mdi mdi-cancel fs-4"></i>
                            <span class="menu-item" data-key="t-gestion_autorisation">Gestion autorisations</span>
                        </a>
                        <ul class="sub-menu" aria-expanded="false">
                            {{-- <li>
                            <a href="{{ route('permission.index') }}"data-key="t-permissions">Permissions</a>
                        </li> --}}
                            @can('Voir Groupes')
                                <li>
                                    <a href="{{ route('group.index') }}"data-key="t-groups">Groupes</a>
                                </li>
                            @endcan
                            @can('Voir Utilisateurs')
                                <li>
                                    <a
                                        href="{{ route('gestion_utilisateur.index') }}"data-key="t-utilisateurs">Utilisateurs</a>
                                </li>
                            @endcan
                        </ul>
                    </li>
                @endcan

                <!-- JOURNALISATION -->
                @can('Voir Journalisation')
                    <li class="menu-title" data-key="t-journalisation">JOURNALISATION</li>
                    <li>
                        <a href="/journalisation">
                            <i class="icon nav-icon" data-eva="file-text-outline"></i>
                            <span class="menu-item" data-key="t-journal">Journalisation</span>
                        </a>
                    </li>
                @endcan

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
