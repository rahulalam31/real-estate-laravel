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
        Schema::create('favorities', function (Blueprint $table) {
            $table->id('favorite_id');
            $table->unsignedBigInteger('users');
            $table->unsignedBigInteger('property_id'); 
            $table->foreignId('user_id')->references('user_id')->on('users');
            $table->foreign('property_id')->references('property_id')->on('properties'); 
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('favorities');
    }
};
