<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>@yield('title', 'Админ-панель') — Кухни мира</title>
    <link rel="stylesheet" href="{{ asset('css/admin.css') }}">
</head>
<body>
    <header class="header">
        <div class="container header__inner">
            <a href="{{ url('/admin') }}" class="logo">Кухни мира · Админ-панель</a>

            @auth
                <nav class="nav">
                    <a href="{{ route('admin.recipes.index') }}" @class(['active' => request()->routeIs('admin.recipes.*')])>Рецепты</a>
                    <a href="{{ route('admin.categories.index') }}" @class(['active' => request()->routeIs('admin.categories.*')])>Категории</a>
                    <a href="{{ route('admin.ingredients.index') }}" @class(['active' => request()->routeIs('admin.ingredients.*')])>Ингредиенты</a>
                    <a href="{{ route('admin.steps.index') }}" @class(['active' => request()->routeIs('admin.steps.*')])>Шаги</a>
                    <a href="{{ route('admin.purchases.index') }}" @class(['active' => request()->routeIs('admin.purchases.*')])>Заказы</a>
                </nav>

                <form action="{{ route('admin.logout') }}" method="POST">
                    @csrf
                    <button type="submit" class="btn btn--light">Выйти</button>
                </form>
            @endauth
        </div>
    </header>

    <main class="container main">
        @if (session('success'))
            <p class="alert success">{{ session('success') }}</p>
        @endif

        @if (session('error'))
            <p class="alert error">{{ session('error') }}</p>
        @endif

        @yield('content')
    </main>
</body>
</html>
