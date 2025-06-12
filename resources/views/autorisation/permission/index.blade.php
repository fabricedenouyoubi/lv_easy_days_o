@extends('layouts.app')

@section('title', config('app.name'). ' | Autorisation')
@section('page-title', 'Permissions')

@section('content')
<div class="row">
    <div class="col-12">
        <livewire:permission />
    </div>
</div>
@endsection
