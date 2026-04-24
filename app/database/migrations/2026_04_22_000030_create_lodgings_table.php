<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('lodgings', function (Blueprint $table) {
            $table->id();
            $table->string('name', 200);
            $table->string('slug', 255)->unique();
            $table->text('description')->nullable();
            $table->enum('type', ['hotel', 'casa', 'camping', 'hostel', 'other'])->index();
            $table->string('website', 255)->nullable();
            $table->string('email', 200)->nullable();
            $table->string('phone', 100)->nullable();
            $table->string('address', 500)->nullable();
            $table->decimal('latitude', 10, 7)->nullable();
            $table->decimal('longitude', 10, 7)->nullable();
            $table->unsignedInteger('views')->default(0);
            $table->unsignedBigInteger('legacy_id')->nullable()->unique();
            $table->timestamps();
            $table->softDeletes();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('lodgings');
    }
};
