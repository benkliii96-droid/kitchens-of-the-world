<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <title>@yield('title', 'Админ-панель')</title>
    <link rel="stylesheet" href="{{ asset('css/admin.css') }}">
</head>
<body>
    @auth
        <p>
            <a href="{{ route('admin.recipes.index') }}">Рецепты</a>
            <a href="{{ route('admin.categories.index') }}">Категории</a>
            <a href="{{ route('admin.ingredients.index') }}">Ингредиенты</a>
        </p>

        <form action="{{ route('admin.logout') }}" method="POST">
            @csrf
            <button type="submit">Выйти</button>
        </form>
        <hr>
    @endauth

    @if (session('success'))
        <p class="success">{{ session('success') }}</p>
    @endif

    @if (session('error'))
        <p class="error">{{ session('error') }}</p>
    @endif

    @yield('content')
</body>
</html>
