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
        Schema::create('organisasi_divisi_map_df', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->foreignUuid('organisasi_divisi_id');
            $table->foreignUuid('design_faktor_id');
            // $table->foreignUuid('assesment_id')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('organisasi_divisi_map_df');
    }
};
