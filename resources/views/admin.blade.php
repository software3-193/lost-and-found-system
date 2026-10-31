@extends('layouts.app')
@section('title', 'لوحة المدير')
@section('wrap', 'wide')
@section('content')
<div class="panel">
    <h1>لوحة المدير</h1>
    <p class="sub">هنا ستراجع طلبات المفقودات وتؤكد الملكية (ميزة الإشراف القادمة).</p>
    <p><a href="{{ route('dashboard') }}">رجوع</a></p>
</div>
@endsection
