@extends('admin.layouts.app')

@section('title', 'Шаги')

@section('content')
    <h1>Шаги рецептов</h1>

    <p><a href="{{ route('admin.steps.create') }}">Добавить шаг</a></p>

    @if ($steps->isEmpty())
        <p>Шагов пока нет</p>
    @else
        <table>
            <tr>
                <th>ID</th>
                <th>Рецепт</th>
                <th>Название</th>
                <th>Порядок</th>
                <th>Действия</th>
            </tr>
            @foreach ($steps as $step)
                <tr>
                    <td>{{ $step->id }}</td>
                    <td>{{ $step->recipe->title ?? '—' }}</td>
                    <td>{{ $step->title }}</td>
                    <td>{{ $step->order }}</td>
                    <td>
                        <a href="{{ route('admin.steps.show', $step) }}">Просмотр</a>
                        <a href="{{ route('admin.steps.edit', $step) }}">Изменить</a>
                        <form action="{{ route('admin.steps.destroy', $step) }}" method="POST">
                            @csrf
                            @method('DELETE')
                            <button type="submit" onclick="return confirm('Удалить шаг?')">Удалить</button>
                        </form>
                    </td>
                </tr>
            @endforeach
        </table>
    @endif
@endsection
