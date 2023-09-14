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
        Schema::create('capability_assesment', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->foreignUuid('capability_level_id');
            $table->foreignUuid('capability_answer_id');
            $table->text('note')->nullable();
            $table->text('ofi')->nullable();
            $table->foreignUuid('assesment_id')->nullable();
            $table->foreignUuid('domain_id')->nullable();
            $table->foreignUuid('capability_target_id')->nullable();
            $table->timestamps();
            $table->softDeletes();
        });
        DB::statement('ALTER TABLE capability_assesment ALTER COLUMN id SET DEFAULT uuid_generate_v4();');
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('capability_assesment');
    }
};
