<?php

declare(strict_types=1);

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Survey;
use App\Models\SurveyResponse;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;

class SurveyResponseController extends Controller
{
    use AuthorizesRequests;

    public function index(Survey $survey): View
    {
        $this->authorize('viewAny', SurveyResponse::class);

        $responses = $survey->responses()
            ->latest()
            ->paginate(20);

        $distribution = $survey->responses()
            ->selectRaw('option_key, COUNT(*) as total')
            ->groupBy('option_key')
            ->pluck('total', 'option_key')
            ->all();

        $total = array_sum($distribution);

        return view('admin.survey-responses.index', [
            'survey'       => $survey,
            'responses'    => $responses,
            'distribution' => $distribution,
            'total'        => $total,
        ]);
    }

    public function show(SurveyResponse $response): View
    {
        $this->authorize('view', $response);

        $response->loadMissing('survey');

        return view('admin.survey-responses.show', [
            'response' => $response,
        ]);
    }

    public function destroy(SurveyResponse $response): RedirectResponse
    {
        $this->authorize('delete', $response);

        $surveyId = $response->survey_id;
        $response->delete();

        return redirect()
            ->route('admin.surveys.responses.index', $surveyId)
            ->with('success', 'Respuesta eliminada.');
    }
}
