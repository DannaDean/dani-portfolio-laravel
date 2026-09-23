<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        if (! app()->environment('production')) {
            return;
        }

        if (DB::table('projects')->exists() || DB::table('skills')->exists() || DB::table('facts')->exists()) {
            return;
        }

        $content = json_decode(
            file_get_contents(database_path('data/portfolio-content.json')),
            true,
            flags: JSON_THROW_ON_ERROR
        );

        foreach (['projects', 'skills', 'facts'] as $table) {
            if ($content[$table] !== []) {
                DB::table($table)->insert($content[$table]);
            }
        }
    }

    public function down(): void
    {
        // Production portfolio content is intentionally preserved on rollback.
    }
};
