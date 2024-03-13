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
        Schema::table('client_users', function (Blueprint $table) {
            $table->string('user_id')->nullable()->after('id');
            $table->string('name')->nullable()->after('email_address');
            $table->string('client_id')->nullable()->after('name');
            $table->string('job_title')->nullable()->after('company');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        //
    }
};
