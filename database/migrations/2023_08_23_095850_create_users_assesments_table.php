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
        // Schema::create('users_assesments', function (Blueprint $table) {
        //     $table->uuid('id')->primary();
        //     $table->foreignUuid('users_id');
        //     $table->foreignUuid('assesment_id');
        //     $table->timestamps();
        //     $table->softDeletes();
        // });
        // DB::statement('ALTER TABLE users_assesments ALTER COLUMN id SET DEFAULT uuid_generate_v4();');
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Schema::dropIfExists('users_assesments');
    }
};
