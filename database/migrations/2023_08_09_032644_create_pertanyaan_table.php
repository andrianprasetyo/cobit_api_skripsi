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
        Schema::create('quisioner_pertanyaan', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->longText('pertanyaan');
            $table->foreignUuid('design_faktor_id')->nullable();
            $table->foreignUuid('quisioner_grup_jawaban_id')->nullable();
            $table->foreignUuid('quisioner_id')->nullable();
            $table->tinyInteger('sorting')->nullable();
            $table->timestamps();
            $table->softDeletes();
        });
        DB::statement('ALTER TABLE quisioner_pertanyaan ALTER COLUMN id SET DEFAULT uuid_generate_v4();');
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('quisioner');
    }
};
