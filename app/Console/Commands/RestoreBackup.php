<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\DB;
use ZipArchive;

class RestoreBackup extends Command
{
    protected $signature = 'backup:restore {zipPath} {--password=}';
    protected $description = 'Restore database from a Spatie backup zip file.';

    public function handle()
    {
        $zipPath = $this->argument('zipPath');
        $password = $this->option('password');

        $tempDir = storage_path('app/restore-temp');
        if (!is_dir($tempDir)) mkdir($tempDir, 0755, true);

        $zip = new ZipArchive;
        if ($zip->open($zipPath) === true) {
            if ($password) $zip->setPassword($password);
            $zip->extractTo($tempDir);
            $zip->close();
        } else {
            $this->error('Failed to open backup zip.');
            return 1;
        }

        $sqlFiles = glob("$tempDir/*.sql");
        if (empty($sqlFiles)) {
            $this->error('No SQL file found inside backup archive.');
            return 1;
        }

        $sql = file_get_contents($sqlFiles[0]);
        $this->info('Restoring database...');

        DB::unprepared($sql);
        $this->info('Database restored successfully.');

        // Cleanup
        foreach ($sqlFiles as $f) unlink($f);
        rmdir($tempDir);

        return 0;
    }
}