@extends('layouts.app')

@section('title', 'Catégories - Configuration RH')
@section('page-title', 'Gestion des Catégories') 

@section('content')
<!-- Breadcrumb -->
<nav aria-label="breadcrumb" class="mb-4">
    <ol class="breadcrumb">
        <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Accueil</a></li>
        <li class="breadcrumb-item">Configuration</li>
        <li class="breadcrumb-item active" aria-current="page">Catégories</li>
    </ol>
</nav>

<div class="row">
    <div class="col-12">
        <livewire:rh-config::categories-list />
    </div>
</div>
@endsection