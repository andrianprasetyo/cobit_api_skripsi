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
        Schema::create('roles_users', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->foreignUuid('roles_id');
            $table->foreignUuid('users_id');
            $table->boolean('default')->default(false);
            $table->timestamps();
            $table->softDeletes();
        });
        DB::statement('ALTER TABLE roles_users ALTER COLUMN id SET DEFAULT uuid_generate_v4();');
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('roles_users');
    }
};
