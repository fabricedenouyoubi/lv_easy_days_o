@extends('layouts.app')

@section('title', config('app.name') . ' | Absences')
@section('page-title', 'Gestion des Absences')
@section('content')

    <x-breadcrumb :items="[['label' => 'Mes demandes d\'absence'],['label' => 'Detail de la demande']]" />

    <div class="row">
        <div class="col-12">
            <livewire:rhfeuilledetempsabsence::rh-feuille-de-temps-absence-details :demandeAbsenceId="$id" />
        </div>
    </div>
@endsection
