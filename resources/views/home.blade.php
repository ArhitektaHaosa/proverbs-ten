@extends('layouts.app')

@section('content')
    <h1>{{ config('site.name') }}</h1>
    <p>{{ config('site.tagline') }}</p>
    {{-- Proverb of the Day and search go here --}}
    <x-ad-slot name="home" />
@endsection
