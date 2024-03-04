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
        Schema::create('discussion_comments', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->string('discussion_id');
            $table->string('project_id')->nullable();
            $table->string('user_id')->nullable();
            $table->string('discussion_type', 50)->nullable();
            $table->integer('parent')->nullable();
            $table->longtext('content')->nullable();
            $table->string('fullname')->nullable();
            $table->string('file_name')->nullable();
            $table->string('file_mime_type', 100)->nullable();
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
        Schema::dropIfExists('discussion_comments');
    }
};
