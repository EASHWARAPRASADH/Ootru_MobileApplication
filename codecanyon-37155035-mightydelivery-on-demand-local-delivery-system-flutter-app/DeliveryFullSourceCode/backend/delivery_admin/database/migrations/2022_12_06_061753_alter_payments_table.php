<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AlterPaymentsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('payments', function (Blueprint $table) {
            $table->double('cancel_charges')->nullable()->default('0');
            $table->double('admin_commission')->nullable()->default('0');
            $table->double('delivery_man_commission')->nullable()->default('0');
            $table->string('received_by')->nullable();
            $table->double('delivery_man_fee')->nullable()->default('0');
            $table->double('delivery_man_tip')->nullable()->default('0');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('payments', function (Blueprint $table) {
            //
        });
    }
}
