<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreatePayTRPaymentsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('pay_t_r_payments', function (Blueprint $table) {
            $table->id();
            $table->bigInteger('merchant_oid')->nullable();
            $table->unsignedBigInteger('client_id')->nullable();
            $table->unsignedBigInteger('merchant_id')->nullable();
            $table->string('hash', 255)->nullable();
            $table->dateTime('datetime')->nullable();
            $table->double('total_amount')->nullable()->default('0');
            $table->string('payment_type');
            $table->string('payment_status')->nullable()->comment('pending, paid, failed');
            $table->foreign('client_id')->references('id')->on('users')->onDelete('cascade');
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
        Schema::dropIfExists('pay_t_r_payments');
    }
}
