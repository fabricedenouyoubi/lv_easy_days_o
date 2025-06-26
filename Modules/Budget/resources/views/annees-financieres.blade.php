@extends('layouts.app')

@section('title', config('app.name') . ' | Budget')
@section('page-title', 'Années Financières')

@section('content')
<!-- Breadcrumb -->
<x-breadcrumb :items="[
    ['label' => 'Années Financières']
]" />

<div class="row">
    <div class="col-12">
        <livewire:budget::annee-financiere-list />
    </div>
</div>
@endsection