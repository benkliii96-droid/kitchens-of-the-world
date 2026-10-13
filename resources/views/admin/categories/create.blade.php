@extends('admin.layouts.app')

@section('title', 'Добавить категорию')

@section('content')
    <h1>Добавить категорию</h1>

    <form action="{{ route('admin.categories.store') }}" method="POST">
        @csrf
        @include('admin.categories._form')
        <button type="submit">Сохранить</button>
    </form>

    <p><a href="{{ route('admin.categories.index') }}">Назад</a></p>
@endsection
