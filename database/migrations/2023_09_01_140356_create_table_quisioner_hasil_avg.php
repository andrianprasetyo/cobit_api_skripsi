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
        Schema::create('quisioner_hasil_avg', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->uuid('quisioner_id');
            $table->uuid('quisioner_pertanyaan_id');
            $table->uuid('design_faktor_komponen_id');
            $table->uuid('assesment_id');
            $table->float('avg_bobot');
//            $table->boolean('default')->default(false);
            $table->timestamps();
//            $table->softDeletes();
        });
        DB::statement('ALTER TABLE quisioner_hasil_avg ALTER COLUMN id SET DEFAULT uuid_generate_v4();');
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('table_quisioner_hasil_avg');
    }
};
