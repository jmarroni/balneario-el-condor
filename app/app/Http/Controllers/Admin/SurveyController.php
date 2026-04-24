<?php

declare(strict_types=1);

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\StoreSurveyRequest;
use App\Http\Requests\Admin\UpdateSurveyRequest;
use App\Models\Survey;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;

class SurveyController extends Controller
{
    use AuthorizesRequests;

    public function index(): View
    {
        $this->authorize('viewAny', Survey::class);

        $surveys = Survey::query()
            ->latest()
            ->paginate(20);

        return view('admin.surveys.index', compact('surveys'));
    }

    public function create(): View
    {
        $this->authorize('create', Survey::class);

        return view('admin.surveys.create', [
            'survey' => new Survey(['options' => [['key' => 1, 'label' => '']]]),
        ]);
    }

    public function store(StoreSurveyRequest $request): RedirectResponse
    {
        $survey = Survey::create($request->validated());

        return redirect()
            ->route('admin.surveys.edit', $survey)
            ->with('success', 'Encuesta creada.');
    }

    public function show(Survey $survey): RedirectResponse
    {
        $this->authorize('view', $survey);

        return redirect()->route('admin.surveys.edit', $survey);
    }

    public function edit(Survey $survey): View
    {
        $this->authorize('update', $survey);

        return view('admin.surveys.edit', [
            'survey' => $survey,
        ]);
    }

    public function update(UpdateSurveyRequest $request, Survey $survey): RedirectResponse
    {
        $survey->update($request->validated());

        return redirect()
            ->route('admin.surveys.edit', $survey)
            ->with('success', 'Encuesta actualizada.');
    }

    public function destroy(Survey $survey): RedirectResponse
    {
        $this->authorize('delete', $survey);

        $survey->delete();

        return redirect()
            ->route('admin.surveys.index')
            ->with('success', 'Encuesta eliminada.');
    }
}
