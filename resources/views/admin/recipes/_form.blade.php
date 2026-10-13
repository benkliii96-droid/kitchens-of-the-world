@php
    $ingredientRows = old('ingredients');

    if ($ingredientRows === null) {
        $ingredientRows = [];
        foreach ($recipe?->ingredients ?? [] as $item) {
            $ingredientRows[] = ['id' => $item->id, 'quantity' => $item->pivot->quantity];
        }
    }

    $ingredientRows = array_values($ingredientRows);
    for ($i = 0; $i < 3; $i++) {
        $ingredientRows[] = ['id' => '', 'quantity' => ''];
    }
@endphp

<p>
    <label>Название рецепта</label><br>
    <input type="text" name="title" value="{{ old('title', $recipe->title ?? '') }}"
           class="@error('title') is-invalid @enderror">
    @error('title')
        <br><span class="invalid-feedback">{{ $message }}</span>
    @enderror
</p>

<p>
    <label>Описание</label><br>
    <textarea name="description" rows="4" cols="60" class="@error('description') is-invalid @enderror">{{ old('description', $recipe->description ?? '') }}</textarea>
    @error('description')
        <br><span class="invalid-feedback">{{ $message }}</span>
    @enderror
</p>

<p>
    <label>Время приготовления (минут)</label><br>
    <input type="number" name="cooking_time" value="{{ old('cooking_time', $recipe->cooking_time ?? '') }}"
           class="@error('cooking_time') is-invalid @enderror">
    @error('cooking_time')
        <br><span class="invalid-feedback">{{ $message }}</span>
    @enderror
</p>

<p>
    <label>Сложность</label><br>
    <select name="difficulty" class="@error('difficulty') is-invalid @enderror">
        <option value="">— выберите —</option>
        @foreach (['легко', 'средне', 'сложно'] as $difficulty)
            <option value="{{ $difficulty }}" @selected(old('difficulty', $recipe->difficulty ?? '') === $difficulty)>{{ $difficulty }}</option>
        @endforeach
    </select>
    @error('difficulty')
        <br><span class="invalid-feedback">{{ $message }}</span>
    @enderror
</p>

<p>
    <label>Категория</label><br>
    <select name="category_id" class="@error('category_id') is-invalid @enderror">
        <option value="">— выберите —</option>
        @foreach ($categories as $category)
            <option value="{{ $category->id }}" @selected(old('category_id', $recipe->category_id ?? '') == $category->id)>{{ $category->name }}</option>
        @endforeach
    </select>
    @error('category_id')
        <br><span class="invalid-feedback">{{ $message }}</span>
    @enderror
</p>

<p>
    <label>Цена (0 — бесплатно)</label><br>
    <input type="text" name="price" value="{{ old('price', $recipe->price ?? '0.00') }}"
           class="@error('price') is-invalid @enderror">
    @error('price')
        <br><span class="invalid-feedback">{{ $message }}</span>
    @enderror
</p>

<fieldset>
    <legend>Фотографии</legend>

    @if ($recipe && $recipe->images->isNotEmpty())
        <p>Фото по умолчанию:</p>
        @foreach ($recipe->images as $image)
            <label>
                <input type="radio" name="default_image_id" value="{{ $image->id }}" @checked($image->is_default)>
                <img src="{{ asset('storage/'.$image->thumbnail) }}" alt="" width="175">
            </label>
        @endforeach
        <p>Если загрузить новые фото, старые будут заменены.</p>
    @endif

    <p>
        <input type="file" name="images[]" multiple accept=".jpg,.jpeg,.png,.webp"
               class="@if ($errors->has('images') || $errors->has('images.*')) is-invalid @endif">
        @error('images')
            <br><span class="invalid-feedback">{{ $message }}</span>
        @enderror
        @foreach ($errors->get('images.*') as $messages)
            @foreach ($messages as $message)
                <br><span class="invalid-feedback">{{ $message }}</span>
            @endforeach
        @endforeach
    </p>

    <p>
        <label>Номер нового фото по умолчанию</label><br>
        <input type="number" name="default_image" min="1" max="5" value="{{ old('default_image', 1) }}">
    </p>
</fieldset>

<fieldset>
    <legend>Ингредиенты</legend>

    @error('ingredients')
        <p class="invalid-feedback">{{ $message }}</p>
    @enderror

    @foreach ($ingredientRows as $i => $row)
        <p>
            <select name="ingredients[{{ $i }}][id]" class="@error("ingredients.$i.id") is-invalid @enderror">
                <option value="">— ингредиент —</option>
                @foreach ($ingredients as $ingredient)
                    <option value="{{ $ingredient->id }}" @selected($row['id'] == $ingredient->id)>
                        {{ $ingredient->title }} ({{ $ingredient->unit }})
                    </option>
                @endforeach
            </select>
            <input type="text" name="ingredients[{{ $i }}][quantity]" value="{{ $row['quantity'] }}" placeholder="Количество"
                   class="@error("ingredients.$i.quantity") is-invalid @enderror">
            @error("ingredients.$i.id")
                <br><span class="invalid-feedback">{{ $message }}</span>
            @enderror
            @error("ingredients.$i.quantity")
                <br><span class="invalid-feedback">{{ $message }}</span>
            @enderror
        </p>
    @endforeach
</fieldset>
