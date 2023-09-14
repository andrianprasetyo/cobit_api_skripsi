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
        Schema::create('capability_target_level', function (Blueprint $table) {
            $table->uuid('id')->primary();
            // $table->foreignUuid('assesment_id');
            $table->foreignUuid('domain_id')->nullable();
            $table->foreignUuid('capability_target_id')->nullable();
            $table->foreignUuid('assesment_domain_id')->nullable();
            $table->double('target',1,2)->nullable();
            $table->timestamps();
            $table->softDeletes();
        });
        DB::statement('ALTER TABLE capability_target_level ALTER COLUMN id SET DEFAULT uuid_generate_v4();');
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('capability_target_level');
    }
};
