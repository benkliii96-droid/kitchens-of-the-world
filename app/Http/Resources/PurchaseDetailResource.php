<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class PurchaseDetailResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        $recipe = $this->recipe;
        $progress = $this->stepProgress->keyBy('step_id');

        return [
            'id' => $this->id,
            'recipe' => [
                'id' => $recipe->id,
                'title' => $recipe->title,
                'description' => $recipe->description,
                'price' => (float) $recipe->price,
                'difficulty' => $recipe->difficulty,
                'cooking_time' => $recipe->cooking_time,
                'formatted_time' => $recipe->formatted_time,
                'steps' => $recipe->steps->map(fn ($step) => [
                    'id' => $step->id,
                    'title' => $step->title,
                    'description' => $step->description,
                    'image' => $step->image ? '/storage/' . $step->image : null,
                    'order' => $step->order,
                    'is_completed' => (bool) $progress->get($step->id)?->completed,
                    'completed_at' => $progress->get($step->id)?->completed_at,
                ]),
            ],
            'total_amount' => (float) $this->total_amount,
            'status' => $this->status,
            'paid_at' => $this->paid_at,
            'created_at' => $this->created_at,
        ];
    }
}
