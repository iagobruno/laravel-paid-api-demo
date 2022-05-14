<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <meta name="turbo-cache-control" content="no-preview">

    <title>@hasSection('page_title')@yield('page_title') - @endif{{ config('app.name') }}</title>

    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.1/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-F3w7mX95PdgyTmZZMECAngseQB83DfGTowi0iMjiWaeVhAn4FJkqJByhZMI3AhiU" crossorigin="anonymous">

    {{-- <link rel="stylesheet" href="{{ mix('/css/app.css') }}"> --}}

    {{-- <script src="{{ mix('/js/app.js') }}" async defer></script> --}}

    @stack('extra_head')
</head>

<body class="@stack('body_class')">
    @include('layouts.partials.top-banner')

    @if ($show_header ?? true)
        @include('layouts.partials.header')
    @endif

    @include('layouts.partials.flash-messages')

    <main class="container pb-5">
        @yield('content')
    </main>

    @stack('extra_body')
</body>
</html>
