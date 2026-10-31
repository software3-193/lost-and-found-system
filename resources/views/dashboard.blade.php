@extends('layouts.app')
@section('title', 'الرئيسية')
@section('wrap', 'wide')
@section('content')
<div class="panel">
    <h1>أهلاً {{ auth()->user()->name }}</h1>
    <p class="sub">
        دورك في النظام:
        <span class="badge">{{ auth()->user()->isAdmin() ? 'مدير' : 'طالب' }}</span>
    </p>
    <p>هنا ستظهر قائمة المفقودات وإضافة مفقود جديد (الميزات القادمة).</p>
    @if(auth()->user()->isAdmin())
        <p><a href="{{ route('admin.home') }}">الانتقال إلى لوحة المدير</a></p>
    @endif
</div>
@endsection
