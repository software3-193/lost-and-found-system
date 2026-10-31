@extends('layouts.app')
@section('title', 'تسجيل الدخول')
@section('content')
<div class="panel">
    <h1>تسجيل الدخول</h1>
    <p class="sub">ادخل لتبحث عن غرضك المفقود أو تعلن عن غرض وجدته.</p>

    <form method="POST" action="{{ route('login') }}" novalidate>
        @csrf
        <label for="email">البريد الإلكتروني</label>
        <input type="email" id="email" name="email" dir="ltr" value="{{ old('email') }}" required autofocus>
        @error('email')<p class="err">{{ $message }}</p>@enderror

        <label for="password">كلمة المرور</label>
        <input type="password" id="password" name="password" dir="ltr" required>
        @error('password')<p class="err">{{ $message }}</p>@enderror

        <label class="check"><input type="checkbox" name="remember" value="1"> تذكرني</label>

        <button type="submit" class="btn">دخول</button>
    </form>

    <p class="foot">ليس لديك حساب؟ <a href="{{ route('register') }}">إنشاء حساب جديد</a></p>
</div>
@endsection
