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
        Schema::create('roles', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->string('nama', 75);
            $table->string('code',50);
            $table->text('deskripsi')->nullable();
            $table->boolean('aktif')->default(true);
            $table->timestamps();
            $table->softDeletes();
        });
        DB::statement('ALTER TABLE roles ALTER COLUMN id SET DEFAULT uuid_generate_v4();');
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('roles');
    }
};
