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
        Schema::create('assement_domain', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->uuid('assesment_id');
            $table->uuid('domain_id');
            $table->timestamps();
            $table->softDeletes();
        });
        DB::statement('ALTER TABLE assement_domain ALTER COLUMN id SET DEFAULT uuid_generate_v4();');
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('assement_domain');
    }
};
