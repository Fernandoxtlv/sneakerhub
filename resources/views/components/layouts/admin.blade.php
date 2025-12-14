@props(['title' => null, 'header' => null, 'subheader' => null])

@extends('layouts.admin')

@section('content')
    {{ $slot }}
@endsection