<?php

namespace Sven\ForgeCLI\Commands\Workers;

use Sven\ForgeCLI\Commands\BaseCommand;
use Sven\ForgeCLI\Contracts\NeedsForge;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Question\ConfirmationQuestion;

class Reboot extends BaseCommand implements NeedsForge
{
    public function configure(): void
    {
        $this->setName('worker:reboot')
            ->addArgument('server', InputArgument::REQUIRED, 'The id of the server the worker to reboot is on.')
            ->addArgument('site', InputArgument::REQUIRED, 'The id of the site the worker to reboot is on.')
            ->addArgument('worker', InputArgument::REQUIRED, 'The id of the worker to reboot.')
            ->setDescription('Reboot one of your workers.');
    }

    public function execute(InputInterface $input, OutputInterface $output): int
    {
        $worker = $input->getArgument('worker');

        $helper = $this->getHelper('question');
        $question = new ConfirmationQuestion('Are you sure you want to reboot the worker with id "'.$worker.'"?', false);

        if (!$helper->ask($input, $output, $question)) {
            $output->writeln('<info>Ok, aborting.</info>');
        } else {
            $this->forge->restartWorker(
                $input->getArgument('server'), $input->getArgument('site'), $worker, false
            );
        }

        return 0;
    }
}
