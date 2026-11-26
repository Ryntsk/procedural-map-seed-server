@extends('layouts.app')

@section('title', 'Карта ' . $map['seed'])

@section('content')
    <h1>Карта, сид {{ $map['seed'] }}</h1>
    <p class="map-info">Координаты: ({{ $map['x'] }}, {{ $map['y'] }}), размер {{ $map['width'] }}×{{ $map['height'] }}</p>

    <div class="map-wrap">
        <div class="map-grid">
            @foreach ($grid as $row)
                <div class="map-row">
                    @foreach ($row as $tile)
                        <div class="tile {{ $tile->value }}"></div>
                    @endforeach
                </div>
            @endforeach
        </div>
    </div>

    <div class="map-nav">
        <a class="btn" href="{{ route('maps.show', [
            'seed' => $map['seed'],
            'x' => $map['x'] + $map['width'],
            'y' => $map['y'],
            'width' => $map['width'],
            'height' => $map['height'],
        ]) }}">Сдвинуть вправо</a>
        <a href="{{ route('maps.index') }}">Другой сид</a>
    </div>
@endsection