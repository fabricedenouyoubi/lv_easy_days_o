@extends('layouts.app')

@section('title', 'Années Financières - Budget')
@section('page-title', 'Années Financières')

@section('content')
<!-- Breadcrumb -->
<nav aria-label="breadcrumb" class="mb-4">
    <ol class="breadcrumb">
        <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Accueil</a></li>
        <li class="breadcrumb-item active" aria-current="page">Années Financières</li>
    </ol>
</nav>

<div class="row">
    <div class="col-12">
        <livewire:budget::annee-financiere-list />
    </div>
</div>
@endsection