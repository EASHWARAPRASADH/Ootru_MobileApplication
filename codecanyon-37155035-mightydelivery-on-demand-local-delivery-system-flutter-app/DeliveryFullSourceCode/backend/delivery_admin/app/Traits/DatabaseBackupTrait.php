<?php

namespace App\Traits;

use App\Models\AppSetting;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Mail;
use Carbon\Carbon;
use Illuminate\Support\Facades\Log;
use ZipArchive;

trait DatabaseBackupTrait
{
    public function createAndSendDatabaseBackup()
    {
        $filename = "backup-" . Carbon::now()->format('Y-m-d_H-i-s') . ".sql";
        $directory = storage_path("app/backups");

        // Ensure the backups directory exists
        if (!file_exists($directory)) {
            mkdir($directory, 0755, true);
        }

        $path = $directory . '/' . $filename;
        

        // Database connection details
        $database = config('database.connections.mysql.database');
        $username = config('database.connections.mysql.username');
        $password = config('database.connections.mysql.password');
        $host = config('database.connections.mysql.host');

        // Generate the database dump
        $command = "mysqldump -h {$host} -u {$username} -p{$password} {$database} > {$path}";

        exec($command, $output, $returnVar);

        if ($returnVar !== 0) {
            return false;
        }

        // Zip the backup file
        $zipPath = $directory . "/backup-" . Carbon::now()->format('Y-m-d_H-i-s') . ".zip";
        $zip = new ZipArchive();
        if ($zip->open($zipPath, ZipArchive::CREATE) === TRUE) {
            $zip->addFile($path, $filename);
            $zip->close();

            // Delete the original SQL file after zipping
            unlink($path);

            // Send the email with the zip file
            $this->sendBackupEmail($zipPath);

            return true;
        } else {
            return false;
        }
    }

    protected function sendBackupEmail($filePath)
    {
        $recipient = appSettingcurrency('backup_email');
        Log::info("Recipient email: $recipient");
        $data = [
            'fileName' => basename($filePath),
            'message' => 'Please find attached the latest database backup.',
        ];
    
        Mail::send('emails.databasebakupmail', $data, function ($message) use ($filePath, $recipient) {
            $message->to($recipient)
                    ->subject('Database Backup')
                    ->attach($filePath, [
                        'as' => basename($filePath),
                        'mime' => 'application/zip',
                    ]);
        });
        unlink($filePath); // Delete zip sending email
    }
}
