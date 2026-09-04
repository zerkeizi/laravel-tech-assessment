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
        Schema::create('users_log', function (Blueprint $table) {
            $table->id();
            // The user row that was changed. Named "subject_id" (rather than
            // the "<table>_id" pattern used by the other *_log tables)
            // because this table also has a `user_id` column for the acting
            // user, and "user_id" for both would be ambiguous.
            $table->foreignId('subject_id')->constrained('users')->cascadeOnDelete();
            $table->unsignedBigInteger('user_id')->nullable();
            $table->json('data');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('users_log');
    }
};
