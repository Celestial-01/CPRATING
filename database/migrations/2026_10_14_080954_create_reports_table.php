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
        Schema::create('reports', function (Blueprint $table) {
            $table->id();

            $table -> foreignId('comment_id')
                ->constrained('comments')
                ->cascadeOnDelete();

            $table -> foreignId('user_id')
                ->constrained('users')
                ->cascadeOnDelete();

            $table -> string('reason');
            $table -> text('details') ->nullable();

            $table->string('status')->default('pending')->index();

            $table->foreignId('handled_by')
                ->nullable()
                ->constrained('users')
                ->nullOnDelete();
            $table->timestamp('handlet_at')->nullable();

            $table->timestamps();

            //รีพอร์ตซ้ำไม่ได้
            $table->unique(['comment_id', 'user_id']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('reports');
    }
};
