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
        Schema::create('entities', function (Blueprint $table) {
            $table->increments('id');
            $table->string('name');

            $table->unsignedMediumInteger('neural_network_id');

            $table->timestampsTz();
            $table->softDeletesTz();

            //foreign
            $table->foreign('neural_network_id')->references('id')->on('neural_networks')->onUpdate('cascade')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('entities');
    }
};
