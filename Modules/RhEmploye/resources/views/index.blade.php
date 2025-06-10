@extends('layouts.app')

@section('title', config('app.name'). ' | Employés')
@section('page-title', 'Employés')

@section('content')
<div class="row">
    <div class="col-12">
        <livewire:rhemploye::employe-list />
    </div>
</div>
@endsection
