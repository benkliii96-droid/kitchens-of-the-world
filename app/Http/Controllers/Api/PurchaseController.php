<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\Api\RecipeIdRequest;
use App\Http\Resources\PurchaseDetailResource;
use App\Http\Resources\PurchaseResource;
use App\Models\Purchase;
use App\Models\Recipe;
use Illuminate\Http\Request;

class PurchaseController extends Controller
{
    public function index(Request $request)
    {
        $purchases = $request->user()->purchases()
            ->with('recipe.defaultImage')
            ->orderByDesc('id')
            ->get();

        $purchases->each->refreshExpiredStatus();

        return PurchaseResource::collection($purchases);
    }

    public function store(RecipeIdRequest $request)
    {
        $recipe = Recipe::find($request->validated('recipe_id'));

        if (!$recipe) {
            return response()->json(['message' => 'Recipe not found'], 404);
        }

        if ($recipe->price <= 0) {
            return response()->json(['message' => 'This recipe is free'], 400);
        }

        $alreadyPaid = $request->user()->purchases()
            ->where('recipe_id', $recipe->id)
            ->where('status', Purchase::STATUS_PAID)
            ->exists();

        if ($alreadyPaid) {
            return response()->json(['message' => 'Recipe already purchased'], 409);
        }

        $purchase = $request->user()->purchases()->create([
            'recipe_id' => $recipe->id,
            'total_amount' => $recipe->price,
            'status' => Purchase::STATUS_PENDING,
            'expires_at' => now()->addMinutes(config('services.payment.expires_minutes')),
        ]);

        return response()->json([
            'purchase_id' => $purchase->id,
            'status' => $purchase->status,
            'payment_link' => $purchase->paymentLink(),
            'expires_at' => $purchase->expires_at,
        ], 201);
    }

    public function show(Request $request, int $id)
    {
        $purchase = $this->findPurchase($request, $id);

        if (!$purchase) {
            return response()->json(['message' => 'Purchase not found'], 404);
        }

        return new PurchaseDetailResource($purchase);
    }

    public function progress(Request $request, int $id)
    {
        $purchase = $this->findPurchase($request, $id);

        if (!$purchase) {
            return response()->json(['message' => 'Purchase not found'], 404);
        }

        $progress = $purchase->stepProgress->keyBy('step_id');

        $steps = $purchase->recipe->steps->map(fn ($step) => [
            'id' => $step->id,
            'title' => $step->title,
            'order' => $step->order,
            'completed' => (bool) $progress->get($step->id)?->completed,
            'completed_at' => $progress->get($step->id)?->completed_at,
        ]);

        $total = $steps->count();
        $completed = $steps->where('completed', true)->count();

        return response()->json([
            'purchase_id' => $purchase->id,
            'recipe_id' => $purchase->recipe->id,
            'recipe_title' => $purchase->recipe->title,
            'total_steps' => $total,
            'completed_steps' => $completed,
            'progress_percent' => $total > 0 ? (int) round($completed / $total * 100) : 0,
            'steps' => $steps,
        ]);
    }

    private function findPurchase(Request $request, int $id): ?Purchase
    {
        $purchase = $request->user()->purchases()
            ->with(['recipe.steps', 'stepProgress'])
            ->find($id);

        $purchase?->refreshExpiredStatus();

        return $purchase;
    }
}
