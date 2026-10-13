@extends('admin.layouts.app')

@section('title', 'Добавить ингредиент')

@section('content')
    <h1>Добавить ингредиент</h1>

    <form action="{{ route('admin.ingredients.store') }}" method="POST">
        @csrf
        @include('admin.ingredients._form')
        <button type="submit">Сохранить</button>
    </form>

    <p><a href="{{ route('admin.ingredients.index') }}">Назад</a></p>
@endsection
