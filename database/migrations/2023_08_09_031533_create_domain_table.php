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
        Schema::create('domain', function (Blueprint $table) {
            $table->uuid('id')->default(DB::raw('(uuid_generate_v1())'));
            $table->primary('id');
            $table->string('kode', 30)->unique();
            // $table->string('nama', 75)->nullable();
            $table->text('ket')->nullable();
            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('domain');
    }
};
