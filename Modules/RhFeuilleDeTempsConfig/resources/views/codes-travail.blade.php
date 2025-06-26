@extends('layouts.app')

@section('title', config('app.name') .' | Codes de travail')
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