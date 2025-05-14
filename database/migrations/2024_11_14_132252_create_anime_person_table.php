<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('anime_person', function (Blueprint $table) {
            $table->foreignUlid('anime_id')->constrained()->cascadeOnDelete();
            $table->foreignUlid('person_id')->constrained()->cascadeOnDelete();
            $table->foreignUlid('voice_person_id')->nullable()->constrained('people')->cascadeOnDelete();
            $table->string('character_name', 128);

            $table->primary(['anime_id', 'person_id']); // Первинний ключ на обидва поля
        });

        DB::unprepared("
                ALTER TABLE anime_person
                ADD COLUMN searchable tsvector GENERATED ALWAYS AS (
                    to_tsvector('ukrainian', character_name)
                ) STORED
            ");

        DB::unprepared('CREATE INDEX anime_person_searchable_idx ON anime_person USING GIN (searchable)');
        DB::unprepared('CREATE INDEX anime_person_trgm_character_name_idx ON anime_person USING GIN (character_name gin_trgm_ops)');
    }

    public function down(): void
    {
        DB::unprepared('DROP INDEX IF EXISTS anime_person_searchable_character_name_idx');
        DB::unprepared('DROP INDEX IF EXISTS anime_person_trgm_character_name_idx');

        Schema::dropIfExists('anime_person');
    }
};
