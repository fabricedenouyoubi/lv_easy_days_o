@extends('layouts.app')

@section('title', 'Jours fériés - Configuration')
@section('page-title', 'Configuration des Jours fériés') 

@section('content')
<!-- Breadcrumb -->
<nav aria-label="breadcrumb" class="mb-4">
    <ol class="breadcrumb">
        <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Accueil</a></li>
        <li class="breadcrumb-item"><a href="#">Ressources Humaines</a></li>
        <li class="breadcrumb-item"><a href="{{ route('rhfeuilledetempsconfig.codes-travail.codetravails') }}">Codes de Travail</a></li>
        <li class="breadcrumb-item active" aria-current="page">Configuration - {{ $codeTravail->libelle ?? 'Jours fériés' }}</li>
    </ol>
</nav>

<div class="row">
    <div class="col-12">
        <livewire:rh-comportement::jours-feries-list :codeTravailId="$codeTravail->id" />
    </div>
</div>
@endsection