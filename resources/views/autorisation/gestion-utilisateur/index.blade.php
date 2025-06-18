@extends('layouts.app')

@section('title', config('app.name'). ' | Autorisation')
@section('page-title', 'Gestion des Utilisateurs')

@section('content')
<x-breadcrumb :items="[['label' => 'Permissions']]" />
<div class="row">
    <div class="col-12">
        <livewire:gestion-utilisateur />
    </div>
</div>
@endsection
