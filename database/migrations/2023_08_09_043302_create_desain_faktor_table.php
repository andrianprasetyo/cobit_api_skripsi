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
        Schema::create('design_faktor', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->string('kode',10);
            $table->string('nama');
            $table->text('deskripsi')->nullable();
            $table->text('pertanyaan')->nullable();
            $table->foreignUuid('quisioner_grup_jawaban_id')->nullable();
            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('design_faktor');
    }
};
