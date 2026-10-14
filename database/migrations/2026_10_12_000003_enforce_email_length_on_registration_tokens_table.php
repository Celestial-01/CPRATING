<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('registration_tokens', function (Blueprint $table): void {
            $table->dropUnique(['email']);
        });

        Schema::table('registration_tokens', function (Blueprint $table): void {
            $table->string('email', 255)->change();
        });

        Schema::table('registration_tokens', function (Blueprint $table): void {
            $table->unique('email');
        });
    }

    public function down(): void
    {
        Schema::table('registration_tokens', function (Blueprint $table): void {
            $table->dropUnique(['email']);
        });

        Schema::table('registration_tokens', function (Blueprint $table): void {
            $table->string('email')->change();
        });

        Schema::table('registration_tokens', function (Blueprint $table): void {
            $table->unique('email');
        });
    }
};
