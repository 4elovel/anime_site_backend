<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('anime_user_notifications', function (Blueprint $table) {
            $table->foreignUlid('user_id')->constrained()->cascadeOnDelete();
            $table->foreignUlid('anime_id')->constrained()->cascadeOnDelete();
            $table->timestamps();

            $table->primary(['user_id', 'anime_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('anime_user_notifications');
    }
};
