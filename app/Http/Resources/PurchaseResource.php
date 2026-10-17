<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class PurchaseResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        $recipe = $this->recipe;

        return [
            'id' => $this->id,
            'recipe' => [
                'id' => $recipe->id,
                'title' => $recipe->title,
                'price' => (float) $recipe->price,
                'default_image' => $recipe->defaultImage ? '/storage/' . $recipe->defaultImage->thumbnail : null,
            ],
            'total_amount' => (float) $this->total_amount,
            'status' => $this->status,
            'paid_at' => $this->paid_at,
            'expires_at' => $this->expires_at,
            'created_at' => $this->created_at,
        ];
    }
}
