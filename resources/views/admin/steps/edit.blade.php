@extends('admin.layouts.app')

@section('title', 'Изменить шаг')

@section('content')
    <h1>Изменить шаг</h1>

    <form action="{{ route('admin.steps.update', $step) }}" method="POST" enctype="multipart/form-data">
        @csrf
        @method('PUT')
        @include('admin.steps._form')
        <button type="submit">Сохранить</button>
    </form>

    <p><a href="{{ route('admin.steps.index') }}">Назад</a></p>
@endsection
