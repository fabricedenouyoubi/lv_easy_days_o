@extends('layouts.app')

@section('title', config('app.name') . ' | Feuille de temps')
@section('page-title', 'Édition Feuille de temps')

@section('content')
    <livewire:rhfeuilledetempsreguliere::rh-feuille-de-temps-reguliere-edit 
        :operationId="$operationId" 
        :semaineId="$semaineId" 
    />
@endsection

@push('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function() {
        // Auto-calcul des heures au changement
        document.addEventListener('input', function(e) {
            if (e.target.matches('input[type="number"]')) {
                // Déclencher le recalcul côté Livewire
                e.target.dispatchEvent(new Event('change'));
            }
        });
        
        // Validation des heures max par jour
        document.addEventListener('input', function(e) {
            if (e.target.matches('input[type="number"]')) {
                const value = parseFloat(e.target.value);
                if (value > 12) {
                    e.target.value = 12;
                    alert('Maximum 12 heures par jour autorisées');
                }
            }
        });
    });
</script>
@endpush