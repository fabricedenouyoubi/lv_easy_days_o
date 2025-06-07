@extends('layouts.app')

@section('title', config('app.name'). ' | Poste')
@section('page-title', 'Postes')

@section('content')
<div class="row">
    <div class="col-12">
        <livewire:rh::poste-list />
    </div>
</div>
@endsection