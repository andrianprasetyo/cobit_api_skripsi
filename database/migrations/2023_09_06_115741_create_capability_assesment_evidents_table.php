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
        Schema::create('capability_assesment_evident', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->foreignUuid('capability_assesment_id');
            $table->string('tipe');
            $table->foreignUuid('media_repositories_id')->nullable();
            $table->text('url')->nullable();
            $table->text('deskripsi')->nullable();
            $table->timestamps();
            $table->softDeletes();
        });
        DB::statement('ALTER TABLE capability_assesment_evident ALTER COLUMN id SET DEFAULT uuid_generate_v4();');
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('capability_assesment_evident');
    }
};
