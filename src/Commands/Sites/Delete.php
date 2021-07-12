<?php

namespace Sven\ForgeCLI\Commands\Sites;

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
        $this->setName('site:delete')
            ->addArgument('server', InputArgument::REQUIRED, 'The id of the server where the site is.')
            ->addArgument('site', InputArgument::REQUIRED, 'The id of the site to delete.')
            ->setDescription('Delete a site.');
    }

    public function execute(InputInterface $input, OutputInterface $output): int
    {
        $site = $this->getSite($input);

        $helper = $this->getHelper('question');
        $question = new ConfirmationQuestion('Are you sure you want to delete the site with id "'.$site.'"?', false);

        if (!$helper->ask($input, $output, $question)) {
            $output->writeln('<info>Ok, aborting. Your site is safe.</info>');
        } else {
            $this->forge->deleteSite($this->getServer($input), $site);
        }

        return 0;
    }
}
