<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('study_plans', function (Blueprint $table): void {
            $table->id();
            $table->string('major_code', 10);
            $table->string('name');
            $table->string('image')->nullable();
            $table->unsignedSmallInteger('academic_year_start');
            $table->boolean('is_published')->default(false);
            $table->timestamps();

            $table->unique(['major_code', 'academic_year_start']);
        });

        Schema::create('study_plan_courses', function (Blueprint $table): void {
            $table->id();
            $table->foreignId('study_plan_id')->constrained()->cascadeOnDelete();
            $table->foreignId('course_id')->constrained()->restrictOnDelete();
            $table->unsignedTinyInteger('year');
            $table->unsignedTinyInteger('term');
            $table->unsignedSmallInteger('position');
            $table->timestamps();

            $table->unique(['study_plan_id', 'course_id']);
            $table->unique(['study_plan_id', 'year', 'term', 'position']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('study_plan_courses');
        Schema::dropIfExists('study_plans');
    }
};
