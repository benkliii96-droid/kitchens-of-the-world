@extends('admin.layouts.app')

@section('title', 'Вход')

@section('content')
    <h1>Вход</h1>

    <form action="{{ route('admin.login.submit') }}" method="POST">
        @csrf

        <p>
            <label>Электронная почта</label><br>
            <input type="text" name="email" value="{{ old('email') }}" class="@error('email') is-invalid @enderror">
            @error('email')
                <br><span class="invalid-feedback">{{ $message }}</span>
            @enderror
        </p>

        <p>
            <label>Пароль</label><br>
            <input type="password" name="password" class="@error('password') is-invalid @enderror">
            @error('password')
                <br><span class="invalid-feedback">{{ $message }}</span>
            @enderror
        </p>

        <button type="submit">Войти</button>
    </form>
@endsection