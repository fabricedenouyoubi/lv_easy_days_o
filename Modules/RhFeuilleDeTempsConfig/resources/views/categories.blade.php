@extends('layouts.app')

@section('title', config('app.name') .' | Catégories ')
@section('page-title', 'Gestion des Catégories') 

@section('content')
<!-- Breadcrumb -->
@section('content')
<x-breadcrumb :items="[
    ['label' => 'Configurations RH'],
    ['label' => 'Catégories']
]" />

<div class="row">
    <div class="col-12">
        <livewire:rh-config::categories-list />
    </div>
</div>
@endsection