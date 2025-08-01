@extends('layouts.app')

@section('title', 'Détails Année Financière - Budget')
@section('page-title', 'Détails de l\'Année Financière')

@section('content')
    <div class="container-fluid">
        <!-- Breadcrumb -->
        <x-breadcrumb :items="[
            ['label' => 'Années financières', 'url' => route('budget.annees-financieres')],
            ['label' => 'Détails'],
        ]" />

        <!-- Information de l'année financière -->
        <x-table-card title="Détails de l'année financière" icon="mdi mdi-calendar-outline me-2">
            <div class="row align-items-center">
                <div class="col-md-3">
                    <div class="mb-3 mb-md-0">
                        <label class="form-label text-muted small">Date de début</label>
                        <p class="fw-medium mb-0">{{ $anneeFinanciere->debut->format('d/m/Y') }}</p>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="mb-3 mb-md-0">
                        <label class="form-label text-muted small">Date de fin</label>
                        <p class="fw-medium mb-0">{{ $anneeFinanciere->fin->format('d/m/Y') }}</p>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="mb-3 mb-md-0">
                        <label class="form-label text-muted small">Statut</label>
                        <p class="fw-medium mb-0">
                            @if ($anneeFinanciere->actif)
                                <span class="badge bg-success d-inline-flex align-items-center px-3 py-2">
                                    <i class="mdi mdi-check-circle me-1"></i>
                                    Actif
                                </span>
                            @else
                                <span class="badge bg-danger d-inline-flex align-items-center px-3 py-2">
                                    <i class="mdi mdi-close-circle me-1"></i>
                                    Inactif
                                </span>
                            @endif
                        </p>
                    </div>
                </div>
                @if ($anneeFinanciere->actif)
                    <div class="col-md-3">
                        <div class="text-md-end mt-3 mt-md-0">
                            <button class="btn btn-danger btn-sm rounded-3 px-3 d-inline-flex align-items-center"
                                onclick="alert('Pas encore disppnible')">
                                <i class="mdi mdi-close-circle-outline me-1"></i>
                                Clôturer l'année
                            </button>
                        </div>
                    </div>
                @endif
            </div>
        </x-table-card>

        <!-- Section feuilles de temps -->
        <div class="row">
            <div class="col-12">
                <livewire:rhfeuilledetempsconfig::feuille-de-temps-details :anneeFinanciereId="$anneeFinanciere->id" />
            </div>
        </div>
    </div>

    @push('scripts')
        <script>
            // Initialiser les tooltips et dropdowns
            document.addEventListener('livewire:navigated', function() {
                // Tooltips
                var tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'));
                var tooltipList = tooltipTriggerList.map(function(tooltipTriggerEl) {
                    return new bootstrap.Tooltip(tooltipTriggerEl);
                });

                // Dropdowns
                var dropdownElementList = [].slice.call(document.querySelectorAll('.dropdown-toggle'));
                var dropdownList = dropdownElementList.map(function(dropdownToggleEl) {
                    return new bootstrap.Dropdown(dropdownToggleEl);
                });
            });
        </script>
    @endpush
@endsection
