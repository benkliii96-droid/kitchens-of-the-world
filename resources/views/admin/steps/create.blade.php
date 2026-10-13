@extends('admin.layouts.app')

@section('title', 'Добавить шаг')

@section('content')
    <h1>Добавить шаг</h1>

    @if ($recipes->isEmpty())
        <p class="error">Сначала создайте хотя бы один рецепт</p>
    @else
        <form action="{{ route('admin.steps.store') }}" method="POST" enctype="multipart/form-data">
            @csrf
            @include('admin.steps._form')
            <button type="submit">Сохранить</button>
        </form>
    @endif

    <p><a href="{{ route('admin.steps.index') }}">Назад</a></p>
@endsection
