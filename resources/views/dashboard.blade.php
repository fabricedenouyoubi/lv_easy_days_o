@extends('layouts.app')

@section('title', 'Dashboard - Feuille de Temps')
@section('page-title', 'Dashboard')

@section('content')
<div class="row">
    <div class="col-xxl-9">
        <div class="row">
            <!-- Statistiques -->
            <div class="col-xl-4 col-lg-6">
                <div class="card">
                    <div class="card-body">
                        <div class="d-flex align-items-center">
                            <div class="flex-shrink-0 me-3">
                                <div class="avatar">
                                    <div class="avatar-title rounded bg-primary bg-gradient">
                                        <i data-eva="clock-outline" class="fill-white"></i>
                                    </div>
                                </div>
                            </div>
                            <div class="flex-grow-1">
                                <p class="text-muted mb-1">Heures ce mois</p>
                                <h4 class="mb-0">152.5h</h4>
                            </div>
                            <div class="flex-shrink-0 align-self-end ms-2">
                                <div class="badge rounded-pill font-size-13 bg-success-subtle text-success">+ 8.2%</div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-xl-4 col-lg-6">
                <div class="card">
                    <div class="card-body">
                        <div class="d-flex align-items-center">
                            <div class="flex-shrink-0 me-3">
                                <div class="avatar">
                                    <div class="avatar-title rounded bg-warning bg-gradient">
                                        <i data-eva="file-text-outline" class="fill-white"></i>
                                    </div>
                                </div>
                            </div>
                            <div class="flex-grow-1">
                                <p class="text-muted mb-1">Feuilles en attente</p>
                                <h4 class="mb-0">3</h4>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-xl-4">
                <div class="card">
                    <div class="card-body">
                        <div class="d-flex align-items-center">
                            <div class="flex-shrink-0 me-3">
                                <div class="avatar">
                                    <div class="avatar-title rounded bg-success bg-gradient">
                                        <i data-eva="checkmark-circle-outline" class="fill-white"></i>
                                    </div>
                                </div>
                            </div>
                            <div class="flex-grow-1">
                                <p class="text-muted mb-1">Feuilles validées</p>
                                <h4 class="mb-0">28</h4>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Placeholder pour le contenu principal -->
        <div class="card">
            <div class="card-body">
                <h5 class="card-title mb-3">Feuilles de temps récentes</h5>
                <div class="text-center py-5">
                    <i data-eva="clock-outline" data-eva-height="48" data-eva-width="48" class="fill-muted"></i>
                    <p class="text-muted mt-3">Les composants Livewire seront intégrés ici</p>
                </div>
            </div>
        </div>
    </div>

    <!-- Sidebar droite -->
    <div class="col-xxl-3">
        <div class="card">
            <div class="card-body">
                <h5 class="card-title mb-3">Activité récente</h5>
                <div class="text-center py-4">
                    <p class="text-muted">Composant Livewire à venir...</p>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection