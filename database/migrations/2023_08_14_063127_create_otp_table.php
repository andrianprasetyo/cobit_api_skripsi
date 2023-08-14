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
        Schema::create('otp', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->string('kode', 6)->nullable();
            $table->text('token')->nullable();
            $table->foreignUuid('users_id');
            $table->dateTime('expire_at');
            $table->string('aksi')->nullable();
            $table->string('verify_by', 20)->nullable();
            $table->boolean('digunakan')->nullable()->default('false');
            $table->timestamps();
            $table->softDeletes();
        });
        DB::statement('ALTER TABLE otp ALTER COLUMN id SET DEFAULT uuid_generate_v4();');
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('otp');
    }
};
