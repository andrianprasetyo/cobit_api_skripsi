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
        Schema::create('quisioner_jawaban', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->text('jawaban');
            $table->foreignUuid('quisioner_grup_jawaban_id')->nullable();
            $table->integer('bobot')->nullable();
            $table->tinyInteger('sorting')->nullable();
            $table->timestamps();
            $table->softDeletes();
        });
        DB::statement('ALTER TABLE quisioner_jawaban ALTER COLUMN id SET DEFAULT uuid_generate_v4();');
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('quisioner_jawaban');
    }
};
