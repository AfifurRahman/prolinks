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
        Schema::create('project', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->string('project_name');
            $table->string('user_id');
            $table->text('project_desc')->nullable();
            $table->integer('project_status')->default(1);
            $table->datetime('start_date')->nullable();
            $table->datetime('deadline')->nullable();
            $table->integer('estimate_hours')->nullable();
            $table->double('total_rate')->nullable();
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
        Schema::dropIfExists('projects');
    }
};
