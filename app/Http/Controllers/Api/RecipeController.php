<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\Api\RecipeIndexRequest;
use App\Http\Resources\RecipeListResource;
use App\Http\Resources\RecipeResource;
use App\Models\Recipe;

class RecipeController extends Controller
{
    public function index(RecipeIndexRequest $request)
    {
        $filters = $request->validated();

        $query = Recipe::with(['category', 'defaultImage']);

        if (!empty($filters['category_id'])) {
            $query->where('category_id', $filters['category_id']);
        }

        if (!empty($filters['difficulty'])) {
            $query->where('difficulty', $filters['difficulty']);
        }

        if (isset($filters['time_min'])) {
            $query->where('cooking_time', '>=', $filters['time_min']);
        }

        if (!empty($filters['time_max'])) {
            $query->where('cooking_time', '<=', $filters['time_max']);
        }

        if (!empty($filters['price_min'])) {
            $query->where('price', '>=', $filters['price_min']);
        }

        if (!empty($filters['price_max'])) {
            $query->where('price', '<=', $filters['price_max']);
        }

        $sort = $filters['sort'] ?? 'newest_desc';

        if ($sort === 'newest_asc') {
            $query->orderBy('created_at')->orderBy('id');
        } elseif ($sort === 'time_asc') {
            $query->orderBy('cooking_time')->orderBy('id');
        } elseif ($sort === 'time_desc') {
            $query->orderByDesc('cooking_time')->orderByDesc('id');
        } else {
            $query->orderByDesc('created_at')->orderByDesc('id');
        }

        $recipes = $query->paginate((int) ($filters['per_page'] ?? 5))
            ->withQueryString()
            ->through(fn (Recipe $recipe) => (new RecipeListResource($recipe))->resolve($request));

        $response = $recipes->toArray();
        unset($response['links']);

        return response()->json($response);
    }

    public function show(int $id)
    {
        $recipe = Recipe::with(['category', 'images', 'ingredients', 'steps'])->find($id);

        if (!$recipe) {
            return response()->json(['message' => 'Recipe not found'], 404);
        }

        return new RecipeResource($recipe);
    }
}
