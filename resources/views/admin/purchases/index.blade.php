@extends('admin.layouts.app')

@section('title', 'Заказы')

@section('content')
    <h1>Заказы</h1>

    @if ($purchases->isEmpty())
        <p>Заказов пока нет</p>
    @else
        <table>
            <tr>
                <th>ID</th>
                <th>Клиент</th>
                <th>Рецепты</th>
                <th>Общая стоимость</th>
                <th>Статус оплаты</th>
                <th>Действия</th>
            </tr>
            @foreach ($purchases as $purchase)
                <tr>
                    <td>{{ $purchase->id }}</td>
                    <td>
                        {{ $purchase->user->email ?? '—' }}<br>
                        {{ $purchase->user->fullname ?? '' }}
                    </td>
                    <td>
                        <ul>
                            <li>
                                {{ $purchase->recipe->title ?? 'Рецепт удалён' }}
                                — {{ $purchase->recipe->price ?? $purchase->total_amount }} ₽
                            </li>
                        </ul>
                    </td>
                    <td>{{ $purchase->total_amount }} ₽</td>
                    <td>{{ $purchase->statusLabel() }}</td>
                    <td>
                        @if ($purchase->canDelete())
                            <form action="{{ route('admin.purchases.destroy', $purchase) }}" method="POST">
                                @csrf
                                @method('DELETE')
                                <button type="submit" onclick="return confirm('Удалить заказ?')">Удалить</button>
                            </form>
                        @else
                            —
                        @endif
                    </td>
                </tr>
            @endforeach
        </table>

        {{ $purchases->links('admin.pagination') }}
    @endif
@endsection
