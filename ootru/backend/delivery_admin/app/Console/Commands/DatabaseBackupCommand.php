<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Traits\DatabaseBackupTrait;

class DatabaseBackupCommand extends Command
{
    use DatabaseBackupTrait;

    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'backup:database';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Create and send a database backup';

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        $this->createAndSendDatabaseBackup();
        $this->info('Database backup sent successfully.');
        return Command::SUCCESS;
    }
}
