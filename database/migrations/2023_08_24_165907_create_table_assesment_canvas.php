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
            $table->integer('step2_init_value')->nullable();
            $table->integer('step2_value')->nullable();
            $table->integer('step3_init_value')->nullable();
            $table->integer('step3_value')->nullable();
            $table->integer('adjustment')->nullable();
            $table->text('reason')->nullable();
            $table->integer('origin_capability_level')->nullable();
            $table->integer('suggest_capability_level')->nullable();
            $table->integer('aggreed_capability_level')->nullable();
            $table->text('reason_adjustment')->nullable();
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
