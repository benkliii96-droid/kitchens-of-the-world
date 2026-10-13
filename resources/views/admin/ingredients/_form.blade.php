@php
    $units = ['шт', 'л', 'кг', 'мг', 'ст.л.'];
@endphp

<p>
    <label>Название ингредиента</label><br>
    <input type="text" name="title" value="{{ old('title', $ingredient->title ?? '') }}"
           class="@error('title') is-invalid @enderror">
    @error('title')
        <br><span class="invalid-feedback">{{ $message }}</span>
    @enderror
</p>

<p>
    <label>Единица измерения</label><br>
    <select name="unit" class="@error('unit') is-invalid @enderror">
        <option value="">— выберите —</option>
        @foreach ($units as $unit)
            <option value="{{ $unit }}" @selected(old('unit', $ingredient->unit ?? '') === $unit)>
                {{ $unit }}
            </option>
        @endforeach
    </select>
    @error('unit')
        <br><span class="invalid-feedback">{{ $message }}</span>
    @enderror
</p>
