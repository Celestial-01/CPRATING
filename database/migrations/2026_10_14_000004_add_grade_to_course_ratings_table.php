<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('course_ratings', function (Blueprint $table): void {
            $table->string('grade', 2)->nullable();
        });
    }

    public function down(): void
    {
        Schema::table('course_ratings', function (Blueprint $table): void {
            $table->dropColumn('grade');
        });
    }
};
