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
        Schema::create('assesment_canvas', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->uuid('assesment_id');
            $table->uuid('domain_id');
            $table->integer('step2_init_value');
            $table->integer('step2_value');
            $table->integer('step3_init_value');
            $table->integer('step3_value');
            $table->integer('adjustment');
            $table->string('reason');
            $table->integer('origin_capability_level');
            $table->integer('suggest_capability_level');
            $table->integer('aggreed_capability_level');
            $table->timestamps();
        });
        DB::statement('ALTER TABLE assesment_canvas ALTER COLUMN id SET DEFAULT uuid_generate_v4();');
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('table_assesment_canvas');
    }
};
