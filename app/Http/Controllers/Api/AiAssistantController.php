<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Services\AiAssistantService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class AiAssistantController extends Controller
{
    protected AiAssistantService $aiService;

    public function __construct(AiAssistantService $aiService)
    {
        $this->aiService = $aiService;
    }

    /**
     * Send user message to AI Copilot and get contextual response.
     */
    public function chat(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'prompt' => 'required|string|max:1000',
            'history' => 'nullable|array',
            'history.*.sender' => 'required_with:history|string|in:user,ai,assistant,model',
            'history.*.text' => 'required_with:history|string',
            'lang' => 'nullable|string|in:id,en',
        ]);

        $prompt = $validated['prompt'];
        $history = $validated['history'] ?? [];
        $lang = $validated['lang'] ?? $request->input('lang', 'id');

        $result = $this->aiService->generateAnswer($prompt, $history, $lang);

        return response()->json($result);
    }

    /**
     * Get dynamic quick question suggestions based on current greenhouse state.
     */
    public function suggestions(Request $request): JsonResponse
    {
        $lang = $request->query('lang', 'id');
        $data = $this->aiService->getDynamicSuggestions($lang);

        return response()->json($data);
    }

    /**
     * Get real-time sensor & batch context snapshot that feeds into AI.
     */
    public function context(): JsonResponse
    {
        $context = $this->aiService->buildRealtimeContext();

        return response()->json([
            'success' => true,
            'context' => $context['summary'],
        ]);
    }
}
