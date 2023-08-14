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
        Schema::create('skor_hasil', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->foreignUuid('domain_id');
            $table->foreignUuid('organisasi_id');
            $table->integer('skor')->nullable();
            $table->integer('skor_baseline')->nullable();
            $table->integer('skor_relative_importance')->nullable();
            $table->timestamps();
        });

        DB::statement('ALTER TABLE skor_hasil ALTER COLUMN id SET DEFAULT uuid_generate_v4();');
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('skor_hasil');
    }
};
