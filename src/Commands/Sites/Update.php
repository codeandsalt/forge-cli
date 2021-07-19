<?php

namespace Sven\ForgeCLI\Commands\Sites;

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
        'directory' => 'directory',
        'alias' => 'aliases',
    ];

    public function configure(): void
    {
        $this->setName('site:update')
            ->addArgument('server', InputArgument::REQUIRED, 'The id of the server the site is on.')
            ->addArgument('site', InputArgument::REQUIRED, 'The id of the server to update.')
            ->addOption('directory', null, InputOption::VALUE_REQUIRED, 'The new base directory of the website.')
            ->addOption('alias', null, InputOption::VALUE_IS_ARRAY | InputOption::VALUE_OPTIONAL, 'The aliases assigned to the site.')
            ->setDescription('Update a site on a specified server.');
    }

    public function execute(InputInterface $input, OutputInterface $output): int
    {
        $this->forge->updateSite(
            $this->getServer($input), $this->getSite($input), $this->fillData($input->getOptions())
        );

        return 0;
    }
}
