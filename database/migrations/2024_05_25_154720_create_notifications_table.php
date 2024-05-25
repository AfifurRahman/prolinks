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
        Schema::create('notification', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->string('client_id')->nullable();
            $table->string('user_id')->nullable();
            $table->string('project_id')->nullable();
            $table->integer('clientuser_id')->nullable();
            $table->string('subproject_id')->nullable();
            $table->integer('type')->default(0)->comment = "0 => discussion, 1 => upload file";
            $table->string('sender_name')->nullable();
            $table->integer('is_read')->default(0);
            $table->string('text')->nullable();
            $table->string('link')->nullable();
            $table->integer('created_by')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('notification');
    }
};
