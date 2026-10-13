<p>
    <label>Рецепт</label><br>
    <select name="recipe_id" class="@error('recipe_id') is-invalid @enderror">
        <option value="">— выберите —</option>
        @foreach ($recipes as $recipe)
            <option value="{{ $recipe->id }}"
                @selected(old('recipe_id', $step->recipe_id ?? '') == $recipe->id)>
                {{ $recipe->title }}
            </option>
        @endforeach
    </select>
    @error('recipe_id')
        <br><span class="invalid-feedback">{{ $message }}</span>
    @enderror
</p>

<p>
    <label>Название шага</label><br>
    <input type="text" name="title" value="{{ old('title', $step->title ?? '') }}"
           class="@error('title') is-invalid @enderror">
    @error('title')
        <br><span class="invalid-feedback">{{ $message }}</span>
    @enderror
</p>

<p>
    <label>Описание шага</label><br>
    <textarea name="description" rows="4" class="@error('description') is-invalid @enderror">{{ old('description', $step->description ?? '') }}</textarea>
    @error('description')
        <br><span class="invalid-feedback">{{ $message }}</span>
    @enderror
</p>

<p>
    <label>Фотография шага</label><br>
    @if (!empty($step?->image))
        <img src="{{ asset('storage/'.$step->image) }}" alt="" width="200"><br>
    @endif
    <input type="file" name="image" accept=".jpg,.jpeg,.png,.webp"
           class="@error('image') is-invalid @enderror">
    @error('image')
        <br><span class="invalid-feedback">{{ $message }}</span>
    @enderror
</p>
