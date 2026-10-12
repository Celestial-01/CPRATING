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
        Schema::create('course_topic', function (Blueprint $table) {
            $table->foreignId('course_id')
                ->constrained('courses')
                ->cascadeOnDelete();

            $table->foreignId('topic_id')
                ->constrained('topics')
                ->cascadeOnDelete();

            $table->primary(['course_id', 'topic_id']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('course_topic');
    }
};
