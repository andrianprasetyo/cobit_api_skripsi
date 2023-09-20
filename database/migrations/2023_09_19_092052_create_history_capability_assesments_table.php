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
        Schema::create('history_capability_assesments', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->foreignUuid('created_by')->nullable();
            $table->foreignUuid('assesment_id')->nullable();
            $table->foreignUuid('domain_id')->nullable();
            $table->json('before')->nullable();
            $table->json('after')->nullable();
            $table->string('create_by_role')->nullable();
            $table->timestamps();
        });

        DB::statement('ALTER TABLE history_capability_assesments ALTER COLUMN id SET DEFAULT uuid_generate_v4();');
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('history_capability_assesments');
    }
};
