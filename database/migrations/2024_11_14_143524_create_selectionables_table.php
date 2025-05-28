<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('selectionables', function (Blueprint $table) {
            $table->ulid('selection_id');
            $table->ulidMorphs('selectionable');
            $table->timestamps();
            $table->primary(['selection_id', 'selectionable_id', 'selectionable_type']); // Комбінація полів для унікальності
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('selectionables');
    }
};
