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
        Schema::create('users', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->string('nama', 75);
            $table->string('username',50);
            $table->string('email')->unique()->nullable();
            $table->timestamp('email_verified_at')->nullable();
            $table->string('password');
            $table->string('divisi')->nullable();
            $table->string('posisi')->nullable();
            $table->string('status',30)->default('pending')->comment('active,pending,banned');
            $table->boolean('internal')->default(true);
            $table->json('avatar')->nullable();
            $table->foreignUuid('organisasi_id')->nullable();
            $table->foreignUuid('assesment_id')->nullable();
            $table->text('token')->nullable();
            $table->rememberToken();
            $table->timestamps();
            $table->softDeletes();
        });
        DB::statement('ALTER TABLE users ALTER COLUMN id SET DEFAULT uuid_generate_v4();');
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('users');
    }
};
