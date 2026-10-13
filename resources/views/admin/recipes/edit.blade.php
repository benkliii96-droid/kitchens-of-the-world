@extends('admin.layouts.app')

@section('title', 'Изменить рецепт')

@section('content')
    <h1>Изменить рецепт</h1>

    @if ($recipe->images->isNotEmpty())
        <h3>Текущие фото</h3>
        <table>
            <tr>
                @foreach ($recipe->images as $image)
                    <td>
                        <img src="{{ asset('storage/'.$image->thumbnail) }}" alt="" width="175"><br>
                        @if ($image->is_default)
                            По умолчанию<br>
                        @endif
                        <form action="{{ route('admin.recipes.images.destroy', [$recipe, $image]) }}" method="POST">
                            @csrf
                            @method('DELETE')
                            <button type="submit" onclick="return confirm('Удалить фото?')">Удалить фото</button>
                        </form>
                    </td>
                @endforeach
            </tr>
        </table>
    @endif

    <form action="{{ route('admin.recipes.update', $recipe) }}" method="POST" enctype="multipart/form-data">
        @csrf
        @method('PUT')
        @include('admin.recipes._form')

        <fieldset>
            <legend>Шаги</legend>
            <ol>
                @foreach ($recipe->steps as $step)
                    <li>
                        {{ $step->title }}
                        <a href="{{ route('admin.steps.edit', $step) }}">Изменить</a>
                    </li>
                @endforeach
            </ol>
            <a href="{{ route('admin.steps.create', ['recipe_id' => $recipe->id]) }}">Добавить шаг</a>
        </fieldset>

        <button type="submit">Сохранить</button>
    </form>

    <p><a href="{{ route('admin.recipes.index') }}">Назад</a></p>
@endsection
