<p>
    <label>Название категории</label><br>
    <input type="text" name="name" value="{{ old('name', $category->name ?? '') }}"
           class="@error('name') is-invalid @enderror">
    @error('name')
        <br><span class="invalid-feedback">{{ $message }}</span>
    @enderror
</p>
