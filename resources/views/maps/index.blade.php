@extends('layouts.app')

@section('title', 'Генератор карт')

@section('content')
    <h1>Генератор карт по сиду</h1>

    <form method="GET" action="{{ route('maps.search') }}">
        <label>Сид: <input type="text" name="seed" value="{{ old('seed') }}"></label>
        <button type="submit">Показать карту</button>
    </form>

    @error('seed')
        <p style="color: red">{{ $message }}</p>
    @enderror
@endsection