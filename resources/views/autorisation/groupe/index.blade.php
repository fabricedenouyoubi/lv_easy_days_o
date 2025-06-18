@extends('layouts.app')

@section('title', config('app.name') . ' | Autorisation')
@section('page-title', 'Gestion des Groupes')

@section('content')
    <x-breadcrumb :items="[['label' => 'Groupes']]" />
    <div class="row">
        <div class="col-12">
            <livewire:groupe />
        </div>
    </div>
@endsection
