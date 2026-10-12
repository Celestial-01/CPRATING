@extends('layouts.admin')

@section('title', 'แก้ไขอาจารย์')

@section('content')
    <x-admin.teacher-form
        :prefixes="$prefixes"
        :teacher="$teacher"
    />
@endsection
