@extends('layouts.app')

@section('title', config('app.name') . ' | Employés')
@section('page-title', 'Gestion des Employés')
@section('content')

    <x-breadcrumb :items="[['label' => 'RH Employé']]" />

    <div class="row">
        <div class="col-12">
            <livewire:rh::employe.employe-list />
        </div>
    </div>
@endsection
