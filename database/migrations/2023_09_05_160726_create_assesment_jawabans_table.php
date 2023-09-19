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
        Schema::create('capability_answer', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->string('nama');
            $table->double('bobot',1,2);
            $table->string('label',5)->nullable();
            // $table->foreignUuid('assesment_id_id');
            $table->timestamps();
            $table->softDeletes();
        });
        DB::statement('ALTER TABLE capability_answer ALTER COLUMN id SET DEFAULT uuid_generate_v4();');
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('capability_answer');
    }
};
