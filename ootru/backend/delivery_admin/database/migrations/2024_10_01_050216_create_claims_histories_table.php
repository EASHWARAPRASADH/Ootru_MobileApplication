<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateClaimsHistoriesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('claims_histories', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('claim_id')->nullable();
            $table->double('amount')->nullable();         
            $table->longText('description')->nullable();       
             $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('claims_histories');
    }
}
