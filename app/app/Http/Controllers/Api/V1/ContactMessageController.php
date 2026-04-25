<?php

declare(strict_types=1);

namespace App\Http\Controllers\Api\V1;

use App\Models\ContactMessage;
use Illuminate\Http\JsonResponse;

class ContactMessageController extends Controller
{
    /**
     * Marcar un mensaje como leído.
     * Requiere contact_messages.update — moderator NO tiene este permiso.
     */
    public function markRead(ContactMessage $message): JsonResponse
    {
        $this->authorize('update', $message);

        $message->update(['read' => true]);

        return response()->json([
            'data' => [
                'id'   => $message->id,
                'read' => true,
            ],
            'meta' => [],
        ]);
    }

    /**
     * Eliminar un mensaje (acción moderable).
     */
    public function destroy(ContactMessage $message): JsonResponse
    {
        $this->authorize('delete', $message);

        $message->delete();

        return response()->json(null, 204);
    }
}
