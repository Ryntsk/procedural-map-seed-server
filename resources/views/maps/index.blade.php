<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="utf-8">
    <title>Генератор карт</title>
</head>
<body>
    <h1>Генератор карт по сиду</h1>

    <form method="GET" action="{{ route('maps.search') }}">
        <label>Сид: <input type="text" name="seed" value="{{ old('seed') }}"></label>
        <button type="submit">Показать карту</button>
    </form>

    @error('seed')
        <p style="color: red">{{ $message }}</p>
    @enderror
</body>
</html>