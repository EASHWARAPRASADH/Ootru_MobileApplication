<?php

use App\Models\Order;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddMilisecondToOrdersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('orders', function (Blueprint $table) {
            $table->string('milisecond')->nullable()->after('currency');
        });
        Order::whereNull('milisecond')->get()->each(function ($order) {
            $createdAt = \Carbon\Carbon::parse($order->created_at);
            $milliseconds = round($createdAt->timestamp * 1000);
            $order->update(['milisecond' => $milliseconds]);
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
