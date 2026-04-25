<?php

declare(strict_types=1);

namespace App\Http\Controllers\Api\V1;

use App\Models\NewsletterSubscriber;
use Illuminate\Http\JsonResponse;

class NewsletterSubscriberController extends Controller
{
    /**
     * Eliminar suscriptor (acción moderable).
     */
    public function destroy(NewsletterSubscriber $subscriber): JsonResponse
    {
        $this->authorize('delete', $subscriber);

        $subscriber->delete();

        return response()->json(null, 204);
    }
}
