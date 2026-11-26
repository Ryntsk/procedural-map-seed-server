<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>@yield('title', 'Генератор карт')</title>
    <link rel="stylesheet" href="{{ asset('css/app.css') }}">
</head>
<body>
    <header class="site-header">
        <nav class="site-nav">
            <span class="brand">Генератор карт</span>
            <a href="{{ route('maps.index') }}">Главная</a>
            <a href="{{ route('maps.show', ['seed' => 42]) }}">Пример: сид 42</a>
            <a href="{{ url('/api/maps/42') }}">API</a>
        </nav>
    </header>

    <main class="container">
        @yield('content')
    </main>
</body>
</html>