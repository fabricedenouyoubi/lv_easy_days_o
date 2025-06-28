@extends('layouts.app')

@section('title', config('app.name') . ' | Feuille de temps')
@section('page-title', 'Consultation Feuille de temps')

@section('content')
    <livewire:rhfeuilledetempsreguliere::rh-feuille-de-temps-reguliere-show 
        :operationId="$operation->id" 
        :semaineId="$semaine->id" 
    />
@endsection

@push('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function() {
        // Initialisation des tooltips
        var tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'));
        var tooltipList = tooltipTriggerList.map(function (tooltipTriggerEl) {
            return new bootstrap.Tooltip(tooltipTriggerEl);
        });
        
        // Gestion de l'affichage conditionnel des modals
        window.addEventListener('show-modal', event => {
            const modal = document.getElementById(event.detail.modal);
            if (modal) {
                modal.style.display = 'block';
                modal.classList.add('show');
            }
        });
        
        window.addEventListener('hide-modal', event => {
            const modal = document.getElementById(event.detail.modal);
            if (modal) {
                modal.style.display = 'none';
                modal.classList.remove('show');
            }
        });
    });
</script>
@endpush