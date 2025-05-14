<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('comments', function (Blueprint $table) {
            $table->ulid('id')->primary();
            $table->ulidMorphs('commentable'); // Поліморфний зв'язок (коментар може бути до різних моделей)
            $table->foreignUlid('user_id')->constrained()->cascadeOnDelete(); // Зовнішній ключ на користувача
            $table->boolean('is_spoiler')->default(false);
            $table->text('body'); // Текст коментаря
            $table->timestamps();
        });

    }

    public function down(): void
    {

        Schema::dropIfExists('comment_like');
        Schema::dropIfExists('comment_report');
        Schema::dropIfExists('comments');
    }
};
