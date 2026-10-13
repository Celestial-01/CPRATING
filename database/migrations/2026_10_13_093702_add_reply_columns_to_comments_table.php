<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('comments', function (Blueprint $table) {
            if (!Schema::hasColumn('comments', 'parent_id')) {
                $table->unsignedBigInteger('parent_id')->nullable()->index();
            }

            if (!Schema::hasColumn('comments', 'reply_to_user_id')) {
                $table->unsignedBigInteger('reply_to_user_id')->nullable();
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('comments', function (Blueprint $table) {
            if (Schema::hasColumn('comments', 'parent_id')) {
                $table->dropIndex(['parent_id']);
            }
        });

        Schema::table('comments', function (Blueprint $table) {
            $columns = array_values(array_filter(
                ['parent_id', 'reply_to_user_id'],
                fn ($column) => Schema::hasColumn('comments', $column)
            ));

            if ($columns) {
                $table->dropColumn($columns);
            }
        });
    }
};