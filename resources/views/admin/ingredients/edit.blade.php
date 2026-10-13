@extends('admin.layouts.app')

@section('title', 'Изменить ингредиент')

@section('content')
    <h1>Изменить ингредиент</h1>

    <form action="{{ route('admin.ingredients.update', $ingredient) }}" method="POST">
        @csrf
        @method('PUT')
        @include('admin.ingredients._form')
        <button type="submit">Сохранить</button>
    </form>

    <p><a href="{{ route('admin.ingredients.index') }}">Назад</a></p>
@endsection
