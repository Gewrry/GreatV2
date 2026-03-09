@props(['header_title'])

@extends('layouts.hr.app')

@section('header_title', $header_title ?? 'Human Resource Management System')

@section('slot')
    {{ $slot }}
@endsection
