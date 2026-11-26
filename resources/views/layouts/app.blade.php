<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>@yield('title', 'Генератор карт')</title>
    @yield('head')
</head>
<body>
    <header>
        <nav>
            <a href="{{ route('maps.index') }}">Генератор карт</a>
            <a href="{{ route('maps.show', ['seed' => 42]) }}">Пример: сид 42</a>
            <a href="{{ url('/api/maps/42') }}">API</a>
        </nav>
    </header>

    <main>
        @yield('content')
    </main>
</body>
</html>