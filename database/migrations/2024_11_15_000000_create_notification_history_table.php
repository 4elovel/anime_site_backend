<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (!Schema::hasTable('notification_histories')) {
            Schema::create('notification_histories', function (Blueprint $table) {
                $table->ulid('id')->primary();
                $table->foreignUlid('user_id')->constrained()->cascadeOnDelete();
                $table->string('notifiable_type');
                $table->string('notifiable_id');
                $table->string('type');
                $table->json('data');
                $table->timestamp('read_at')->nullable();
                $table->timestamps();
                
                $table->index(['notifiable_type', 'notifiable_id']);
            });
        }
    }

    public function down(): void
    {
        Schema::dropIfExists('notification_histories');
    }
};