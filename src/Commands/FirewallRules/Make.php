<?php

namespace Sven\ForgeCLI\Commands\FirewallRules;

use Sven\ForgeCLI\Commands\BaseCommand;
use Sven\ForgeCLI\Contracts\NeedsForge;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;

class Make extends BaseCommand implements NeedsForge
{
    /**
     * @var array
     */
    protected array $optionMap = [
        'name' => 'name',
        'port' => 'port',
    ];

    public function configure(): void
    {
        $this->setName('rule:make')
            ->addArgument('server', InputArgument::REQUIRED, 'The id of the server to create the firewall rule on.')
            ->addOption('name', null, InputOption::VALUE_REQUIRED, 'The name of the firewall rule.')
            ->addOption('port', null, InputOption::VALUE_REQUIRED, 'The port to create the firewall rule for.')
            ->setDescription('Create a new firewall rule.');
    }

    public function execute(InputInterface $input, OutputInterface $output): int
    {
        $this->forge->createFirewallRule(
            $this->getServer($input), $this->fillData($input->getOptions()), false
        );

        return 0;
    }
}
