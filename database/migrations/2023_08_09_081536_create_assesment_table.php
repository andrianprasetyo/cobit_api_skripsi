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
        Schema::create('assesment', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->char('nama', 150);
            $table->char('tahun',4);
            $table->foreignUuid('organisasi_id');
            $table->char('status')->default('ongoing')->comment('ongoing, completed');// ongoing, completed
            $table->text('deskripsi')->nullable();
            $table->foreignUuid('users_id')->nullable();
            $table->timestamps();
            $table->softDeletes();
        });
        DB::statement('ALTER TABLE assesment ALTER COLUMN id SET DEFAULT uuid_generate_v4();');
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('assesment');
    }
};
