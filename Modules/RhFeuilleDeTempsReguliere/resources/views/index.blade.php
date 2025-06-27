@extends('layouts.app')

@section('title', config('app.name') . ' | Feuilles de temps')
@section('page-title', 'Mes Feuilles de temps')

@section('content')
    <x-breadcrumb :items="[
        ['label' => 'Feuilles de temps'],
        ['label' => 'Liste par employé']
    ]" />

    <div class="row">
        <div class="col-12">
            <livewire:rhfeuilledetempsreguliere::rh-feuille-de-temps-reguliere-list />
        </div>
    </div>
@endsection

@push('scripts')
<script>
    // Scripts spécifiques à la page si nécessaire
    document.addEventListener('DOMContentLoaded', function() {
        // Initialisation des tooltips
        var tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'));
        var tooltipList = tooltipTriggerList.map(function (tooltipTriggerEl) {
            return new bootstrap.Tooltip(tooltipTriggerEl);
        });
    });
</script>
@endpush
