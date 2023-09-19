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
        Schema::create('assesment_users_hasil', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->foreignUuid('design_faktor_id');
            $table->foreignUuid('assesment_user_id');
            $table->foreignUuid('domain_id');
            $table->float('score',5,3);
            $table->float('baseline_score',5,3);
            $table->float('relative_importance',5,3);
            $table->timestamps();
            $table->softDeletes();
        });
        DB::statement('ALTER TABLE assesment_users_hasil ALTER COLUMN id SET DEFAULT uuid_generate_v4();');
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('assesment_user_hasil');
    }
};
