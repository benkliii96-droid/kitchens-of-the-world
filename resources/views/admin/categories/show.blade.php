@extends('admin.layouts.app')

@section('title', 'Категория')

@section('content')
    <h1>{{ $category->name }}</h1>

    <p>ID: {{ $category->id }}</p>
    <p>Рецептов: {{ $category->recipes_count }}</p>

    <p>
        <a href="{{ route('admin.categories.edit', $category) }}">Изменить</a>
        <a href="{{ route('admin.categories.index') }}">Назад</a>
    </p>
@endsection
