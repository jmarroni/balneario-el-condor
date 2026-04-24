<?php

declare(strict_types=1);

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\NewsletterSubscriber;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Http\Request;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;
use Symfony\Component\HttpFoundation\StreamedResponse;

class NewsletterSubscriberController extends Controller
{
    use AuthorizesRequests;

    private const ALLOWED_STATUSES = ['pending', 'confirmed', 'unsubscribed'];

    public function index(Request $request): View
    {
        $this->authorize('viewAny', NewsletterSubscriber::class);

        $status = $request->query('status');
        $q      = trim((string) $request->query('q', ''));
        $query  = NewsletterSubscriber::query()->latest();

        if (is_string($status) && in_array($status, self::ALLOWED_STATUSES, true)) {
            $query->where('status', $status);
        }

        if ($q !== '') {
            $query->where('email', 'like', '%'.$q.'%');
        }

        return view('admin.newsletter-subscribers.index', [
            'subscribers' => $query->paginate(25)->withQueryString(),
            'status'      => $status,
            'q'           => $q,
        ]);
    }

    public function destroy(NewsletterSubscriber $subscriber): RedirectResponse
    {
        $this->authorize('delete', $subscriber);

        $subscriber->delete();

        return redirect()
            ->route('admin.newsletter-subscribers.index')
            ->with('success', 'Suscriptor eliminado.');
    }

    public function export(): StreamedResponse
    {
        $this->authorize('viewAny', NewsletterSubscriber::class);

        $filename = 'subscribers-' . now()->format('Y-m-d') . '.csv';
        $columns  = ['email', 'status', 'subscribed_at', 'confirmed_at', 'unsubscribed_at', 'ip_address'];

        return response()->streamDownload(function () use ($columns): void {
            $handle = fopen('php://output', 'w');
            fputcsv($handle, $columns);

            NewsletterSubscriber::query()
                ->orderBy('id')
                ->chunk(500, function ($chunk) use ($handle, $columns): void {
                    foreach ($chunk as $subscriber) {
                        fputcsv($handle, [
                            $subscriber->email,
                            $subscriber->status,
                            optional($subscriber->subscribed_at)->toIso8601String(),
                            optional($subscriber->confirmed_at)->toIso8601String(),
                            optional($subscriber->unsubscribed_at)->toIso8601String(),
                            $subscriber->ip_address,
                        ]);
                    }
                });

            fclose($handle);
        }, $filename, [
            'Content-Type' => 'text/csv; charset=UTF-8',
        ]);
    }
}
