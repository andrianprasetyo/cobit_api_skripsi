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
        Schema::create('quisioner_hasil', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->foreignUuid('quisioner_id');
            $table->foreignUuid('quisioner_pertanyaan_id');
            $table->foreignUuid('jawaban_id');
            $table->foreignUuid('assesment_users_id');
            $table->integer('bobot');
            $table->timestamps();
            $table->softDeletes();
        });

        DB::statement('ALTER TABLE quisioner_hasil ALTER COLUMN id SET DEFAULT uuid_generate_v4();');
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('quisioner_hasil');
    }
};
