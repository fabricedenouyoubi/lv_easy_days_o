@extends('layouts.app')

@section('title', 'Codes de travail - Configuration RH')
@section('page-title', 'Gestion des Codes de travail') 

@section('content')
<!-- Breadcrumb -->
<x-breadcrumb :items="[
    ['label' => 'Configurations RH'],
    ['label' => 'Codes de travail']
]" />

<div class="row">
    <div class="col-12">
        <livewire:rh-config::codes-travail-list />
    </div>
</div>
@endsection