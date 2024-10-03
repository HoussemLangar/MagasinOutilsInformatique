<?php

namespace App\Command;

use App\Service\DatabaseBackupService;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Attribute\AsCommand;

#[AsCommand(
    name: 'app:database-backup',
    description: 'Sauvegarde la base de données.'
)]
class DatabaseBackupCommand extends Command
{
    private DatabaseBackupService $backupService;

    public function __construct(DatabaseBackupService $backupService)
    {
        parent::__construct();
        $this->backupService = $backupService;
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        try {
            $this->backupService->backup();
            $output->writeln('<info>Sauvegarde réalisée avec succès.</info>');
            return Command::SUCCESS;
        } catch (\Exception $e) {
            $output->writeln('<error>Erreur : ' . $e->getMessage() . '</error>');
            return Command::FAILURE;
        }
    }
}
