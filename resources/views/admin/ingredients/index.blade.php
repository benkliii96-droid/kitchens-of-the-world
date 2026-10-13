@extends('admin.layouts.app')

@section('title', 'Ингредиенты')

@section('content')
    <h1>Ингредиенты</h1>

    <p><a href="{{ route('admin.ingredients.create') }}">Добавить ингредиент</a></p>

    @if ($ingredients->isEmpty())
        <p>Ингредиентов пока нет</p>
    @else
        <table>
            <tr>
                <th>ID</th>
                <th>Название</th>
                <th>Единица</th>
                <th>Действия</th>
            </tr>
            @foreach ($ingredients as $ingredient)
                <tr>
                    <td>{{ $ingredient->id }}</td>
                    <td>{{ $ingredient->title }}</td>
                    <td>{{ $ingredient->unit }}</td>
                    <td>
                        <a href="{{ route('admin.ingredients.show', $ingredient) }}">Просмотр</a>
                        <a href="{{ route('admin.ingredients.edit', $ingredient) }}">Изменить</a>
                        <form action="{{ route('admin.ingredients.destroy', $ingredient) }}" method="POST">
                            @csrf
                            @method('DELETE')
                            <button type="submit" onclick="return confirm('Удалить ингредиент?')">Удалить</button>
                        </form>
                    </td>
                </tr>
            @endforeach
        </table>
    @endif
@endsection
