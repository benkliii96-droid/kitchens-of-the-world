<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\StepRequest;
use App\Models\Recipe;
use App\Models\Step;
use Illuminate\Support\Facades\Storage;

class StepController extends Controller
{
    public function index()
    {
        $steps = Step::with('recipe')->orderBy('id', 'desc')->get();

        return view('admin.steps.index', compact('steps'));
    }

    public function create()
    {
        $recipes = Recipe::orderBy('title')->get();

        return view('admin.steps.create', compact('recipes'));
    }

    public function store(StepRequest $request)
    {
        $data = $request->validated();
        $recipe = Recipe::findOrFail($data['recipe_id']);

        $path = $request->file('image')->store('steps', 'public');

        $order = ($recipe->steps()->max('order') ?? 0) + 1;

        $recipe->steps()->create([
            'title' => $data['title'],
            'description' => $data['description'],
            'image' => $path,
            'order' => $order,
        ]);

        return redirect()->route('admin.steps.index')->with('success', 'Шаг создан');
    }

    public function show(Step $step)
    {
        $step->load('recipe');

        return view('admin.steps.show', compact('step'));
    }

    public function edit(Step $step)
    {
        $recipes = Recipe::orderBy('title')->get();

        return view('admin.steps.edit', compact('step', 'recipes'));
    }

    public function update(StepRequest $request, Step $step)
    {
        $data = $request->validated();

        if ($request->hasFile('image')) {
            if ($step->image) {
                Storage::disk('public')->delete($step->image);
            }
            $data['image'] = $request->file('image')->store('steps', 'public');
        } else {
            unset($data['image']);
        }

        $step->update($data);

        return redirect()->route('admin.steps.index')->with('success', 'Шаг обновлён');
    }

    public function destroy(Step $step)
    {
        if ($step->recipe && $step->recipe->steps()->count() <= 1) {
            return back()->with('error', 'У рецепта должен остаться хотя бы один шаг');
        }

        if ($step->image) {
            Storage::disk('public')->delete($step->image);
        }

        $step->delete();

        return redirect()->route('admin.steps.index')->with('success', 'Шаг удалён');
    }
}
