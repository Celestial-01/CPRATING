
<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('topics', function ($table) {
            $table->dropUnique('topics_name_unique');
        });

        DB::statement(
            'CREATE UNIQUE INDEX topics_name_active_unique
             ON topics (name)
             WHERE deleted_at IS NULL'
        );
    }

    public function down(): void
    {
        DB::statement('DROP INDEX IF EXISTS topics_name_active_unique');

        Schema::table('topics', function ($table) {
            $table->unique('name', 'topics_name_unique');
        });
    }
};
