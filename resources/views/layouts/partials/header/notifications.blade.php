<div class="dropdown d-inline-block">
    <button type="button" class="btn header-item noti-icon" id="page-header-notifications-dropdown-v"
        data-bs-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
        <i class="icon-sm" data-eva="bell-outline"></i>
        @if(auth()->user() && auth()->user()->unreadNotifications->count() > 0)
        <span class="noti-dot bg-danger rounded-pill">{{ auth()->user()->unreadNotifications->count() }}</span>
        @endif
    </button>
    <div class="dropdown-menu dropdown-menu-lg dropdown-menu-end p-0"
        aria-labelledby="page-header-notifications-dropdown-v">
        <div class="p-3">
            <div class="row align-items-center">
                <div class="col">
                    <h5 class="m-0 font-size-15">Notifications</h5>
                </div>
                <div class="col-auto">
                    <a href="#!" class="small fw-semibold text-decoration-underline">Marquer tout comme lu</a>
                </div>
            </div>
        </div>
        <div data-simplebar style="max-height: 250px;">
            @if(auth()->user() && auth()->user()->notifications->count() > 0)
                @foreach(auth()->user()->notifications->take(5) as $notification)
                <a href="#!" class="text-reset notification-item">
                    <div class="d-flex">
                        <div class="flex-shrink-0 me-3">
                            <div class="avatar-sm">
                                <span class="avatar-title bg-primary rounded-circle font-size-16">
                                    <i class="bx bx-bell"></i>
                                </span>
                            </div>
                        </div>
                        <div class="flex-grow-1">
                            <h6 class="mb-1">{{ $notification->data['title'] ?? 'Notification' }}</h6>
                            <div class="font-size-13 text-muted">
                                <p class="mb-1">{{ $notification->data['message'] ?? 'Nouveau message' }}</p>
                                <p class="mb-0"><i class="mdi mdi-clock-outline"></i> <span>{{ $notification->created_at->diffForHumans() }}</span></p>
                            </div>
                        </div>
                    </div>
                </a>
                @endforeach
            @else
            <div class="text-center py-4">
                <i class="icon-sm" data-eva="bell-off-outline"></i>
                <p class="text-muted mt-2">Aucune notification</p>
            </div>
            @endif
        </div>
        <div class="p-2 border-top d-grid">
            <a class="btn btn-sm btn-link font-size-14 btn-block text-center" href="{{ route('notifications.index') }}">
                <i class="uil-arrow-circle-right me-1"></i> <span>Voir plus...</span>
            </a>
        </div>
    </div>
</div>