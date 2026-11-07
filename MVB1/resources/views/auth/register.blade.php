@extends('layouts.app')
@section('title', 'إنشاء حساب')
@section('content')
<div class="panel">
    <h1>إنشاء حساب طالب</h1>
    <p class="sub">سجّل مرة واحدة لتتمكن من نشر المفقودات والبحث عنها.</p>

    <form method="POST" action="{{ route('register') }}" novalidate>
        @csrf
        <label for="name">الاسم الكامل</label>
        <input type="text" id="name" name="name" value="{{ old('name') }}" required autofocus>
        @error('name')<p class="err">{{ $message }}</p>@enderror

        <label for="email">البريد الإلكتروني</label>
        <input type="email" id="email" name="email" dir="ltr" value="{{ old('email') }}" required>
        @error('email')<p class="err">{{ $message }}</p>@enderror

        <label for="password">كلمة المرور (8 أحرف على الأقل)</label>
        <input type="password" id="password" name="password" dir="ltr" required>
        @error('password')<p class="err">{{ $message }}</p>@enderror

        <label for="password_confirmation">تأكيد كلمة المرور</label>
        <input type="password" id="password_confirmation" name="password_confirmation" dir="ltr" required>

        <button type="submit" class="btn">إنشاء الحساب</button>
    </form>

    <p class="foot">لديك حساب؟ <a href="{{ route('login') }}">تسجيل الدخول</a></p>
</div>
@endsection
