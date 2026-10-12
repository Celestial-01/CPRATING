@extends('layouts.admin')

@section('title', 'เพิ่มอาจารย์')

@section('content')
    <x-admin.teacher-form :prefixes="$prefixes" />
@endsection
