<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\Api\StepCompleteRequest;
use App\Models\Purchase;
use App\Models\Step;
use App\Models\StepProgress;
use Illuminate\Validation\ValidationException;

class StepController extends Controller
{
    public function complete(StepCompleteRequest $request, int $stepId)
    {
        $step = Step::find($stepId);

        if (!$step) {
            return response()->json(['message' => 'Step not found'], 404);
        }

        $purchase = $request->user()->purchases()->find($request->validated('purchase_id'));

        if (!$purchase) {
            return response()->json(['message' => 'Purchase not found'], 404);
        }

        if ((int) $purchase->recipe_id !== (int) $step->recipe_id) {
            throw ValidationException::withMessages([
                'purchase_id' => ['The step does not belong to this purchase'],
            ]);
        }

        $purchase->refreshExpiredStatus();

        if ($purchase->status !== Purchase::STATUS_PAID) {
            return response()->json(['message' => 'Purchase is not paid'], 403);
        }

        $completed = $request->boolean('completed');

        $progress = StepProgress::updateOrCreate(
            ['purchase_id' => $purchase->id, 'step_id' => $step->id],
            ['completed' => $completed, 'completed_at' => $completed ? now() : null],
        );

        return response()->json([
            'success' => true,
            'message' => $completed ? 'Step marked as completed' : 'Step marked as not completed',
            'step' => [
                'id' => $step->id,
                'title' => $step->title,
                'completed' => $progress->completed,
                'completed_at' => $progress->completed_at,
            ],
        ]);
    }
}
