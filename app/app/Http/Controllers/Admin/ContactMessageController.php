<?php

declare(strict_types=1);

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\ContactMessage;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class ContactMessageController extends Controller
{
    use AuthorizesRequests;

    public function index(Request $request): View
    {
        $this->authorize('viewAny', ContactMessage::class);

        $read  = $request->query('read');
        $query = ContactMessage::query()->latest();

        if ($read === '0' || $read === '1') {
            $query->where('read', (bool) (int) $read);
        }

        return view('admin.contact-messages.index', [
            'messages' => $query->paginate(25)->withQueryString(),
            'read'     => $read,
        ]);
    }

    public function show(ContactMessage $message): View
    {
        $this->authorize('view', $message);

        return view('admin.contact-messages.show', [
            'message' => $message,
        ]);
    }

    public function destroy(ContactMessage $message): RedirectResponse
    {
        $this->authorize('delete', $message);

        $message->delete();

        return redirect()
            ->route('admin.contact-messages.index')
            ->with('success', 'Mensaje eliminado.');
    }

    public function markRead(ContactMessage $message): RedirectResponse
    {
        $this->authorize('update', $message);

        $message->update(['read' => ! $message->read]);

        return back()->with('success', $message->read ? 'Mensaje marcado como leído.' : 'Mensaje marcado como no leído.');
    }
}
