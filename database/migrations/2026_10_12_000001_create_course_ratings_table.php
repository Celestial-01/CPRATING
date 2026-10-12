<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('course_ratings', function (Blueprint $table): void {
            $table->id();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->string('course_code', 8);
            $table->unsignedTinyInteger('difficulty');
            $table->unsignedTinyInteger('workload');
            $table->unsignedTinyInteger('interest');
            $table->timestamps();

            $table->unique(['user_id', 'course_code']);
            $table->index('course_code');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('course_ratings');
    }
};
