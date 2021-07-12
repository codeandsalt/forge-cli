<?php

namespace Sven\ForgeCLI\Commands\Projects;

use Sven\ForgeCLI\Commands\BaseCommand;
use Sven\ForgeCLI\Contracts\NeedsForge;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Question\ConfirmationQuestion;

class DeleteWordpress extends BaseCommand implements NeedsForge
{
    public function configure(): void
    {
        $this->setName('wordpress:delete')
            ->addArgument('server', InputArgument::REQUIRED, 'The id of the server the site is on.')
            ->addArgument('site', InputArgument::REQUIRED, 'The id of the site to delete the WordPress project from.')
            ->setDescription('Delete a WordPress project from a site.');
    }

    public function execute(InputInterface $input, OutputInterface $output): int
    {
        $site = $this->getSite($input);

        $helper = $this->getHelper('question');
        $question = new ConfirmationQuestion('Are you sure you want to delete the WordPress project from the site with id "'.$site.'"?', false);

        if (!$helper->ask($input, $output, $question)) {
            $output->writeln('<info>Ok, aborting. Your WordPress project is safe.</info>');
        } else {
            $this->forge->removeWordPress($this->getServer($input), $site);
        }

        return 0;
    }
}
