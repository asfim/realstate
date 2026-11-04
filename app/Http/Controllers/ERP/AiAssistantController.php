<?php

namespace App\Http\Controllers\ERP;

use App\Http\Controllers\Controller;
use App\Services\ERP\CompanyBranchContext;
use App\Services\ERP\ErpAiAgentService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class AiAssistantController extends Controller
{
    public function __construct(
        protected ErpAiAgentService $aiService,
        protected CompanyBranchContext $context
    ) {}

    /**
     * Display the AI Assistant conversational interface.
     */
    public function index(): View
    {
        $currentCompany = $this->context->currentCompany();
        $currentBranch = $this->context->currentBranch();

        $initialOverview = $this->aiService->fetchMetricsForIntent('company_overview', $currentCompany?->id, $currentBranch?->id);
        $suggestions = $this->aiService->getDefaultSuggestions();

        return view('erp.ai.index', [
            'overview' => $initialOverview,
            'suggestions' => $suggestions,
            'currentCompany' => $currentCompany,
            'currentBranch' => $currentBranch,
        ]);
    }

    /**
     * Process an AI query via AJAX.
     */
    public function query(Request $request): JsonResponse
    {
        $request->validate([
            'query' => 'required|string|max:1000',
        ]);

        $currentCompany = $this->context->currentCompany();
        $currentBranch = $this->context->currentBranch();

        $result = $this->aiService->ask(
            $request->input('query'),
            $currentCompany?->id,
            $currentBranch?->id
        );

        return response()->json([
            'success' => true,
            'data' => $result,
        ]);
    }

    /**
     * Quick stats summary for floating drawer / widget.
     */
    public function quickOverview(): JsonResponse
    {
        $currentCompany = $this->context->currentCompany();
        $currentBranch = $this->context->currentBranch();

        $metrics = $this->aiService->fetchMetricsForIntent('company_overview', $currentCompany?->id, $currentBranch?->id);

        return response()->json([
            'success' => true,
            'metrics' => $metrics,
        ]);
    }
}

