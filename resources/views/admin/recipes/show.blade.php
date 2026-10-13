@extends('admin.layouts.app')

@section('title', $recipe->title)

@section('content')
    <h1>{{ $recipe->title }}</h1>

    @if ($recipe->trashed())
        <p class="error">Рецепт помечен на удаление</p>
    @endif

    <p>Категория: {{ $recipe->category->name ?? '—' }}</p>
    <p>Время приготовления: {{ $recipe->formatted_time }}</p>
    <p>Сложность: {{ $recipe->difficulty }}</p>
    <p>Стоимость: {{ $recipe->price > 0 ? $recipe->price.' ₽' : 'бесплатно' }}</p>
    <p>{{ $recipe->description }}</p>

    <h3>Фото</h3>
    @foreach ($recipe->images as $image)
        <a href="{{ asset('storage/'.$image->path) }}" target="_blank">
            <img src="{{ asset('storage/'.$image->thumbnail) }}" alt="" width="175">
        </a>
        @if ($image->is_default)
            (по умолчанию)
        @endif
    @endforeach

    <h3>Ингредиенты</h3>
    <ul>
        @foreach ($recipe->ingredients as $ingredient)
            <li>{{ $ingredient->title }} — {{ $ingredient->pivot->quantity }} {{ $ingredient->pivot->unit }}</li>
        @endforeach
    </ul>

    <h3>Шаги</h3>
    <ol>
        @foreach ($recipe->steps as $step)
            <li>
                <b>{{ $step->title }}</b><br>
                {{ $step->description }}<br>
                @if ($step->image)
                    <img src="{{ asset('storage/'.$step->image) }}" alt="" width="200">
                @endif
            </li>
        @endforeach
    </ol>

    <p>
        @unless ($recipe->trashed())
            <a href="{{ route('admin.recipes.edit', $recipe) }}">Изменить</a>
        @endunless
        <a href="{{ route('admin.recipes.index') }}">Назад</a>
    </p>
@endsection
