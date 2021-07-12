<?php

namespace Sven\ForgeCLI\Commands\QuickDeploy;

use Sven\ForgeCLI\Commands\BaseCommand;
use Sven\ForgeCLI\Contracts\NeedsForge;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class Enable extends BaseCommand implements NeedsForge
{
    public function configure(): void
    {
        $this->setName('quickdeploy:enable')
            ->addArgument('server', InputArgument::REQUIRED, 'The id of the server the site is on.')
            ->addArgument('site', InputArgument::REQUIRED, 'The id of the site to enable quick deployment for.')
            ->setDescription('Enable quick deployment on the given site.');
    }

    public function execute(InputInterface $input, OutputInterface $output): int
    {
        $this->forge->enableQuickDeploy(
            $this->getServer($input), $this->getSite($input)
        );

        return 0;
    }
}
