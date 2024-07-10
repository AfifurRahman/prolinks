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
        Schema::create('action_document', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->string('project_id')->nullable();
            $table->string('subproject_id')->nullable();
            $table->string('user_id')->nullable();
            $table->integer('status')->default(0);
            $table->integer('action_type')->default(1);
            $table->longText('items_basename')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('action_document');
    }
};
