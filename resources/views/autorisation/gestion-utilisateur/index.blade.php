@extends('layouts.app')

@section('title', config('app.name'). ' | Autorisation')
@section('page-title', 'Utilisateurs')

@section('content')
<div class="row">
    <div class="col-12">
        <livewire:gestion-utilisateur />
    </div>
</div>
@endsection
