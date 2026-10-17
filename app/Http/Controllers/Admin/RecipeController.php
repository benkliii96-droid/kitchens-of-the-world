<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\RecipeFilterRequest;
use App\Http\Requests\Admin\RecipeRequest;
use App\Models\Category;
use App\Models\Ingredient;
use App\Models\Recipe;
use App\Models\RecipeImage;
use App\Services\RecipeImageService;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;

class RecipeController extends Controller
{
    public function __construct(private RecipeImageService $images)
    {
    }

    public function index(RecipeFilterRequest $request)
    {
        $filters = $request->validated();

        $query = Recipe::withTrashed()->with(['category', 'defaultImage']);

        if (!empty($filters['difficulty'])) {
            $query->where('difficulty', $filters['difficulty']);
        }

        if (!empty($filters['category_id'])) {
            $query->where('category_id', $filters['category_id']);
        }

        if (!empty($filters['time'])) {
            if ($filters['time'] === 'to15') {
                $query->where('cooking_time', '<=', 15);
            } elseif ($filters['time'] === '60-120') {
                $query->whereBetween('cooking_time', [60, 120]);
            } elseif ($filters['time'] === 'from120') {
                $query->where('cooking_time', '>', 120);
            }
        }

        if (!empty($filters['price'])) {
            if ($filters['price'] === 'free') {
                $query->where('price', 0);
            } else {
                $query->where('price', '>', 0);
            }
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
            $query->orderBy('cooking_time');
        } elseif ($sort === 'time_desc') {
            $query->orderByDesc('cooking_time');
        } else {
            $query->orderByDesc('created_at')->orderByDesc('id');
        }

        $recipes = $query->paginate(5)->withQueryString();
        $categories = Category::orderBy('name')->get();

        return view('admin.recipes.index', compact('recipes', 'categories', 'sort'));
    }

    public function create()
    {
        $categories = Category::orderBy('name')->get();
        $ingredients = Ingredient::orderBy('title')->get();

        return view('admin.recipes.create', compact('categories', 'ingredients'));
    }

    public function store(RecipeRequest $request)
    {
        $data = $request->validated();

        DB::transaction(function () use ($request, $data) {
            $category = Category::findOrFail($data['category_id']);

            $recipe = $category->recipes()->create([
                'title' => $data['title'],
                'description' => $data['description'],
                'cooking_time' => $data['cooking_time'],
                'difficulty' => $data['difficulty'],
                'price' => $data['price'] ?? 0,
            ]);

            $defaultIndex = ($data['default_image'] ?? 1) - 1;
            $this->images->addImages($recipe, $request->file('images'), $defaultIndex);

            $this->syncIngredients($recipe, $data['ingredients']);

            $order = 1;
            foreach ($data['steps'] as $index => $step) {
                $recipe->steps()->create([
                    'title' => $step['title'],
                    'description' => $step['description'],
                    'image' => $request->file("steps.$index.image")->store('steps', 'public'),
                    'order' => $order++,
                ]);
            }
        });

        return redirect()->route('admin.recipes.index')->with('success', 'Рецепт создан');
    }

    public function show(Recipe $recipe)
    {
        $recipe->load(['category', 'images', 'ingredients', 'steps']);

        return view('admin.recipes.show', compact('recipe'));
    }

    public function edit(Recipe $recipe)
    {
        $recipe->load(['images', 'ingredients', 'steps']);
        $categories = Category::orderBy('name')->get();
        $ingredients = Ingredient::orderBy('title')->get();

        return view('admin.recipes.edit', compact('recipe', 'categories', 'ingredients'));
    }

    public function update(RecipeRequest $request, Recipe $recipe)
    {
        $data = $request->validated();

        DB::transaction(function () use ($request, $data, $recipe) {
            $recipe->update([
                'title' => $data['title'],
                'description' => $data['description'],
                'cooking_time' => $data['cooking_time'],
                'difficulty' => $data['difficulty'],
                'category_id' => $data['category_id'],
                'price' => $data['price'] ?? 0,
            ]);

            if ($request->hasFile('images')) {
                $this->images->deleteAllImages($recipe);
                $defaultIndex = ($data['default_image'] ?? 1) - 1;
                $this->images->addImages($recipe, $request->file('images'), $defaultIndex);
            } else {
                if (!empty($data['default_image_id'])) {
                    $recipe->images()->update(['is_default' => false]);
                    $recipe->images()->where('id', $data['default_image_id'])->update(['is_default' => true]);
                }

                if ($recipe->wasChanged('title')) {
                    $this->images->refreshWatermarks($recipe->load('images'));
                }
            }

            $this->syncIngredients($recipe, $data['ingredients']);
        });

        return redirect()->route('admin.recipes.index')->with('success', 'Рецепт обновлён');
    }

    public function destroy(Recipe $recipe)
    {
        $recipe->delete();

        return redirect()->route('admin.recipes.index')->with('success', 'Рецепт помечен на удаление');
    }

    public function restore(Recipe $recipe)
    {
        $recipe->restore();

        return redirect()->route('admin.recipes.index')->with('success', 'Рецепт восстановлен');
    }

    public function forceDelete(Recipe $recipe)
    {
        foreach ($recipe->steps as $step) {
            if ($step->image) {
                Storage::disk('public')->delete($step->image);
            }
        }

        $this->images->deleteFolder($recipe);
        $recipe->forceDelete();

        return redirect()->route('admin.recipes.index')->with('success', 'Рецепт удалён окончательно');
    }

    public function destroyImage(Recipe $recipe, RecipeImage $image)
    {
        if ($image->recipe_id !== $recipe->id) {
            abort(404);
        }

        if ($recipe->images()->count() <= 1) {
            return back()->with('error', 'У рецепта должно остаться хотя бы одно фото');
        }

        $wasDefault = $image->is_default;
        $this->images->deleteImage($image);

        if ($wasDefault) {
            $recipe->images()->first()->update(['is_default' => true]);
        }

        return back()->with('success', 'Фото удалено');
    }

    private function syncIngredients(Recipe $recipe, array $rows): void
    {
        $sync = [];

        foreach ($rows as $row) {
            $ingredient = Ingredient::find($row['id']);
            $sync[$ingredient->id] = [
                'quantity' => $row['quantity'],
                'unit' => $ingredient->unit,
            ];
        }

        $recipe->ingredients()->sync($sync);
    }
}
