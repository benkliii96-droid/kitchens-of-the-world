@extends('admin.layouts.app')

@section('title', 'Добавить рецепт')

@section('content')
    <h1>Добавить рецепт</h1>

    <form action="{{ route('admin.recipes.store') }}" method="POST" enctype="multipart/form-data">
        @csrf
        @include('admin.recipes._form', ['recipe' => null])

        <fieldset>
            <legend>Шаги</legend>

            @error('steps')
                <p class="invalid-feedback">{{ $message }}</p>
            @enderror

            @for ($i = 0; $i < 3; $i++)
                <p>
                    <b>Шаг {{ $i + 1 }}</b><br>
                    <input type="text" name="steps[{{ $i }}][title]" value="{{ old("steps.$i.title") }}" placeholder="Название"
                           class="@error("steps.$i.title") is-invalid @enderror">
                    @error("steps.$i.title")
                        <br><span class="invalid-feedback">{{ $message }}</span>
                    @enderror
                    <br>
                    <textarea name="steps[{{ $i }}][description]" rows="2" cols="60" placeholder="Описание"
                              class="@error("steps.$i.description") is-invalid @enderror">{{ old("steps.$i.description") }}</textarea>
                    @error("steps.$i.description")
                        <br><span class="invalid-feedback">{{ $message }}</span>
                    @enderror
                    <br>
                    <input type="file" name="steps[{{ $i }}][image]" accept=".jpg,.jpeg,.png,.webp"
                           class="@error("steps.$i.image") is-invalid @enderror">
                    @error("steps.$i.image")
                        <br><span class="invalid-feedback">{{ $message }}</span>
                    @enderror
                </p>
            @endfor

            <p>Остальные шаги можно добавить в разделе «Шаги».</p>
        </fieldset>

        <button type="submit">Сохранить</button>
    </form>

    <p><a href="{{ route('admin.recipes.index') }}">Назад</a></p>
@endsection
