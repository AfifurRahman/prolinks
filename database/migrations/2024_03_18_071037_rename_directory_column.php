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
        Schema::table('upload_files', function (Blueprint $table) {
            $table->renameColumn('directory', 'project_id');
        });
        Schema::table('upload_folders', function (Blueprint $table) {
            $table->renameColumn('directory', 'project_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('upload_files', function (Blueprint $table) {
            $table->renameColumn('project_id', 'directory');
        });
        Schema::table('upload_folders', function (Blueprint $table) {
            $table->renameColumn('project_id', 'directory');
        });
    }
};
