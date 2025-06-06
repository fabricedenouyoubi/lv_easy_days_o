@extends('layouts.app')

@section('title', 'Sites - Entreprise')
@section('page-title', 'Sites de entreprise') 

@section('content')
<div class="row">
    <div class="col-12">
        <livewire:entreprise::site-list />
    </div>
</div>
@endsection