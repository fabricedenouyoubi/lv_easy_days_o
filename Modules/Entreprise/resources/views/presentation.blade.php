@extends('layouts.app')

@section('title', 'Entreprise')
@section('page-title', 'Présentation entreprise')

@section('content')
<!-- Breadcrumb -->
<nav aria-label="breadcrumb" class="mb-4">
    <ol class="breadcrumb">
        <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Accueil</a></li>
        <li class="breadcrumb-item active" aria-current="page">Présentation entreprise</li>
    </ol>
</nav>

<div class="row">
    <div class="col-12 mb-4">
        <livewire:entreprise::entreprise-presentation />
    </div>

    <!-- Section Sites -->
    <div class="col-12">
        <livewire:entreprise::sites-list />
    </div>

</div>
@endsection