<?php

namespace App\Service;

use Symfony\Component\Filesystem\Filesystem;
use Symfony\Component\Filesystem\Exception\IOExceptionInterface;

class DatabaseBackupService
{
    private $backupDir;
    private $databaseName;
    private $databaseUser;
    private $databasePassword;
    private $filesystem;

    public function __construct(string $backupDir, string $databaseName, string $databaseUser, string $databasePassword)
    {
        $this->backupDir = $backupDir;
        $this->databaseName = $databaseName;
        $this->databaseUser = $databaseUser;
        $this->databasePassword = $databasePassword;
        $this->filesystem = new Filesystem();
    }

    public function backup(): void
    {
        $date = (new \DateTime())->format('Y-m-d_H-i-s');
        $backupFile = $this->backupDir . '/backup_' . $date . '.sql';
        
        $command = sprintf('mysqldump -u %s -p%s %s > %s', $this->databaseUser, $this->databasePassword, $this->databaseName, $backupFile);
        system($command);
    }

    public function cleanOldBackups(int $days = 7): void
    {
        $files = scandir($this->backupDir);
        $now = time();

        foreach ($files as $file) {
            $filePath = $this->backupDir . '/' . $file;
            if (is_file($filePath) && $now - filemtime($filePath) >= $days * 86400) {
                $this->filesystem->remove($filePath);
            }
        }
    }

    public function restore(string $backupFile): void
    {
        $command = sprintf('mysql -u %s -p%s %s < %s', $this->databaseUser, $this->databasePassword, $this->databaseName, $backupFile);
        system($command);
    }
}
