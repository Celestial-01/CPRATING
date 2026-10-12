<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (Schema::hasColumn('courses', 'is_active') && ! Schema::hasColumn('courses', 'is_offered')) {
            Schema::table('courses', function (Blueprint $table): void {
                $table->renameColumn('is_active', 'is_offered');
            });
        }

        if (! Schema::hasColumn('courses', 'is_offered')) {
            Schema::table('courses', function (Blueprint $table): void {
                $table->boolean('is_offered')->default(true)->index();
            });
        }

        if (! Schema::hasColumn('courses', 'publication_status')) {
            Schema::table('courses', function (Blueprint $table): void {
                $table->string('publication_status', 20)->default('published')->index();
            });
        }
    }

    public function down(): void
    {
        if (Schema::hasColumn('courses', 'publication_status')) {
            Schema::table('courses', function (Blueprint $table): void {
                $table->dropColumn('publication_status');
            });
        }

        if (Schema::hasColumn('courses', 'is_offered') && ! Schema::hasColumn('courses', 'is_active')) {
            Schema::table('courses', function (Blueprint $table): void {
                $table->renameColumn('is_offered', 'is_active');
            });
        }
    }
};
