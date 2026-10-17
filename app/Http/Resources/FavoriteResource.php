<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class FavoriteResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        $recipe = $this->recipe;

        return [
            'id' => $recipe->id,
            'title' => $recipe->title,
            'description' => $recipe->description,
            'cooking_time' => $recipe->cooking_time,
            'difficulty' => $recipe->difficulty,
            'price' => (float) $recipe->price,
            'default_image' => $recipe->defaultImage ? '/storage/' . $recipe->defaultImage->thumbnail : null,
            'formatted_time' => $recipe->formatted_time,
            'favorited_at' => $this->created_at,
        ];
    }
}
