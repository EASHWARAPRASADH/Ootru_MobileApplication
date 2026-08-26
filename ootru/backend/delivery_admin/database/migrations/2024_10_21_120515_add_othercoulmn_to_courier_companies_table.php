<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddOthercoulmnToCourierCompaniesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('courier_companies', function (Blueprint $table) {
            $table->string('tracking_details')->nullable();
            $table->string('tracking_number', 255)->nullable();
            $table->string('shipping_provider')->nullable();
            $table->dateTime('date_shipped')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('courier_companies', function (Blueprint $table) {
            //
        });
    }
}
