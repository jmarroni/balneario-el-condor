<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('classified_contacts', function (Blueprint $table) {
            $table->id();
            $table->foreignId('classified_id')->constrained()->cascadeOnDelete();
            $table->string('contact_name', 100);
            $table->string('contact_email', 200);
            $table->string('contact_phone', 100)->nullable();
            $table->text('message')->nullable();
            $table->string('destination_email', 200)->nullable();  // a quién se reenvió
            $table->string('ip_address', 45)->nullable();
            $table->unsignedBigInteger('legacy_id')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('classified_contacts');
    }
};
