@extends('layouts.app')

@section('title', config('app.name'). ' | Autorisation')
@section('page-title', 'Gestion des Permissions')

@section('content')
<x-breadcrumb :items="[['label' => 'Permissions']]" />
<div class="row">
    <div class="col-12">
        <livewire:permission />
    </div>
</div>
@endsection
