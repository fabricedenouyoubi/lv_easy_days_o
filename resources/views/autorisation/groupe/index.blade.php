@extends('layouts.app')

@section('title', config('app.name') . ' | Autorisation')
@section('page-title', 'Groupes')

@section('content')
    <div class="row">
        <div class="col-12">
            <livewire:groupe />
        </div>
    </div>
@endsection
