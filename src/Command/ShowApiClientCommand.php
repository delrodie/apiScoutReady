<?php

namespace App\Command;

use App\Repository\ApiClientRepository;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Helper\Table;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;

#[AsCommand(
    name: 'app:show-api-client',
    description: 'Affichage de la table Api Client',
)]
class ShowApiClientCommand extends Command
{
    public function __construct(
        private ApiClientRepository $apiClientRepository
    )
    {
        parent::__construct();
    }

    protected function configure(): void
    {
        $this
            ->addArgument('arg1', InputArgument::OPTIONAL, 'Argument description')
            ->addOption('option1', null, InputOption::VALUE_NONE, 'Option description')
        ;
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $io = new SymfonyStyle($input, $output);
        $clients = $this->apiClientRepository->findAll();

        $table = new Table($output);
        $table
            ->setHeaders(['ID', 'NOM', 'KEYS', 'ROLES'])
            ->setRows(array_map(fn($u) =>[
                $u->getId(),
                $u->getName(),
                $u->getApiKey(),
                implode(', ', $u->getRoles())
            ], $clients));

        $table->render();

//        $io->success('You have a new command! Now make it your own! Pass --help to see your options.');

        return Command::SUCCESS;
    }
}
