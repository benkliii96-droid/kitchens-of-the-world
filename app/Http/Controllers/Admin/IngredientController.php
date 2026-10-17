<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\IngredientRequest;
use App\Models\Ingredient;

class IngredientController extends Controller
{
    public function index()
    {
        $ingredients = Ingredient::orderBy('title')->get();

        return view('admin.ingredients.index', compact('ingredients'));
    }

    public function create()
    {
        return view('admin.ingredients.create');
    }

    public function store(IngredientRequest $request)
    {
        Ingredient::create($request->validated());

        return redirect()->route('admin.ingredients.index')->with('success', 'Ингредиент создан');
    }

    public function show(Ingredient $ingredient)
    {
        return view('admin.ingredients.show', compact('ingredient'));
    }

    public function edit(Ingredient $ingredient)
    {
        return view('admin.ingredients.edit', compact('ingredient'));
    }

    public function update(IngredientRequest $request, Ingredient $ingredient)
    {
        $ingredient->update($request->validated());

        return redirect()->route('admin.ingredients.index')->with('success', 'Ингредиент обновлён');
    }

    public function destroy(Ingredient $ingredient)
    {
        if ($ingredient->recipes()->withTrashed()->exists()) {
            return back()->with('error', 'Нельзя удалить ингредиент, он используется в рецептах');
        }

        $ingredient->delete();

        return redirect()->route('admin.ingredients.index')->with('success', 'Ингредиент удалён');
    }
}
