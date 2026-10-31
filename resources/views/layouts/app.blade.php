<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>@yield('title', 'نظام المفقودات')</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link href="https://fonts.googleapis.com/css2?family=IBM+Plex+Sans+Arabic:wght@400;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="{{ asset('css/app.css') }}">
</head>
<body>
    <header class="topbar">
        <div class="brand"><i></i> نظام المفقودات</div>
        @auth
            <form method="POST" action="{{ route('logout') }}">
                @csrf
                <span>{{ auth()->user()->name }}</span>
                <button type="submit" class="btn small">تسجيل الخروج</button>
            </form>
        @endauth
    </header>
    <main class="wrap @yield('wrap')">
        @yield('content')
    </main>
</body>
</html>
