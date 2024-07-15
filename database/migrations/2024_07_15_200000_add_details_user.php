<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Laravel\Fortify\Fortify;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->text('phone')
                ->after('email')
                ->nullable();

            $table->text('title')
                ->after('username')
                ->nullable();

        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
     
    }
};
