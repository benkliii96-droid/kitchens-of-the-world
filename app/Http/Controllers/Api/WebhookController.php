<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\Api\WebhookRequest;
use App\Models\Purchase;

class WebhookController extends Controller
{
    public function __invoke(WebhookRequest $request)
    {
        $data = $request->validated();

        $purchase = Purchase::findByHash($data['purchase_id']);

        if (!$purchase) {
            return response()->json(['message' => 'Purchase not found'], 404);
        }

        $purchase->refreshExpiredStatus();

        if ($purchase->status === Purchase::STATUS_PAID) {
            return response()->json(['message' => 'Recipe already purchased'], 409);
        }

        if ($purchase->status === Purchase::STATUS_FAILED) {
            return response()->json(['message' => "You can't buy a cancelled prescription"], 408);
        }

        if ($purchase->status === Purchase::STATUS_EXPIRED) {
            return response()->json(['message' => 'Time is left'], 419);
        }

        if ($data['status'] === Purchase::STATUS_PAID) {
            $purchase->update([
                'status' => Purchase::STATUS_PAID,
                'paid_at' => now(),
                'expires_at' => null,
            ]);
        } else {
            $purchase->update(['status' => Purchase::STATUS_FAILED]);
        }

        return response()->json(['message' => 'Success']);
    }
}
