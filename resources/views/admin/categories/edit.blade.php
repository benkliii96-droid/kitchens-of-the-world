@extends('admin.layouts.app')

@section('title', 'Изменить категорию')

@section('content')
    <h1>Изменить категорию</h1>

    <form action="{{ route('admin.categories.update', $category) }}" method="POST">
        @csrf
        @method('PUT')
        @include('admin.categories._form')
        <button type="submit">Сохранить</button>
    </form>

    <p><a href="{{ route('admin.categories.index') }}">Назад</a></p>
@endsection
