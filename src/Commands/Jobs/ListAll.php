<?php

namespace Sven\ForgeCLI\Commands\Jobs;

use Laravel\Forge\Resources\Job;
use Sven\ForgeCLI\Commands\BaseCommand;
use Sven\ForgeCLI\Contracts\NeedsForge;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class ListAll extends BaseCommand implements NeedsForge
{
    public function configure(): void
    {
        $this->setName('job:list')
            ->addArgument('server', InputArgument::REQUIRED, 'The id of the server to list the jobs for.')
            ->setDescription('Show all jobs configured on a server.');
    }

    public function execute(InputInterface $input, OutputInterface $output): int
    {
        $this->table($output, ['Id', 'Command', 'User', 'Frequency', 'Created'], array_map(function (Job $job) {
            return [$job->id, $job->name, $job->user, $job->frequency, $job->createdAt];
        }, $this->forge->jobs($this->getServer($input))));

        return 0;
    }
}
