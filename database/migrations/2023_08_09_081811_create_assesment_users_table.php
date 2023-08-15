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
        Schema::create('assesment_users', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->foreignUuid('assesment_id');
            $table->char('email',50);
            $table->char('divisi',100);
            $table->char('jabatan',100);
            $table->char('code',100)->comment('Kode invitation random string 50 karakter');
            $table->char('status',15)->default('diundang')->comment('diundang, selesai');
//            $table->foreignUuid('users_id');
            $table->timestamps();
            $table->softDeletes();
        });
        DB::statement('ALTER TABLE assesment_users ALTER COLUMN id SET DEFAULT uuid_generate_v4();');
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('assesment_users');
    }
};
