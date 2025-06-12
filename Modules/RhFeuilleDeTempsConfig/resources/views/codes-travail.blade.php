@extends('layouts.app')

@section('title', 'Codes de travail - Configuration RH')
@section('page-title', 'Gestion des Codes de travail') 

@section('content')
<!-- Breadcrumb -->
<nav aria-label="breadcrumb" class="mb-4">
    <ol class="breadcrumb">
        <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Accueil</a></li>
        <li class="breadcrumb-item">Configurations RH</li>
        <li class="breadcrumb-item active" aria-current="page">Codes de travail</li>
    </ol>
</nav>

<div class="row">
    <div class="col-12">
        <livewire:rh-config::codes-travail-list />
    </div>
</div>
@endsection