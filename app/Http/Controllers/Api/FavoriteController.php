<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\Api\RecipeIdRequest;
use App\Http\Resources\FavoriteResource;
use App\Models\Recipe;
use Illuminate\Http\Request;

class FavoriteController extends Controller
{
    public function index(Request $request)
    {
        $favorites = $request->user()->favorites()
            ->whereHas('recipe')
            ->with('recipe.defaultImage')
            ->orderByDesc('id')
            ->get();

        return FavoriteResource::collection($favorites);
    }

    public function store(RecipeIdRequest $request)
    {
        $recipe = Recipe::find($request->validated('recipe_id'));

        if (!$recipe) {
            return response()->json(['message' => 'Recipe not found'], 404);
        }

        if ($request->user()->favorites()->where('recipe_id', $recipe->id)->exists()) {
            return response()->json(['message' => 'Recipe already in favorites'], 409);
        }

        $request->user()->favorites()->create(['recipe_id' => $recipe->id]);

        return response()->json([
            'success' => true,
            'message' => 'Recipe added to favorites',
        ], 201);
    }

    public function destroy(Request $request, int $recipeId)
    {
        $favorite = $request->user()->favorites()->where('recipe_id', $recipeId)->first();

        if (!$favorite) {
            return response()->json(['message' => 'Favorite not found'], 404);
        }

        $favorite->delete();

        return response()->json([
            'success' => true,
            'message' => 'Recipe removed from favorites',
        ]);
    }
}
