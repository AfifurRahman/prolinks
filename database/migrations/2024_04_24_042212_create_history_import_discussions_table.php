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
        Schema::create('history_import_discussions', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->string('client_id')->nullable();
            $table->string('project_id')->nullable();
            $table->string('user_id')->nullable();
            $table->string('file')->nullable();
            $table->integer('created_by')->nullable();
            $table->integer('updated_by')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('history_import_discussions');
    }
};
