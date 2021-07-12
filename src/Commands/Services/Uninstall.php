<?php

namespace Sven\ForgeCLI\Commands\Services;

use Sven\ForgeCLI\Commands\BaseCommand;
use Sven\ForgeCLI\Contracts\NeedsForge;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Question\ConfirmationQuestion;

class Uninstall extends BaseCommand implements NeedsForge
{
    public function configure(): void
    {
        $this->setName('service:uninstall')
            ->addArgument('server', InputArgument::REQUIRED, 'The id of the server to uninstall the service from.')
            ->addArgument('service', InputArgument::REQUIRED, 'The service to be uninstalled. Can be "blackfire" or "papertrail".')
            ->setDescription('Uninstall a service from a server.');
    }

    public function execute(InputInterface $input, OutputInterface $output): int
    {
        $server = $this->getServer($input);
        $service = strtolower($input->getArgument('service'));

        $helper = $this->getHelper('question');
        $question = new ConfirmationQuestion('Are you sure you want to uninstall '.$service.' from the server with id '.$server.'?', false);

        if (!$helper->ask($input, $output, $question)) {
            $output->writeln('<info>Ok, aborting.</info>');

            return 0;
        }

        switch ($service) {
            case 'blackfire':
                $this->forge->removeBlackfire($server);
                break;

            case 'papertrail':
                $this->forge->removePapertrail($server);
                break;
        }

        return 0;
    }
}
