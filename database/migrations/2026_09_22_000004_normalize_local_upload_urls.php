<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        foreach (['skills' => ['image'], 'projects' => ['desk_img', 'mobile_img']] as $table => $columns) {
            foreach ($columns as $column) {
                foreach (DB::table($table)->whereNotNull($column)->select('id', $column)->cursor() as $row) {
                    $url = $row->$column;
                    $host = parse_url($url, PHP_URL_HOST);
                    $path = parse_url($url, PHP_URL_PATH);
                    if (in_array($host, ['localhost', '127.0.0.1', '::1'], true) && is_string($path) && str_starts_with($path, '/storage/')) {
                        DB::table($table)->where('id', $row->id)->update([$column => $path]);
                    }
                }
            }
        }
    }

    public function down(): void
    {
        // Keep the portable paths; the original local host and port cannot be recovered.
    }
};
