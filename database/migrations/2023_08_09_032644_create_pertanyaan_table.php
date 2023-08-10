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
        Schema::create('quisioner', function (Blueprint $table) {
            $table->uuid('id')->default(DB::raw('(uuid_generate_v1())'));
            $table->primary('id');
            $table->longText('pertanyaan');
            $table->foreignUuid('design_faktor_id')->nullable();
            $table->tinyInteger('sorting')->nullable();
            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('quisioner');
    }
};
