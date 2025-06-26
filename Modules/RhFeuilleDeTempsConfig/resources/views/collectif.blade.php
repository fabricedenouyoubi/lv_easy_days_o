@extends('layouts.app')

@section('title', config('app.name') . ' | ' . ($codeTravail->libelle ?? ''))
@section('page-title', 'Configuration Collective') 

@section('content')
<!-- Breadcrumb -->
<x-breadcrumb :items="[
    ['label' => 'Ressources Humaines'],
    ['label' => 'Codes de Travail', 'url' => route('rhfeuilledetempsconfig.codes-travail.codetravails')],
    ['label' => 'Configuration - ' . ($codeTravail->libelle ?? 'Collectif')]
]" />

<div class="row">
    <div class="col-12">
        <livewire:rh-comportement::collectif-list :codeTravailId="$codeTravail->id" />
    </div>
</div>
@endsection