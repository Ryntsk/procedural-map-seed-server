@extends('layouts.app')

@section('title', 'Генератор карт')

@section('content')
    <h1>Генератор карт по сиду</h1>

    <form class="seed-form" method="GET" action="{{ route('maps.search') }}">
        <label>Сид: <input type="text" name="seed" value="{{ old('seed') }}"></label>
        <button type="submit">Показать карту</button>
    </form>

    @error('seed')
        <p class="error">{{ $message }}</p>
    @enderror
@endsection