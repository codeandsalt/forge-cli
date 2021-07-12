<?php

namespace Sven\ForgeCLI\Commands\Databases;

use Sven\ForgeCLI\Commands\BaseCommand;
use Sven\ForgeCLI\Contracts\NeedsForge;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class Show extends BaseCommand implements NeedsForge
{
    public function configure(): void
    {
        $this->setName('database:show')
            ->addArgument('server', InputArgument::REQUIRED, 'The id of the server to the database is running on.')
            ->addArgument('database', InputArgument::REQUIRED, 'The id of the database to show information about.')
            ->setDescription('Show information about the given database.');
    }

    public function execute(InputInterface $input, OutputInterface $output): int
    {
        $database = $this->forge->mysqlDatabase(
            $this->getServer($input), $input->getArgument('database')
        );

        $output->writeln([
            '<info>Status:</info>  '.$database->status,
            '<info>Name:</info>    '.$database->name,
            '<info>Created:</info> '.$database->createdAt,
        ]);

        return 0;
    }
}
