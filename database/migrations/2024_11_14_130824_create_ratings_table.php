<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('ratings', function (Blueprint $table) {
            $table->ulid('id')->primary();
            $table->foreignUlid('user_id')->constrained()->cascadeOnDelete();
            $table->foreignUlid('anime_id')->constrained()->cascadeOnDelete();
            $table->unsignedTinyInteger('number')->checkBetween(1, 10);
            $table->text('review')->nullable();
            $table->timestamps();

            $table->unique(['user_id', 'anime_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('ratings');
    }
};
