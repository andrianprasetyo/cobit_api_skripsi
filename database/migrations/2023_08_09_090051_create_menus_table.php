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
        Schema::create('menu', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->string('nama', 100)->comment('nama menu');
            $table->string('code', 50)->comment('kode menu');
            $table->string('url')->nullable()->comment('url menu');
            $table->string('icon', 50)->nullable()->comment('icon menu');
            $table->integer('sorting')->comment('pengurutan menu');
            $table->foreignUuid('parent_id')->nullable()->comment('jika != null maka sub menu');
            $table->longText('deskripsi')->nullable()->comment('deksripsi menu');
            $table->boolean('active')->default(true)->comment('aktif/tidak menu');
            $table->boolean('is_menu')->default(false)->comment('1/true untuk di tampilkan di web');
            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('menus');
    }
};
