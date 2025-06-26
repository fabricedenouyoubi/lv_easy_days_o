@extends('layouts.app')

@section('title', config('app.name') . ' | Configuration')
@section('page-title', 'Configuration des Jours fériés') 

@section('content')
<!-- Breadcrumb -->
<x-breadcrumb :items="[
    ['label' => 'Ressources Humaines'],
    ['label' => 'Codes de Travail', 'url' => route('rhfeuilledetempsconfig.codes-travail.codetravails')],
    ['label' => 'Configuration - ' . ($codeTravail->libelle ?? 'Jours fériés')]
]" />

<div class="row">
    <div class="col-12">
        <livewire:rh-comportement::jours-feries-list :codeTravailId="$codeTravail->id" />
    </div>
</div>
@endsection