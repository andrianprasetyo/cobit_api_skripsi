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
        Schema::create('design_faktor_map_additional', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->char('domain','10');
            $table->foreignUuid('design_faktor_id');
            $table->integer('urutan');
            $table->timestamps();
        });
        DB::statement('ALTER TABLE design_faktor_map_additional ALTER COLUMN id SET DEFAULT uuid_generate_v4();');
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('design_faktor_map_additional');
    }
};
