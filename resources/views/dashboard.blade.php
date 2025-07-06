@extends('layouts.app')

@section('title', 'Dashboard - Feuille de Temps')
@section('page-title', 'Dashboard')

@section('content')
    <!-- Breadcrumb -->
    <x-breadcrumb :items="[
        ['label' => 'Accueil']
    ]" />

    <div class="card shadow-sm">
        <div class="card-body p-4">
            @livewire('rh-feuille-de-temps-reguliere-manager-dashboard')
        </div>
    </div>
@endsection