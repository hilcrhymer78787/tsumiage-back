<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\File;
use Carbon\Carbon;

class BackupDatabaseCommand extends Command
{
    protected $signature = 'backup:database';
    protected $description = 'Dump the MySQL database to storage/app/backup and delete backups older than 3 days';

    public function handle(): int
    {
        $database = config('database.connections.mysql.database');
        $username = config('database.connections.mysql.username');
        $password = config('database.connections.mysql.password');
        $host = config('database.connections.mysql.host');
        $port = config('database.connections.mysql.port', 3306);

        $timestamp = Carbon::now()->format('Ymd_His');
        $fileName = "backup_{$timestamp}.sql";
        $backupDir = storage_path('app/backup');

        if (!File::isDirectory($backupDir)) {
            File::makeDirectory($backupDir, 0755, true);
        }

        $filePath = "{$backupDir}/{$fileName}";

        // データベースバックアップ
        $command = sprintf(
            "mysqldump -h %s -P %d -u %s -p'%s' --ssl-mode=DISABLED --no-tablespaces %s > %s",
            escapeshellarg($host),
            $port,
            escapeshellarg($username),
            addslashes($password),
            escapeshellarg($database),
            escapeshellarg($filePath)
        );

        $this->info('Running: ' . $command);
        exec($command, $output, $returnCode);

        if ($returnCode === 0) {
            $this->info("✅ Backup complete: {$fileName}");
        } else {
            $this->error('❌ Database backup failed.');
            return self::FAILURE;
        }

        // --- 古いバックアップ削除 ---
        $files = File::files($backupDir);
        $now = Carbon::now();

        foreach ($files as $file) {
            if ($now->diffInDays(Carbon::createFromTimestamp(File::lastModified($file))) > 3) {
                File::delete($file);
                $this->info("🗑 Deleted old backup: {$file->getFilename()}");
            }
        }

        return self::SUCCESS;
    }
}
