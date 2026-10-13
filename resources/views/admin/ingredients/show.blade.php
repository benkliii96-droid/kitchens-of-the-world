@extends('admin.layouts.app')

@section('title', 'Ингредиент')

@section('content')
    <h1>{{ $ingredient->title }}</h1>

    <p>ID: {{ $ingredient->id }}</p>
    <p>Единица измерения: {{ $ingredient->unit }}</p>

    <p>
        <a href="{{ route('admin.ingredients.edit', $ingredient) }}">Изменить</a>
        <a href="{{ route('admin.ingredients.index') }}">Назад</a>
    </p>
@endsection
