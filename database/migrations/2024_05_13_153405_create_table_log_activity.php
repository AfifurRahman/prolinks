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
        Schema::create('log_activity', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->string('ip')->nullable();
            $table->text('url')->nullable();
            $table->text('header')->nullable();
            $table->text('request')->nullable();
            $table->text('response')->nullable();
            $table->string('method')->nullable();
            $table->string('http_status', 15)->nullable();
            $table->string('agent')->nullable();
            $table->string('user_id')->nullable();
            $table->string('client_id')->nullable();
            $table->longText('description')->nullable();
            $table->integer('status')->default(1);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('log_activity');
    }
};
