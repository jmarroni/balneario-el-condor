<?php

declare(strict_types=1);

namespace App\Http\Controllers\Api\V1;

use App\Http\Requests\Api\V1\StoreContactMessageRequest;
use App\Models\ContactMessage;
use Illuminate\Http\JsonResponse;

/**
 * @group Endpoints públicos
 *
 * Endpoints públicos de la API (sin auth).
 * Pensado para apps externas que necesiten enviar formularios al sitio.
 */
class PublicApiController extends Controller
{
    /**
     * Enviar mensaje de contacto
     *
     * Recibe un formulario de contacto público y lo persiste para que el equipo lo modere.
     * Rate limit: 10 requests por minuto por IP.
     *
     * @unauthenticated
     *
     * @bodyParam name string required Nombre del remitente. Example: Juan Pérez
     * @bodyParam email string required Email de contacto. Example: juan@example.com
     * @bodyParam phone string Teléfono opcional. Example: +54 9 11 1234-5678
     * @bodyParam subject string Asunto del mensaje. Example: Consulta sobre alojamientos
     * @bodyParam message string required Cuerpo del mensaje. Example: Quiero información para enero.
     */
    public function contact(StoreContactMessageRequest $request): JsonResponse
    {
        $message = ContactMessage::create([
            'name'       => $request->string('name')->toString(),
            'email'      => $request->string('email')->toString(),
            'phone'      => $request->input('phone'),
            'subject'    => $request->input('subject'),
            'message'    => $request->string('message')->toString(),
            'ip_address' => $request->ip(),
            'read'       => false,
        ]);

        // TODO Task 6: Mail::to($admin)->queue(new ContactMessageReceivedMail($message));

        return response()->json([
            'data' => [
                'id'          => $message->id,
                'received_at' => $message->created_at->toIso8601String(),
            ],
            'meta' => [
                'message' => 'Mensaje recibido. Te respondemos pronto.',
            ],
        ], 201);
    }
}
