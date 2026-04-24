<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('contact_messages', function (Blueprint $table) {
            $table->id();
            $table->string('name', 200);
            $table->string('email', 200);
            $table->string('phone', 100)->nullable();
            $table->string('subject', 300)->nullable();
            $table->text('message');
            $table->string('ip_address', 45)->nullable();
            $table->boolean('read')->default(false)->index();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('contact_messages');
    }
};
