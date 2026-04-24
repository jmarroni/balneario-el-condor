<?php

declare(strict_types=1);

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\StoreNewsletterCampaignRequest;
use App\Http\Requests\Admin\UpdateNewsletterCampaignRequest;
use App\Jobs\SendCampaign;
use App\Models\NewsletterCampaign;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Bus;
use Illuminate\View\View;

class NewsletterCampaignController extends Controller
{
    use AuthorizesRequests;

    public function index(): View
    {
        $this->authorize('viewAny', NewsletterCampaign::class);

        $campaigns = NewsletterCampaign::query()
            ->latest()
            ->paginate(20);

        return view('admin.newsletter-campaigns.index', compact('campaigns'));
    }

    public function create(): View
    {
        $this->authorize('create', NewsletterCampaign::class);

        return view('admin.newsletter-campaigns.create', [
            'campaign' => new NewsletterCampaign(),
        ]);
    }

    public function store(StoreNewsletterCampaignRequest $request): RedirectResponse
    {
        $data = $request->validated();
        $data['created_by_user_id'] = $request->user()?->id;
        $data['status'] = $data['status'] ?? 'draft';

        $campaign = NewsletterCampaign::create($data);

        return redirect()
            ->route('admin.newsletter-campaigns.edit', $campaign)
            ->with('success', 'Campaña creada.');
    }

    public function show(NewsletterCampaign $campaign): View
    {
        $this->authorize('view', $campaign);

        return view('admin.newsletter-campaigns.show', [
            'campaign' => $campaign,
        ]);
    }

    public function edit(NewsletterCampaign $campaign): View
    {
        $this->authorize('update', $campaign);

        return view('admin.newsletter-campaigns.edit', [
            'campaign' => $campaign,
        ]);
    }

    public function update(UpdateNewsletterCampaignRequest $request, NewsletterCampaign $campaign): RedirectResponse
    {
        $campaign->update($request->validated());

        return redirect()
            ->route('admin.newsletter-campaigns.edit', $campaign)
            ->with('success', 'Campaña actualizada.');
    }

    public function destroy(NewsletterCampaign $campaign): RedirectResponse
    {
        $this->authorize('delete', $campaign);

        $campaign->delete();

        return redirect()
            ->route('admin.newsletter-campaigns.index')
            ->with('success', 'Campaña eliminada.');
    }

    public function send(NewsletterCampaign $campaign): RedirectResponse
    {
        $this->authorize('update', $campaign);

        Bus::dispatch(new SendCampaign($campaign));

        return redirect()
            ->route('admin.newsletter-campaigns.edit', $campaign)
            ->with('success', 'Envío de campaña encolado.');
    }
}
