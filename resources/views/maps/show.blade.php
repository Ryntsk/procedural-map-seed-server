<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="utf-8">
    <title>Карта {{ $map['seed'] }}</title>
    <style>
        .row { display: flex; }
        .tile { width: 16px; height: 16px; }
        .water { background: #3a7bd5; }
        .plain { background: #a8d672; }
        .forest { background: #2e7d32; }
        .mountain { background: #8d8d8d; }
    </style>
</head>
<body>
    <h1>Карта, сид {{ $map['seed'] }}</h1>
    <p>Координаты: ({{ $map['x'] }}, {{ $map['y'] }}), размер {{ $map['width'] }}×{{ $map['height'] }}</p>

    <div>
        @foreach ($grid as $row)
            <div class="row">
                @foreach ($row as $tile)
                    <div class="tile {{ $tile->value }}"></div>
                @endforeach
            </div>
        @endforeach
    </div>

    <p>
        <a href="{{ route('maps.show', [
            'seed' => $map['seed'],
            'x' => $map['x'] + $map['width'],
            'y' => $map['y'],
            'width' => $map['width'],
            'height' => $map['height'],
        ]) }}">Сдвинуть вправо</a>
    </p>
    <p><a href="{{ route('maps.index') }}">Другой сид</a></p>
</body>
</html>