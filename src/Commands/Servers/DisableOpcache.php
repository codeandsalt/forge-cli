<?php

namespace Sven\ForgeCLI\Commands\Servers;

use Sven\ForgeCLI\Commands\BaseCommand;
use Sven\ForgeCLI\Contracts\NeedsForge;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;

class DisableOpcache extends BaseCommand implements NeedsForge
{
    /**
     * @var array
     */
    protected array $optionMap = [];

    public function configure(): void
    {
        $this->setName('server:opcache-disable')
            ->addArgument('server', InputArgument::REQUIRED, 'The id of the server to update.')
            ->setDescription('Disable the PHP Opcache on a server.');
    }

    public function execute(InputInterface $input, OutputInterface $output): int
    {
        $this->forge->disableOPCache($this->getServer($input));

        return 0;
    }
}
