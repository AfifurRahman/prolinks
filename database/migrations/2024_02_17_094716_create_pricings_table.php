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
        Schema::create('pricing', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->string('pricing_id');
            $table->string('pricing_name');
            $table->text('pricing_desc')->nullable();
            $table->integer('pricing_type')->nullable()->comment = "1 => allocation only 2 => allocation & date";
            $table->datetime('date_from')->nullable();
            $table->datetime('date_to')->nullable();
            $table->double('allocation_size')->nullable();
            $table->integer('pricing_status')->default(1);
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
        Schema::dropIfExists('pricings');
    }
};
