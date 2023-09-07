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
            $table->foreignUuid('assesment_id');
            $table->foreignUuid('domain_id');
            $table->integer('agreed')->nullable();
            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('capability_target_level');
    }
};
