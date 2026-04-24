<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::table('event_registrations', function (Blueprint $table) {
            $table->string('legacy_id', 50)->nullable()->change();
            // index por prefijo + event
            $table->index(['event_id', 'legacy_id']);
        });
    }

    public function down(): void
    {
        // Soltar primero la FK para poder borrar el índice compuesto
        // (MariaDB mantiene el índice porque cubre la columna event_id del FK).
        Schema::table('event_registrations', function (Blueprint $table) {
            $table->dropForeign(['event_id']);
            $table->dropIndex(['event_id', 'legacy_id']);
            $table->unsignedBigInteger('legacy_id')->nullable()->change();
            $table->foreign('event_id')->references('id')->on('events')->cascadeOnDelete();
        });
    }
};
