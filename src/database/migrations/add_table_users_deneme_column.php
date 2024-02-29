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
        Schema::table('users_deneme', function (Blueprint $table) {
            $table->string('username')->unique()->after('name');
            $table->string('phone')->unique()->after('username');
            $table->integer('is_active')->default(1)->after('password');
            $table->timestamp('last_seen')->nullable()->after('is_active');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('users_deneme', function (Blueprint $table) {
            //
        });
    }
};
