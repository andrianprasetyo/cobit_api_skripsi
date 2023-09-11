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
        Schema::create('capability_assesment_submited', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->foreignUuid('assesment_id');
            $table->foreignUuid('domain_id');
            $table->string('level',2);
            $table->boolean('submited')->default(true);
            $table->timestamps();
            $table->softDeletes();
        });
        DB::statement('ALTER TABLE capability_assesment_submited ALTER COLUMN id SET DEFAULT uuid_generate_v4();');
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('capability_assesment_submited');
    }
};
