<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\File;
use Carbon\Carbon;

class BackupDatabaseCommand extends Command
{
    protected $signature = 'backup:database';
    protected $description = 'Dump the MySQL database to storage/app/backup';

    public function handle(): int
    {
        $database = config('database.connections.mysql.database');
        $username = config('database.connections.mysql.username');
        $password = config('database.connections.mysql.password');
        $host = config('database.connections.mysql.host');

        $timestamp = Carbon::now()->format('Ymd_His');
        $fileName = "backup_{$timestamp}.sql";
        $backupDir = storage_path('app/backup');

        if (!File::isDirectory($backupDir)) {
            File::makeDirectory($backupDir, 0755, true);
        }

        $filePath = "{$backupDir}/{$fileName}";

        // SSL を無効にする場合
        $command = sprintf(
            "mysqldump -h %s -u %s -p'%s' --ssl-mode=DISABLED --no-tablespaces %s > %s",
            escapeshellarg($host),
            escapeshellarg($username),
            addslashes($password),
            escapeshellarg($database),
            escapeshellarg($filePath)
        );

        $this->info('Running: ' . $command);

        exec($command, $output, $returnCode);

        if ($returnCode === 0) {
            $this->info("✅ Backup complete: {$fileName}");
            return self::SUCCESS;
        }

        $this->error('❌ Database backup failed.');
        return self::FAILURE;
    }
}
