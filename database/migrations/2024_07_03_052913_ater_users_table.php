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
        //add status integer fields in users table
        Schema::table('users', function (Blueprint $table) {
            $table->integer('status')->default(1)->after('role');
        });

    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        //drop status integer fields in users table
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn('status');
        });
    }
};
