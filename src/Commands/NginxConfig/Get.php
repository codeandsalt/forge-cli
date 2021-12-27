<?php

namespace Sven\ForgeCLI\Commands\NginxConfig;

use Sven\ForgeCLI\Commands\BaseCommand;
use Sven\ForgeCLI\Contracts\NeedsForge;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class Get extends BaseCommand implements NeedsForge
{
    public function configure(): void
    {
        $this->setName('nginx-config:get')
            ->addArgument('server', InputArgument::REQUIRED, 'The id of the server the site is on.')
            ->addArgument('site', InputArgument::REQUIRED, 'The id of the site to get the config script for.')
            ->setDescription('Show the nginx configuration file.');
    }

    public function execute(InputInterface $input, OutputInterface $output): int
    {
        $config = $this->forge->siteNginxFile(
            $this->getServer($input), $this->getSite($input)
        );

        $output->writeln($config);

        return 0;
    }
}
