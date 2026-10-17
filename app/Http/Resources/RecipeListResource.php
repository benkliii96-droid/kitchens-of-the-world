<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class RecipeListResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'title' => $this->title,
            'description' => $this->description,
            'cooking_time' => $this->cooking_time,
            'difficulty' => $this->difficulty,
            'price' => (float) $this->price,
            'category' => [
                'id' => $this->category->id,
                'name' => $this->category->name,
            ],
            'default_image' => $this->defaultImage ? '/storage/' . $this->defaultImage->thumbnail : null,
            'formatted_time' => $this->formatted_time,
            'created_at' => $this->created_at,
        ];
    }
}
