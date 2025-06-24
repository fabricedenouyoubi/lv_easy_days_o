@extends('layouts.app')

@section('title', config('app.name') . ' | Profile')
@section('page-title', 'Profile utilisateur')

@section('content')
    <livewire:profil-utilisateur>
@endsection
