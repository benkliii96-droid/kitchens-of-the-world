@extends('admin.layouts.app')

@section('title', 'Шаг')

@section('content')
    <h1>{{ $step->title }}</h1>

    <p>ID: {{ $step->id }}</p>
    <p>Рецепт: {{ $step->recipe->title ?? '—' }}</p>
    <p>Порядок: {{ $step->order }}</p>
    <p>Описание: {{ $step->description }}</p>

    @if ($step->image)
        <p>
            <img src="{{ asset('storage/'.$step->image) }}" alt="" width="300">
        </p>
    @endif

    <p>
        <a href="{{ route('admin.steps.edit', $step) }}">Изменить</a>
        <a href="{{ route('admin.steps.index') }}">Назад</a>
    </p>
@endsection
