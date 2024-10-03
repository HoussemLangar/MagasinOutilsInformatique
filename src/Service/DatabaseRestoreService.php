<?php

namespace App\Service;

use Symfony\Component\Filesystem\Filesystem;

class DatabaseRestoreService
{
    private $backupDir;
    private $databaseName;
    private $databaseUser;
    private $databasePassword;

    public function __construct(
        string $backupDir,
        string $databaseName,
        string $databaseUser,
        string $databasePassword
    ) {
        $this->backupDir = $backupDir;
        $this->databaseName = $databaseName;
        $this->databaseUser = $databaseUser;
        $this->databasePassword = $databasePassword;
    }

    public function restore(): void
    {
        $filesystem = new Filesystem();
        if (!$filesystem->exists($this->backupDir)) {
            throw new \RuntimeException("Le répertoire de sauvegarde n'existe pas : " . $this->backupDir);
        }

        $backupFiles = glob($this->backupDir . '/*.sql');
        if (empty($backupFiles)) {
            throw new \RuntimeException("Aucun fichier de sauvegarde trouvé dans : " . $this->backupDir);
        }

        $latestBackup = max($backupFiles);

        $command = sprintf(
            'mysql -u %s -p%s %s < %s',
            escapeshellarg($this->databaseUser),
            escapeshellarg($this->databasePassword),
            escapeshellarg($this->databaseName),
            escapeshellarg($latestBackup)
        );

        exec($command, $output, $returnVar);

        if ($returnVar !== 0) {
            throw new \RuntimeException('Erreur lors de la restauration de la base de données. Code de retour : ' . $returnVar);
        }
    }
}
