<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('anime_tag', function (Blueprint $table) {
            $table->foreignUlid('anime_id')->constrained('animes')->onDelete('cascade');
            $table->foreignUlid('tag_id')->constrained('tags')->onDelete('cascade');
            $table->primary(['anime_id', 'tag_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('anime_tag');
    }
};
