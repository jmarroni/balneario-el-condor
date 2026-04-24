<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('rentals', function (Blueprint $table) {
            $table->id();
            $table->string('title', 200);
            $table->string('slug', 255)->unique();
            $table->unsignedInteger('places')->nullable();   // plazas
            $table->string('contact_name', 200)->nullable();
            $table->string('phone', 100)->nullable();
            $table->string('email', 200)->nullable();
            $table->string('address', 500)->nullable();
            $table->text('description')->nullable();
            $table->unsignedBigInteger('legacy_id')->nullable()->unique();
            $table->timestamps();
            $table->softDeletes();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('rentals');
    }
};
