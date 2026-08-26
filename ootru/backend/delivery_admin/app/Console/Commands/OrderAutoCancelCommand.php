<?php

namespace App\Console\Commands;

use App\Models\Order;
use Illuminate\Console\Command;
use Carbon\Carbon;

class OrderAutoCancelCommand extends Command
{

    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'order:autocancel';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Order Cancelled Successfully!';

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        $now = Carbon::now()->addMinutes(30);
        $orders = Order::where('status', 'create')
            ->whereRaw("STR_TO_DATE(JSON_UNQUOTE(JSON_EXTRACT(pickup_point, '$.end_time')), '%Y-%m-%d %H:%i') < ?", [$now])
            ->get();

        foreach ($orders as $order) {
            $order->status = 'cancelled';
            $order->save();
        }

        return Command::SUCCESS;
    }
}
