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
        Schema::create('images', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->string('name');
            // $table->string('size');
            // $table->string('extension');
            // $table->string('path');

            $table->string('small_size_url');
            $table->string('webpage_url');

            $table->unsignedInteger('entity_id');

            $table->timestampsTz();
            $table->softDeletesTz();

            //foreign
            $table->foreign('entity_id')->references('id')->on('entities')->onUpdate('cascade')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('images');
    }
};
