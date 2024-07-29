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
        Schema::create('watermark', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->string('client_id')->nullable();
            $table->integer('display_view')->default(0);
            $table->integer('display_printing')->default(0);
            $table->integer('display_download')->default(0);
            $table->integer('details_projectname')->default(0);
            $table->integer('details_fullname')->default(0);
            $table->integer('details_email')->default(0);
            $table->integer('details_companyname')->default(0);
            $table->integer('details_timestamp')->default(0);
            $table->string('color')->default("gray");
            $table->integer('opacity')->default(0);
            $table->integer('position')->default(0);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('watermark');
    }
};
