<?php

namespace App\Console;

use App\Models\AppSetting;
use Illuminate\Console\Scheduling\Schedule;
use Illuminate\Foundation\Console\Kernel as ConsoleKernel;

class Kernel extends ConsoleKernel
{
    /**
     * Define the application's command schedule.
     *
     * @param  \Illuminate\Console\Scheduling\Schedule  $schedule
     * @return void
     */
    protected $command = [
        // 
    ];
    protected function schedule(Schedule $schedule)
    {
        $schedule->command('order:autocancel')->everyFiveMinutes();

        $interval = AppSetting::select('backup_type')->first();
        switch ($interval->backup_type) {
            case 'daily':
                $schedule->command('backup:database')->daily()->at('02:00');
                break;
            case 'weekly':
                $schedule->command('backup:database')->weekly()->sundays()->at('03:00');
                break;
            case 'monthly':
                $schedule->command('backup:database')->monthly()->at('04:00');
                break;
            default:
                // $schedule->command('backup:database')->everyMinute();
                break;
        }
    }

    /**
     * Register the commands for the application.
     *
     * @return void
     */
    protected function commands()
    {
        $this->load(__DIR__ . '/Commands');
    }
}
