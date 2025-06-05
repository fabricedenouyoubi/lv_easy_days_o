<div class="dropdown d-inline-block">
    <button type="button" class="btn header-item user text-start d-flex align-items-center" id="page-header-user-dropdown-v"
        data-bs-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
        @if(auth()->user() && auth()->user()->avatar)
            <img class="rounded-circle header-profile-user" src="{{ asset('storage/' . auth()->user()->avatar) }}" alt="Header Avatar">
        @else
            <img class="rounded-circle header-profile-user" src="{{ asset('assets/borex/images/users/avatar-1.jpg') }}" alt="Header Avatar">
        @endif
    </button>
    <div class="dropdown-menu dropdown-menu-end pt-0">
        <div class="p-3 border-bottom">
            <h6 class="mb-0">{{ auth()->user()->name ?? 'Utilisateur' }}</h6>
            <p class="mb-0 font-size-11 text-muted">{{ auth()->user()->email ?? 'email@example.com' }}</p>
        </div>
        <a class="dropdown-item" href="{{ route('profile.show') }}">
            <i class="mdi mdi-account-circle text-muted font-size-16 align-middle me-1"></i> 
            <span class="align-middle">Profil</span>
        </a>
        <a class="dropdown-item" href="{{ route('timesheet.index') }}">
            <i class="mdi mdi-clock-outline text-muted font-size-16 align-middle me-1"></i> 
            <span class="align-middle">Mes Feuilles</span>
        </a>
        <a class="dropdown-item" href="{{ route('help') }}">
            <i class="mdi mdi-lifebuoy text-muted font-size-16 align-middle me-1"></i> 
            <span class="align-middle">Aide</span>
        </a>
        <div class="dropdown-divider"></div>
        @if(auth()->user())
        <a class="dropdown-item" href="#">
            <i class="mdi mdi-wallet text-muted font-size-16 align-middle me-1"></i> 
            <span class="align-middle">Heures ce mois : <b>152.5h</b></span>
        </a>
        @endif
        <a class="dropdown-item d-flex align-items-center" href="{{ route('settings') }}">
            <i class="mdi mdi-cog-outline text-muted font-size-16 align-middle me-1"></i> 
            <span class="align-middle">Paramètres</span>
        </a>
        <form method="POST" action="{{ route('logout') }}">
            @csrf
            <button type="submit" class="dropdown-item">
                <i class="mdi mdi-logout text-muted font-size-16 align-middle me-1"></i> 
                <span class="align-middle">Déconnexion</span>
            </button>
        </form>
    </div>
</div>