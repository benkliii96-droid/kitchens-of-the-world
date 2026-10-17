<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class RecipeResource extends JsonResource
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
            'images' => $this->images->map(fn ($image) => [
                'id' => $image->id,
                'path' => '/storage/' . $image->path,
                'thumbnail' => '/storage/' . $image->thumbnail,
                'is_default' => $image->is_default,
            ]),
            'ingredients' => $this->ingredients->map(fn ($ingredient) => [
                'id' => $ingredient->id,
                'title' => $ingredient->title,
                'pivot' => [
                    'quantity' => fmod((float) $ingredient->pivot->quantity, 1) == 0
                        ? (int) $ingredient->pivot->quantity
                        : (float) $ingredient->pivot->quantity,
                    'unit' => $ingredient->pivot->unit,
                ],
            ]),
            'steps' => $this->steps->map(fn ($step) => [
                'id' => $step->id,
                'title' => $step->title,
                'description' => $step->description,
                'image' => $step->image ? '/storage/' . $step->image : null,
                'order' => $step->order,
            ]),
            'formatted_time' => $this->formatted_time,
            'created_at' => $this->created_at,
        ];
    }
}
