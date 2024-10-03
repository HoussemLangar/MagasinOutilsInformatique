<?php

namespace App\Command;

use App\Service\DatabaseRestoreService;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Attribute\AsCommand;

#[AsCommand(
    name: 'app:database-restore',
    description: 'Restaure la base de données à partir d’un fichier de sauvegarde.'
)]
class DatabaseRestoreCommand extends Command
{
    private DatabaseRestoreService $restoreService;

    public function __construct(DatabaseRestoreService $restoreService)
    {
        parent::__construct();
        $this->restoreService = $restoreService;
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        try {
            $this->restoreService->restore();
            $output->writeln('<info>Restauration réalisée avec succès.</info>');
            return Command::SUCCESS;
        } catch (\Exception $e) {
            $output->writeln('<error>Erreur : ' . $e->getMessage() . '</error>');
            return Command::FAILURE;
        }
    }
}
