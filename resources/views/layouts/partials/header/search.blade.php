<div class="dropdown">
    <button type="button" class="btn header-item"
        data-bs-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
        <i class="icon-sm" data-eva="search-outline"></i>
    </button>
    <div class="dropdown-menu dropdown-menu-end dropdown-menu-md p-0">
        <form class="p-2" method="GET" action="{{ route('search') }}">
            <div class="search-box">
                <div class="position-relative">
                    <input type="text" class="form-control bg-light border-0" 
                           placeholder="Rechercher..." name="q" 
                           value="{{ request('q') }}">
                    <i class="search-icon" data-eva="search-outline" data-eva-height="26" data-eva-width="26"></i>
                </div>
            </div>
        </form>
    </div>
</div>