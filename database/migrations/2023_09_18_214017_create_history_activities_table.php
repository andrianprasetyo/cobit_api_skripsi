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
        Schema::create('history_activities', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->string('action')->nullable();
            $table->json('before')->nullable();
            $table->json('after')->nullable();
            $table->foreignUuid('created_by')->nullable();
            $table->foreignUuid('pk')->nullable();
            $table->string('module')->nullable();
            $table->string('path')->nullable();
            $table->string('method')->nullable();
            $table->string('create_by_role')->nullable();
            $table->text('description')->nullable();
            $table->timestamps();
            $table->softDeletes();
        });
        DB::statement('ALTER TABLE history_activities ALTER COLUMN id SET DEFAULT uuid_generate_v4();');
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('history_activities');
    }
};
