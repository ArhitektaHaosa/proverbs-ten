<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>{{ $title ?? config('site.name') }}</title>
    <meta name="description" content="{{ $description ?? config('site.tagline') }}">
    <link rel="canonical" href="{{ url()->current() }}">
    @if(!config('site.indexable'))
        <meta name="robots" content="noindex,nofollow">
    @endif
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    @include('partials.cmp')
    @include('partials.analytics')
</head>
<body class="antialiased">
    <header>
        <a href="{{ route('home') }}">{{ config('site.name') }}</a>
    </header>

    <main>
        @yield('content')
    </main>

    <footer>
        <a href="{{ route('about') }}">About</a>
        <a href="{{ route('privacy') }}">Privacy</a>
        <a href="{{ route('licensing') }}">Licensing</a>
        <a href="{{ route('contact') }}">Contact</a>
    </footer>
</body>
</html>
