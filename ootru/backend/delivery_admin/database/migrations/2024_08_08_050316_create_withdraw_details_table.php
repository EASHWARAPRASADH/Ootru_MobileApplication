<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateWithdrawDetailsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('withdraw_details', function (Blueprint $table) {
            $table->id();
            $table->string('transaction_id')->nullable();
            $table->string('via')->nullable();
            $table->string('other_detail')->nullable();
            $table->unsignedBigInteger('withdrawrequest_id')->nullable();
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
        Schema::dropIfExists('withdraw_details');
    }
}