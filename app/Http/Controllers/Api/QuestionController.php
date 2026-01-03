<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Question;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class QuestionController extends Controller
{
    /**
     * Get all active questions with options
     * Used by mobile app to fetch question configuration
     */
    public function index(Request $request): JsonResponse
    {
        $type = $request->query('type'); // Optional filter: garage, fournisseur

        $query = Question::with('options')
            ->active()
            ->ordered();

        if ($type) {
            $query->forType($type);
        }

        $questions = $query->get()->map(function ($question) {
            return [
                'code' => $question->code,
                'establishment_type' => $question->establishment_type,
                'options' => $question->options->map(function ($option) {
                    return [
                        'code' => $option->code,
                    ];
                })->toArray(),
            ];
        });

        return response()->json([
            'success' => true,
            'questions' => $questions,
        ]);
    }
}
