<?php

namespace Sven\ForgeCLI\Commands\Servers;

use Sven\ForgeCLI\Commands\BaseCommand;
use Sven\ForgeCLI\Contracts\NeedsForge;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;

class Update extends BaseCommand implements NeedsForge
{
    /**
     * @var array
     */
    protected array $optionMap = [
        'name' => 'name',
        'size' => 'size',
        'ip' => 'ip_address',
        'private-ip' => 'private_ip_address',
        'max-upload-size' => 'max_upload_size',
        'max-execution-time' => 'max_execution_time',
        'network' => 'network',
    ];

    public function configure(): void
    {
        $this->setName('server:update')
            ->addArgument('server', InputArgument::REQUIRED, 'The id of the server to update.')
            ->addOption('name', null, InputOption::VALUE_REQUIRED, 'The name of the server.')
            ->addOption('size', null, InputOption::VALUE_REQUIRED, 'The amount of RAM the server has.')
            ->addOption('ip', null, InputOption::VALUE_REQUIRED, 'The server\'s IP address.')
            ->addOption('private-ip', null, InputOption::VALUE_REQUIRED, 'The server\'s private IP address.')
            ->addOption('max-upload-size', 'M', InputOption::VALUE_REQUIRED, 'The configured max upload size on the server.')
            ->addOption('max-execution-time', 'T', InputOption::VALUE_REQUIRED, 'The configured max execution time on the server.')
            ->addOption('network', null, InputOption::VALUE_IS_ARRAY | InputOption::VALUE_REQUIRED, 'Other servers\' ids this one can network with.')
            ->setDescription('Update the metadata on one of your servers.');
    }

    public function execute(InputInterface $input, OutputInterface $output): int
    {
        $this->forge->updateServer(
            $this->getServer($input), $this->fillData($input->getOptions())
        );

        return 0;
    }
}
