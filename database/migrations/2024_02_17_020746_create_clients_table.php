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
        Schema::create('clients', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->string('client_id');
            $table->string('user_id');
            $table->string('client_name');
            $table->string('client_email');
            $table->string('client_phone');
            $table->string('client_website')->nullable();
            $table->string('client_vat')->nullable();
            $table->text('client_address');
            $table->string('client_city')->nullable();
            $table->string('client_state')->nullable();
            $table->string('client_country')->nullable();
            $table->string('client_status')->nullable();
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
        Schema::dropIfExists('clients');
    }
};
