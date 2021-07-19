<?php

namespace Sven\ForgeCLI\Commands\Env;

use Sven\ForgeCLI\Commands\BaseCommand;
use Sven\ForgeCLI\Contracts\NeedsForge;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;

class SetVar extends BaseCommand implements NeedsForge
{
    /**
     * @var array
     */
    protected array $optionMap = [
        'file' => 'file',
    ];

    /**
     * {@inheritdoc}
     */
    public function configure()
    {
        $this->setName('env:set-var')
            ->addArgument('server', InputArgument::REQUIRED, 'The id of the server the site is on.')
            ->addArgument('site', InputArgument::REQUIRED, 'The id of the site you want to update the .env file of.')
            ->addArgument('variable', InputArgument::REQUIRED, 'The variable to update.')
            ->addArgument('value', InputArgument::REQUIRED, 'The new value to use.')
            ->addOption('recipe', null, InputOption::VALUE_REQUIRED, 'The recipe ID to run afterward, to reset the server, for instance.')
            ->addOption('update-only', 'u', InputOption::VALUE_NONE, 'Only update value. Do not create a new value.')
            ->setDescription('Set a single variable for a site and optionally run a recipe.');
    }

    /**
     * {@inheritdoc}
     */
    public function execute(InputInterface $input, OutputInterface $output)
    {
        $originalFile = $this->forge->siteEnvironmentFile($this->getServer($input), $this->getSite($input));

        $updatedFile = $this->updateEnvironmentFile(
            $originalFile,
            $input->getArgument('variable'),
            $input->getArgument('value'),
            $input->getOption('update-only')
        );

        if ($originalFile === $updatedFile) {
            $output->writeln('The original and updated files were the same. Not updating.');

            return 1;
        }

        $this->forge->updateSiteEnvironmentFile(
            $this->getServer($input), $this->getSite($input), $updatedFile
        );

        if ($input->getOption('recipe')) {
            $this->forge->runRecipe(
                $input->getOption('recipe'), [
                    'servers' => [$this->getServer($input)],
                ]
            );
        }

        return 0;
    }

    private function updateEnvironmentFile(string $originalFile, string $variable, string $value, $updateOnly = true)
    {
        if (preg_match('/[^A-Z0-9-_]/i', $value)) {
            $value = "\"$value\"";
        }

        $isFoundInEnvironment = preg_match("/^{$variable}=/um", $originalFile);

        if ($updateOnly && ! $isFoundInEnvironment) {
            return $originalFile;
        }

        if ($isFoundInEnvironment) {
            return preg_replace("/^{$variable}=.+$/um", "{$variable}=$value", $originalFile);
        } else {
            return $originalFile . "\n" . "{$variable}={$value}";
        }
    }
}
