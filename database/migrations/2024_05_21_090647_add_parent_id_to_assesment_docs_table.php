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
        Schema::table('assesment_docs', function (Blueprint $table) {
            $table->foreignUuid('parent_id')->nullable();
            $table->boolean('current')->default(false);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('assesment_docs', function (Blueprint $table) {
            //
        });
    }
};
