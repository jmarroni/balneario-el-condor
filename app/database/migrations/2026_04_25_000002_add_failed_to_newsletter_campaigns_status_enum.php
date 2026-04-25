<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration {
    public function up(): void
    {
        DB::statement(
            "ALTER TABLE newsletter_campaigns "
            . "MODIFY COLUMN status ENUM('draft','sending','sent','failed') "
            . "NOT NULL DEFAULT 'draft'"
        );
    }

    public function down(): void
    {
        DB::statement(
            "ALTER TABLE newsletter_campaigns "
            . "MODIFY COLUMN status ENUM('draft','sending','sent') "
            . "NOT NULL DEFAULT 'draft'"
        );
    }
};
