<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('gallery_images', function (Blueprint $table) {
            $table->id();
            $table->string('title', 200)->nullable();
            $table->string('slug', 255)->unique();
            $table->text('description')->nullable();
            $table->string('path', 500);
            $table->string('thumb_path', 500)->nullable();
            $table->string('original_path', 500)->nullable();
            $table->date('taken_on')->nullable();
            $table->unsignedInteger('views')->default(0);
            $table->unsignedBigInteger('legacy_id')->nullable()->unique();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('gallery_images');
    }
};
