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
        Schema::create('discussions', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->string('discussion_id');
            $table->string('project_id')->nullable();
            $table->string('user_id')->nullable();
            $table->string('subject')->nullable();
            $table->longtext('description')->nullable();
            $table->integer('status')->default(0);
            $table->integer('show_to_customer')->nullable();
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
        Schema::dropIfExists('discussions');
    }
};
