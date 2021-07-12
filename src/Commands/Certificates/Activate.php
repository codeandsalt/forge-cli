<?php

namespace Sven\ForgeCLI\Commands\Certificates;

use Sven\ForgeCLI\Commands\BaseCommand;
use Sven\ForgeCLI\Contracts\NeedsForge;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class Activate extends BaseCommand implements NeedsForge
{
    public function configure(): void
    {
        $this->setName('certificate:activate')
            ->addArgument('server', InputArgument::REQUIRED, 'The id of the server the site is on.')
            ->addArgument('site', InputArgument::REQUIRED, 'The id of the site the certificate should be activated on.')
            ->addArgument('certificate', InputArgument::REQUIRED, 'The id of the certificate to activate.')
            ->setDescription('Activate one of the SSL certificates on the given site.');
    }

    public function execute(InputInterface $input, OutputInterface $output): int
    {
        $this->forge->activateCertificate(
            $this->getServer($input), $this->getSite($input), $input->getArgument('certificate'), false
        );

        return 0;
    }
}
