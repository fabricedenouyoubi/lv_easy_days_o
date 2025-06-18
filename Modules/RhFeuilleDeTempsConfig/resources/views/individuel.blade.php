@extends('layouts.app')

@section('title', 'Configuration Individuelle - ' . ($codeTravail->libelle ?? ''))
@section('page-title', 'Configuration Individuelle') 

@section('content')
<!-- Breadcrumb -->
<x-breadcrumb :items="[
    ['label' => 'Ressources Humaines'],
    ['label' => 'Codes de Travail', 'url' => route('rhfeuilledetempsconfig.codes-travail.codetravails')],
    ['label' => 'Configuration - ' . ($codeTravail->libelle ?? 'Individuel')]
]" />

<div class="row">
    <div class="col-12">
        <livewire:rh-comportement::individuel-list :codeTravailId="$codeTravail->id" />
    </div>
</div>
@endsection