<?php

namespace Sven\ForgeCLI\Commands\Jobs;

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
        'command' => 'command',
        'user' => 'user',
        'frequency' => 'frequency',
        'minute' => 'minute',
        'hour' => 'hour',
        'day' => 'day',
        'month' => 'month',
        'weekday' => 'weekday',
    ];

    public function configure(): void
    {
        $this->setName('job:make')
            ->addArgument('server', InputArgument::REQUIRED, 'The id of the server to schedule a new job for.')
            ->addOption('user', null, InputOption::VALUE_REQUIRED, 'The user that will be executing the command.', 'forge')
            ->addOption('command', null, InputOption::VALUE_REQUIRED, 'The command to schedule.')
            ->addOption('frequency', null, InputOption::VALUE_REQUIRED, 'With what frequency should the command run? Valid values are "minutely", "hourly", "nightly", "weekly", "monthly", and "custom".', 'custom')
            ->addOption('minute', null, InputOption::VALUE_REQUIRED, 'The minute to run the scheduled job on. Only required when using the "custom" frequency.', '*')
            ->addOption('hour', null, InputOption::VALUE_REQUIRED, 'The hour to run the scheduled job on. Only required when using the "custom" frequency.', '*')
            ->addOption('day', null, InputOption::VALUE_REQUIRED, 'The day to run the scheduled job on. Only required when using the "custom" frequency.', '*')
            ->addOption('month', null, InputOption::VALUE_REQUIRED, 'The month to run the scheduled job on. Only required when using the "custom" frequency.', '*')
            ->addOption('weekday', null, InputOption::VALUE_REQUIRED, 'The weekday to run the scheduled job on. Only required when using the "custom" frequency.', '*')
            ->setDescription('Schedule a new job on one of your servers.');
    }

    public function execute(InputInterface $input, OutputInterface $output): int
    {
        if (strtolower($input->getOption('frequency')) !== 'custom') {
            $this->requireOptions($input, 'minute', 'hour', 'day', 'month', 'weekday');
        }

        $this->forge->createJob(
            $this->getServer($input),
            $this->fillData($input->getOptions()),
            false
        );

        return 0;
    }
}
