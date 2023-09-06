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
        Schema::create('capability_level_assesment', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->foreignUuid('capability_level_id');
            $table->timestamps();
            $table->softDeletes();
        });

        DB::statement('ALTER TABLE capability_level_assesment ALTER COLUMN id SET DEFAULT uuid_generate_v4();');
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('capability_level_assesment');
    }
};
