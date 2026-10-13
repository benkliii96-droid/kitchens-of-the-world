@extends('admin.layouts.app')

@section('title', 'Рецепты')

@section('content')
    <h1>Рецепты</h1>

    <p><a href="{{ route('admin.recipes.create') }}">Добавить рецепт</a></p>

    <form action="{{ route('admin.recipes.index') }}" method="GET" class="filters">
        <select name="difficulty">
            <option value="">Сложность: все</option>
            @foreach (['легко', 'средне', 'сложно'] as $difficulty)
                <option value="{{ $difficulty }}" @selected(request('difficulty') === $difficulty)>{{ $difficulty }}</option>
            @endforeach
        </select>

        <select name="time">
            <option value="">Время: любое</option>
            <option value="to15" @selected(request('time') === 'to15')>до 15 минут</option>
            <option value="60-120" @selected(request('time') === '60-120')>60–120 минут</option>
            <option value="from120" @selected(request('time') === 'from120')>свыше 120 минут</option>
        </select>

        <select name="category_id">
            <option value="">Категория: все</option>
            @foreach ($categories as $category)
                <option value="{{ $category->id }}" @selected(request('category_id') == $category->id)>{{ $category->name }}</option>
            @endforeach
        </select>

        <select name="price">
            <option value="">Стоимость: любая</option>
            <option value="free" @selected(request('price') === 'free')>Бесплатные</option>
            <option value="paid" @selected(request('price') === 'paid')>Платные</option>
        </select>

        <select name="sort">
            <option value="newest_desc" @selected($sort === 'newest_desc')>По новизне ▼</option>
            <option value="newest_asc" @selected($sort === 'newest_asc')>По новизне ▲</option>
            <option value="time_asc" @selected($sort === 'time_asc')>По времени ▲</option>
            <option value="time_desc" @selected($sort === 'time_desc')>По времени ▼</option>
        </select>

        <button type="submit">Применить</button>
        <a href="{{ route('admin.recipes.index') }}">Сбросить</a>
    </form>

    @if ($recipes->isEmpty())
        <p>Рецептов не найдено</p>
    @else
        <table>
            <tr>
                <th>Фото</th>
                <th>Название</th>
                <th>Время</th>
                <th>Стоимость</th>
                <th>Сложность</th>
                <th>Статус</th>
                <th>Действия</th>
            </tr>
            @foreach ($recipes as $recipe)
                <tr class="{{ $recipe->trashed() ? 'trashed' : '' }}">
                    <td>
                        @if ($recipe->defaultImage)
                            <img src="{{ asset('storage/'.$recipe->defaultImage->thumbnail) }}" alt="" width="175">
                        @endif
                    </td>
                    <td>{{ $recipe->title }}</td>
                    <td>{{ $recipe->formatted_time }}</td>
                    <td>{{ $recipe->price > 0 ? $recipe->price.' ₽' : '' }}</td>
                    <td>{{ $recipe->difficulty }}</td>
                    <td>{{ $recipe->trashed() ? 'Помечен на удаление' : 'Активен' }}</td>
                    <td>
                        <a href="{{ route('admin.recipes.show', $recipe) }}">Просмотр</a>

                        @if ($recipe->trashed())
                            <form action="{{ route('admin.recipes.restore', $recipe) }}" method="POST">
                                @csrf
                                @method('PATCH')
                                <button type="submit">Восстановить</button>
                            </form>
                            <form action="{{ route('admin.recipes.force', $recipe) }}" method="POST">
                                @csrf
                                @method('DELETE')
                                <button type="submit" onclick="return confirm('Удалить рецепт навсегда?')">Удалить навсегда</button>
                            </form>
                        @else
                            <a href="{{ route('admin.recipes.edit', $recipe) }}">Изменить</a>
                            <form action="{{ route('admin.recipes.destroy', $recipe) }}" method="POST">
                                @csrf
                                @method('DELETE')
                                <button type="submit" onclick="return confirm('Пометить рецепт на удаление?')">Удалить</button>
                            </form>
                        @endif
                    </td>
                </tr>
            @endforeach
        </table>

        {{ $recipes->links('admin.pagination') }}
    @endif
@endsection
