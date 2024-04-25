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
        Schema::create('sub_project', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->string('subproject_id');
            $table->string('subproject_name');
            $table->string('project_id')->nullable();
            $table->string('user_id')->nullable();
            $table->string('client_id')->nullable();
            $table->text('subproject_desc')->nullable();
            $table->integer('subproject_status')->default(1);
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
        Schema::dropIfExists('sub_project');
    }
};
