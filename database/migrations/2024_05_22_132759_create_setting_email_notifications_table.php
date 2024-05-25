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
        Schema::create('setting_email_notification', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->string('client_id')->nullable();
            $table->string('user_id')->nullable();
            $table->string('project_id')->nullable();
            $table->string('subproject_id')->nullable();
            $table->integer('clientuser_id')->nullable();
            $table->integer('is_upload_file')->default(1);
            $table->integer('is_discussion')->default(1);
            $table->integer('is_change_role')->default(1);
            $table->integer('created_by')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('setting_email_notification');
    }
};
