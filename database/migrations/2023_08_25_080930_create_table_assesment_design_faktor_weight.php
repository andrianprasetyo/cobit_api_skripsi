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
        Schema::create('assesment_design_faktor_weight', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->uuid('assesment_id');
            $table->uuid('design_faktor_id');
            $table->float('weight');
            $table->timestamps();
        });
        DB::statement('ALTER TABLE assesment_design_faktor_weight ALTER COLUMN id SET DEFAULT uuid_generate_v4();');
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('table_assesment_design_faktor_weight');
    }
};
