@props(['title' => null])

@extends('layouts.app', ['title' => $title])

@section('content')
    {{ $slot }}
@endsection