<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddBidDataMigationToOrdersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('orders', function (Blueprint $table) {
            $table->tinyInteger('bid_type')->nullable();
            $table->text('nearby_driver_ids')->nullable()->after('bid_type');
            $table->text('accept_delivery_man_ids')->nullable()->after('nearby_driver_ids');
            $table->text('reject_delivery_man_ids')->nullable()->after('accept_delivery_man_ids');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('orders', function (Blueprint $table) {
            //
        });
    }
}
