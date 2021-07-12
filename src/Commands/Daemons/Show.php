<?php

namespace Sven\ForgeCLI\Commands\Daemons;

use Sven\ForgeCLI\Commands\BaseCommand;
use Sven\ForgeCLI\Contracts\NeedsForge;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class Show extends BaseCommand implements NeedsForge
{
    public function configure(): void
    {
        $this->setName('daemon:show')
            ->addArgument('server', InputArgument::REQUIRED, 'The id of the server to the daemon is running on.')
            ->addArgument('daemon', InputArgument::REQUIRED, 'The id of the daemon to show information about.')
            ->setDescription('Show information about the given daemon.');
    }

    public function execute(InputInterface $input, OutputInterface $output): int
    {
        $daemon = $this->forge->daemon(
            $this->getServer($input), $input->getArgument('daemon')
        );

        $output->writeln([
            '<info>Status:</info>  '.$daemon->status,
            '<info>Command:</info> '.$daemon->command,
            '<info>Created:</info> '.$daemon->createdAt,
        ]);

        return 0;
    }
}
