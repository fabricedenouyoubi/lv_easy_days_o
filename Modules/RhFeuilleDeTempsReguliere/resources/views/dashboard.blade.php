@extends('layouts.app')

@section('title', config('app.name') . ' | Feuilles de temps')
@section('page-title', 'Mes Feuilles de temps')

@section('content')
    <x-breadcrumb :items="[
        ['label' => 'Feuilles de temps'],
        ['label' => 'En attente de validation']
    ]" />

    <div class="row">
        <div class="col-12">
            <livewire:rhfeuilledetempsreguliere::livewire.rh-feuille-de-temps-reguliere-manager-dashboard />
        </div>
    </div>
@endsection