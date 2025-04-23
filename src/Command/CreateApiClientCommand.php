<?php

namespace App\Command;

use App\Entity\ApiClient;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Question\ChoiceQuestion;
use Symfony\Component\Console\Question\Question;
use Symfony\Component\Console\Style\SymfonyStyle;

#[AsCommand(
    name: 'app:create-api-client',
    description: 'Creation des clés API',
)]
class CreateApiClientCommand extends Command
{
    public function __construct(private EntityManagerInterface $entityManager)
    {
        parent::__construct();
    }

    protected function configure(): void
    {
        $this
            ->addArgument('name', InputArgument::OPTIONAL, 'Nom du client')
            ->addOption('option1', null, InputOption::VALUE_NONE, 'Option description')
        ;
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $io = new SymfonyStyle($input, $output);
        $helper = $this->getHelper("question");

        $questionName = new Question("Entrez le nom du client : ");
        $questionName->setValidator(function ($value){
            if (trim($value) === '') throw new \Exception("Le nom du client ne peut pas être vide.");
            return $value;
        });
        $name = $helper->ask($input, $output, $questionName);

        $questionRole = new ChoiceQuestion("Choissiez le role: ", ['ROLE_API', 'ROLE_MOBILE', 'ROLE_ADMIN', 'ROLE_SUPER_ADMIN']);
        $questionRole->setErrorMessage("Le role est invalide");
        $role = $helper->ask($input, $output, $questionRole);


        $client = new ApiClient();
        $client->setName($name);
        $client->setRoles([$role]);

        $this->entityManager->persist($client);
        $this->entityManager->flush();

        $io->success("L'utilisateur {$client->getName()} a été crée avec succès! Veuillez copiez soigneusement la clé ci-dessous");
        $io->comment("Clé : {$client->getApiKey()} ");
        $output->writeln("");
        $output->writeln("");

        return Command::SUCCESS;
    }
}
