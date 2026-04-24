<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('tides', function (Blueprint $table) {
            $table->id();
            $table->string('location', 100)->default('El Cóndor');
            $table->date('date')->index();
            $table->time('first_high')->nullable();
            $table->string('first_high_height', 20)->nullable();
            $table->time('first_low')->nullable();
            $table->string('first_low_height', 20)->nullable();
            $table->time('second_high')->nullable();
            $table->string('second_high_height', 20)->nullable();
            $table->time('second_low')->nullable();
            $table->string('second_low_height', 20)->nullable();
            $table->unsignedBigInteger('legacy_id')->nullable()->unique();
            $table->timestamps();

            $table->unique(['location', 'date']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('tides');
    }
};
