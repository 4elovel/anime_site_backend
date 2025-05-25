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
        Schema::table('personal_access_tokens', function (Blueprint $table) {
            // Видаляємо старі індекси та зовнішні ключі
            $table->dropMorphs('tokenable');
            
            // Додаємо нові колонки з правильним типом даних
            $table->string('tokenable_id')->after('id');
            $table->string('tokenable_type')->after('tokenable_id');
            
            // Додаємо індекс
            $table->index(['tokenable_id', 'tokenable_type']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('personal_access_tokens', function (Blueprint $table) {
            // Видаляємо нові колонки
            $table->dropIndex(['tokenable_id', 'tokenable_type']);
            $table->dropColumn(['tokenable_id', 'tokenable_type']);
            
            // Повертаємо старі колонки
            $table->morphs('tokenable');
        });
    }
};
