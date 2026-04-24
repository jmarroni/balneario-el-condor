<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('survey_responses', function (Blueprint $table) {
            $table->id();
            $table->foreignId('survey_id')->constrained()->cascadeOnDelete();
            $table->unsignedInteger('option_key');
            $table->text('comment')->nullable();
            $table->string('email', 200)->nullable();
            $table->boolean('accepted_terms')->default(false);
            $table->string('ip_address', 45)->nullable();
            $table->unsignedBigInteger('legacy_id')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('survey_responses');
    }
};
