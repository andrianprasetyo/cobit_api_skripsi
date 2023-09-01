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
            $table->string('nama', 150);
            $table->string('tahun',10)->nullable();
            $table->foreignUuid('organisasi_id');
            $table->string('status')->default('ongoing')->comment('ongoing, completed');// ongoing, completed
            $table->text('deskripsi')->nullable();
            $table->foreignUuid('users_id')->nullable();
            $table->timestamps();
            $table->date('start_date');
            $table->date('end_date');
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
