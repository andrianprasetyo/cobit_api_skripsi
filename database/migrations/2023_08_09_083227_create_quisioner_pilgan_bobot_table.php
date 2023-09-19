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
        // Schema::create('quisioner_jawaban_bobot', function (Blueprint $table) {
        //     $table->uuid('id')->default(DB::raw('(uuid_generate_v1())'));
        //     $table->primary('id');
        //     $table->foreignUuid('quisioner_id');
        //     $table->foreignUuid('grup_jawaban_id');
        //     $table->foreignUuid('jawaban_id');
        //     $table->integer('bobot');
        //     $table->timestamps();
        //     $table->softDeletes();
        // });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Schema::dropIfExists('quisioner_jawaban_bobot');
    }
};
