<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\{
    Classified, ContactMessage, Event, Lodging,
    News, NewsletterSubscriber, SurveyResponse,
};
use Illuminate\View\View;

class DashboardController extends Controller
{
    public function __invoke(): View
    {
        return view('admin.dashboard', [
            'stats' => [
                'News'          => News::count(),
                'Eventos'       => Event::count(),
                'Hospedajes'    => Lodging::count(),
                'Clasificados'  => Classified::count(),
                'Mensajes'      => ContactMessage::where('read', false)->count(),
                'Subscribers'   => NewsletterSubscriber::where('status', 'confirmed')->count(),
                'Responses'     => SurveyResponse::count(),
            ],
        ]);
    }
}
