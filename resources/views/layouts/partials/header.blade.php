<header id="page-topbar" class="isvertical-topbar">
    <div class="navbar-header">
        <div class="d-flex">
            <!-- LOGO -->
            <div class="navbar-brand-box">
                <a href="{{ route('dashboard') }}" class="logo logo-dark">
                    <span class="logo-sm">
                        <img src="{{ asset('assets/borex/images/logo-dark-sm.png') }}" alt="" height="22">
                    </span>
                    <span class="logo-lg">
                        <img src="{{ asset('assets/borex/images/logo-dark-sm.png') }}" alt="" height="22">
                    </span>
                </a>

                <a href="{{ route('dashboard') }}" class="logo logo-light">
                    <span class="logo-lg">
                        <img src="{{ asset('assets/borex/images/logo-light.png') }}" alt="" height="22">
                    </span>
                    <span class="logo-sm">
                        <img src="{{ asset('assets/borex/images/logo-light-sm.png') }}" alt="" height="22">
                    </span>
                </a>
            </div>

            <button type="button" class="btn btn-sm px-3 font-size-16 header-item vertical-menu-btn topnav-hamburger">
                <span class="hamburger-icon open">
                    <span></span>
                    <span></span>
                    <span></span>
                </span>
            </button>

            <div class="d-none d-sm-block ms-3 align-self-center">
                <h4 class="page-title">@yield('page-title', 'Dashboard')</h4>
            </div>
        </div>

        <div class="d-flex">
            
            <!-- Language -->
            @include('layouts.partials.header.language')
            
            <!-- Apps -->
           @include('layouts.partials.header.apps')
            
            <!-- Notifications -->
            @include('layouts.partials.header.notifications')
            
            <!-- User Profile -->
            @include('layouts.partials.header.user-profile')
        </div>
    </div>
</header>