<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Purchase;

class PurchaseController extends Controller
{
    public function index()
    {
        $purchases = Purchase::with(['user', 'recipe'])
            ->orderByDesc('created_at')
            ->paginate(5);

        foreach ($purchases as $purchase) {
            $purchase->refreshExpiredStatus();
        }

        return view('admin.purchases.index', compact('purchases'));
    }

    public function destroy(Purchase $purchase)
    {
        $purchase->refreshExpiredStatus();

        if (!$purchase->canDelete()) {
            return back()->with('error', 'Можно удалить только заказы с ошибкой оплаты или с истёкшим временем');
        }

        $purchase->delete();

        return redirect()->route('admin.purchases.index')->with('success', 'Заказ удалён');
    }
}
