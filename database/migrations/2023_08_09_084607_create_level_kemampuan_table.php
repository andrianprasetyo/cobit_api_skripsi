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
        Schema::create('capability_level', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->string('kegiatan');
            $table->string('kode',50)->nullable();
            $table->text('translate')->nullable();
            $table->integer('bobot')->nullable();
            $table->string('level')->nullable();
            $table->integer('urutan')->nullable();
            $table->foreignUuid('domain_id')->nullable();
            $table->timestamps();
            $table->softDeletes();
        });
        DB::statement('ALTER TABLE capability_level ALTER COLUMN id SET DEFAULT uuid_generate_v4();');
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('capability_level');
    }
};
