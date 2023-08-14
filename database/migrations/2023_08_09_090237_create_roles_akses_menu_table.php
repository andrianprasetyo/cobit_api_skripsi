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
        Schema::create('roles_akses_menu', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->foreignUuid('role_id');
            $table->foreignUuid('menu_id');
            $table->foreignUuid('menu_code');
            $table->boolean('akses')->default(false);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('roles_akses_menu');
    }
};
