<!doctype html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8" />
    <title>@yield('title', config('app.name'))</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="description" content="@yield('description', 'Application de gestion de feuille de temps')" />
    <meta name="author" content="@yield('author', config('app.name'))" />
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <!-- App favicon -->
    <link rel="shortcut icon" href="{{ asset('assets/borex/images/favicon.ico') }}">

    <!-- Bootstrap Css -->
    <link href="{{ asset('assets/borex/css/bootstrap.min.css') }}" id="bootstrap-style" rel="stylesheet" type="text/css" />
    <!-- Icons Css -->
    <link href="{{ asset('assets/borex/css/icons.min.css') }}" rel="stylesheet" type="text/css" />
    <!-- App Css-->
    <link href="{{ asset('assets/borex/css/app.min.css') }}" id="app-style" rel="stylesheet" type="text/css" />

    @stack('styles')
    @livewireStyles
</head>

<body data-sidebar="dark" @yield('body-attributes')>
    <!-- Begin page -->
    <div id="layout-wrapper">

        <!-- Header -->
        @include('layouts.partials.header')

        <!-- Sidebar -->
        @include('layouts.partials.sidebar')

        <!-- Horizontal Header (si nécessaire) -->
       @include('layouts.partials.horizontal-header')

        <!-- Main Content -->
        <div class="main-content">
            <div class="page-content">
                <div class="container-fluid">

                    @yield('content')

                </div>
            </div>

            <!-- Footer -->
            @include('layouts.partials.footer')
        </div>
        <!-- end main content-->
    </div>
    <!-- END layout-wrapper -->

    <!-- Right Sidebar -->
   @include('layouts.partials.right-sidebar')

    <!-- Right bar overlay-->
    <div class="rightbar-overlay"></div>

    <!-- JAVASCRIPT -->
    <script src="{{ asset('assets/borex/libs/bootstrap/js/bootstrap.bundle.min.js') }}"></script>
    <script src="{{ asset('assets/borex/libs/metismenujs/metismenujs.min.js') }}"></script>
    <script src="{{ asset('assets/borex/libs/simplebar/simplebar.min.js') }}"></script>
    <script src="{{ asset('assets/borex/libs/eva-icons/eva.min.js') }}"></script>

    @stack('scripts')
    @livewireScripts

    <script src="{{ asset('assets/borex/js/app.js') }}"></script>
</body>
</html>


