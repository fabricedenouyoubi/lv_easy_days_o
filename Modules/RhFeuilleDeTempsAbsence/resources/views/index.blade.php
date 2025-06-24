@extends('layouts.app')

@section('title', config('app.name') . ' | Absences')
@section('page-title', 'Gestion des Absences')
@section('content')

    <x-breadcrumb :items="[['label' => 'Mes demandes d\'absence']]" />

    <div class="row">
        <div class="col-12">
            <livewire:rhfeuilledetempsabsence::rh-feuille-de-temps-absence-list />
        </div>
    </div>
@endsection
