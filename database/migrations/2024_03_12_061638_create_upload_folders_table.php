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
        Schema::create('upload_folders', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->string('directory');
            $table->string('basename');
            $table->string('name');
            $table->string('description')->nullable();
            $table->string('access_user');
            $table->integer('status');
            $table->string('uploaded_by');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('upload_folders');
    }
};
