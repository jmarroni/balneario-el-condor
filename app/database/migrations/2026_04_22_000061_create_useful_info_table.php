<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('useful_info', function (Blueprint $table) {
            $table->id();
            $table->string('title', 200);
            $table->string('phone', 100)->nullable();
            $table->string('website', 255)->nullable();
            $table->string('email', 200)->nullable();
            $table->string('address', 500)->nullable();
            $table->decimal('latitude', 10, 7)->nullable();
            $table->decimal('longitude', 10, 7)->nullable();
            $table->unsignedInteger('sort_order')->default(0)->index();
            $table->unsignedBigInteger('legacy_id')->nullable()->unique();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('useful_info');
    }
};
