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
        Schema::create('design_faktor_map', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->foreignUuid('domain_id');
            $table->foreignUuid('design_faktor_id');
            $table->foreignUuid('design_faktor_komponen_id');
            $table->float('nilai')->nullable();
            $table->timestamps();
        });
        DB::statement('ALTER TABLE design_faktor_map ALTER COLUMN id SET DEFAULT uuid_generate_v4();');
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('design_faktor_map');
    }
};
