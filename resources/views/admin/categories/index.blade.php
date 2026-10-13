@extends('admin.layouts.app')

@section('title', 'Категории')

@section('content')
    <h1>Категории</h1>

    <p><a href="{{ route('admin.categories.create') }}">Добавить категорию</a></p>

    @if ($categories->isEmpty())
        <p>Категорий пока нет</p>
    @else
        <table>
            <tr>
                <th>ID</th>
                <th>Название</th>
                <th>Рецептов</th>
                <th>Действия</th>
            </tr>
            @foreach ($categories as $category)
                <tr>
                    <td>{{ $category->id }}</td>
                    <td>{{ $category->name }}</td>
                    <td>{{ $category->recipes_count }}</td>
                    <td>
                        <a href="{{ route('admin.categories.show', $category) }}">Просмотр</a>
                        <a href="{{ route('admin.categories.edit', $category) }}">Изменить</a>
                        <form action="{{ route('admin.categories.destroy', $category) }}" method="POST">
                            @csrf
                            @method('DELETE')
                            <button type="submit" onclick="return confirm('Удалить категорию?')">Удалить</button>
                        </form>
                    </td>
                </tr>
            @endforeach
        </table>
    @endif
@endsection
