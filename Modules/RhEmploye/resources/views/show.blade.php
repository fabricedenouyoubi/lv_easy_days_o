@extends('layouts.app')

@section('title', config('app.name'). ' | Employés')
@section('page-title', 'Details Employé')

@section('content')
<div class="row">
    <div class="col-12">
        <livewire:rhemploye::employe-details :employeId='$id'/>
    </div>
</div>
@endsection
