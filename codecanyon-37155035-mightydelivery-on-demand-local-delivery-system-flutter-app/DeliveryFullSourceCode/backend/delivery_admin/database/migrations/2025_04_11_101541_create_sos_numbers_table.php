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
        Schema::create('sos_numbers', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('delivery_man_id')->nullable();
            $table->string('name')->nullable();
            $table->string('contact_number', 255)->nullable();
            $table->foreign('delivery_man_id')->references('id')->on('users')->onDelete('cascade');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('sos_numbers');
    }
};
