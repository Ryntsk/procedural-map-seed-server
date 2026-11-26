@extends('layouts.app')

@section('title', 'Генератор карт')

@section('content')
    <h1>Генератор карт по сиду</h1>
    <p class="lead">Одно и то же число всегда даёт одну и ту же карту.</p>

    @error('seed')
        <p class="error">{{ $message }}</p>
    @enderror

    <form class="seed-form" method="GET" action="{{ route('maps.search') }}">
        <label>Сид
            <input type="text" name="seed" value="{{ old('seed') }}"
                   placeholder="например, 42" inputmode="numeric"
                   aria-invalid="{{ $errors->has('seed') ? 'true' : 'false' }}">
        </label>
        <button type="submit">Показать карту</button>
    </form>
@endsection