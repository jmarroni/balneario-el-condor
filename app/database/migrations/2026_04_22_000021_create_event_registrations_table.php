<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('event_registrations', function (Blueprint $table) {
            $table->id();
            $table->foreignId('event_id')->constrained()->cascadeOnDelete();
            $table->string('name', 200);
            $table->string('last_name', 200)->nullable();
            $table->string('email', 200)->nullable()->index();
            $table->string('phone', 100)->nullable();
            $table->string('province', 100)->nullable();
            $table->string('city', 200)->nullable();
            $table->json('extra_data')->nullable();  // campos custom por evento (concursantes, cena, etc.)
            $table->text('comments')->nullable();
            $table->string('ip_address', 45)->nullable();
            $table->unsignedBigInteger('legacy_id')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('event_registrations');
    }
};
