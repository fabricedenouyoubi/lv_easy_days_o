@extends('layouts.app')

@section('title', config('app.name') . ' | Présentation')
@section('page-title', 'Présentation entreprise')

@section('content')
<!-- Breadcrumb -->
<x-breadcrumb :items="[
    ['label' => 'Présentation entreprise']
]" />

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