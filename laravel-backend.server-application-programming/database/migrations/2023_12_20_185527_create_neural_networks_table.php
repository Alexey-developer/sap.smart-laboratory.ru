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
        Schema::create('neural_networks', function (Blueprint $table) {
            $table->mediumIncrements('id');
            $table->string('name');
            $table->string('description')->default('');

            $table->unsignedSmallInteger('user_id');

            $table->timestampsTz();
            $table->softDeletesTz();

            //foreign
            $table->foreign('user_id')->references('id')->on('users')->onUpdate('cascade')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('neural_networks');
    }
};
