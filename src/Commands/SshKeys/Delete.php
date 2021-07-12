<?php

namespace Sven\ForgeCLI\Commands\SshKeys;

use Sven\ForgeCLI\Commands\BaseCommand;
use Sven\ForgeCLI\Contracts\NeedsForge;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Question\ConfirmationQuestion;

class Delete extends BaseCommand implements NeedsForge
{
    public function configure(): void
    {
        $this->setName('key:delete')
            ->addArgument('server', InputArgument::REQUIRED, 'The id of the server where the SSH key is.')
            ->addArgument('key', InputArgument::REQUIRED, 'The id of the SSH key to delete.')
            ->setDescription('Delete an SSH key.');
    }

    public function execute(InputInterface $input, OutputInterface $output): int
    {
        $key = $input->getArgument('key');

        $helper = $this->getHelper('question');
        $question = new ConfirmationQuestion('Are you sure you want to delete the SSH key with id "'.$key.'"?', false);

        if (!$helper->ask($input, $output, $question)) {
            $output->writeln('<info>Ok, aborting. Your SSH key is safe.</info>');
        } else {
            $this->forge->deleteSSHKey($this->getServer($input), $key);
        }

        return 0;
    }
}
