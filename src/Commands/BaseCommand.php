<?php

namespace Sven\ForgeCLI\Commands;

use InvalidArgumentException;
use RuntimeException;
use Sven\FileConfig\Drivers\Json;
use Sven\FileConfig\File;
use Sven\FileConfig\Store;
use Sven\ForgeCLI\Contracts\NeedsForge;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Helper\Table;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Laravel\Forge\Forge;
use Laravel\Forge\Resources\Server;
use Laravel\Forge\Resources\Site;

abstract class BaseCommand extends Command
{
    protected Forge $forge;
    protected Store $config;
    protected array $optionMap = [];
    protected ?Server $serverContext = null;

    public function __construct(?Forge $forge = null)
    {
        parent::__construct();

        $this->config = $this->getFileConfig();
        $this->serverContext = null;

        if ($this instanceof NeedsForge) {
            $this->forge = $forge ?: new Forge($this->config->get('key'));
        }
    }

    protected function table(OutputInterface $output, array $header, array $rows)
    {
        $table = new Table($output);
        $table->setHeaders($header)
            ->setRows($rows);

        $table->render();
    }

    protected function fillData(array $options, array $optionMap = null): array
    {
        $data = [];

        foreach ($optionMap ?: $this->optionMap as $option => $requestKey) {
            if (!isset($options[$option])) {
                continue;
            }

            $data[$requestKey] = $options[$option];
        }

        return $data;
    }

    protected function getFileContent(InputInterface $input, string $option): string
    {
        $filename = $input->hasOption($option) ? $input->getOption($option) : 'php://stdin';

        if (!file_exists($filename)) {
            return $filename;
        } else {
            return file_get_contents($filename);
        }

        if ($filename && ftell(STDIN) !== false) {
            return file_get_contents($filename);
        }

        throw new InvalidArgumentException('This command requires either the "--'.$option.'" option to be set, or an input from STDIN.');
    }

    protected function requireOptions(InputInterface $input, string ...$keys): void
    {
        foreach ($keys as $key) {
            if ($input->hasOption($key)) {
                continue;
            }

            throw new RuntimeException(
                sprintf('The option "%s" is required.', $key)
            );
        }
    }

    protected function getFileConfig(): Store
    {
        $homeDirectory = (
            strncasecmp(PHP_OS, 'WIN', 3) === 0
                ? $_SERVER['USERPROFILE']
                : $_SERVER['HOME']
            ).DIRECTORY_SEPARATOR;

        $visibleConfigFile = $homeDirectory.'forge.json';
        $hiddenConfigFile = $homeDirectory.'.forge.json';

        // If an existing visible configuration file exists, continue using it.
        if (file_exists($visibleConfigFile)) {
            return new Store(new File($visibleConfigFile), new Json());
        }

        // If a hidden configuration file does not exist, create it.
        if (!file_exists($hiddenConfigFile)) {
            file_put_contents($hiddenConfigFile, '{"key":""}');
        }

        // Return the hidden configuration file.
        return new Store(new File($hiddenConfigFile), new Json());
    }

    protected function getServer(InputInterface $input)
    {
        $originalServer = $input->getArgument('server');

        if (is_numeric($originalServer)) {
            return $originalServer;
        }

        // If this is cached, just use it.
        if ($this->serverContext && $this->serverContext->name === $originalServer) {
            return $this->serverContext->id;
        }

        foreach ($this->getServerList() as $server) {
            if ($server->name === $originalServer) {
                $this->serverContext = $server;

                return $server->id;
            }
        }
    }

    protected function getSite(InputInterface $input)
    {
        $originalSite = $input->getArgument('site');

        if (is_numeric($originalSite)) {
            return $originalSite;
        }

        if (! $this->serverContext) {
            return null;
        }

        $sites = $this->getSiteList($this->serverContext->id);

        foreach ($sites as $site) {
            if ($site->name === $originalSite) {
                return $site->id;
            }
        }

    }

    private function getServerList()
    {
        if ($cachedServers = $this->config->get('cache:servers')) {
            return array_map(function ($data) {
                return new Server($data, $this->forge);
            }, $cachedServers);
        }

        $servers = $this->forge->servers();

        $this->config->set('cache:servers', array_map(function ($server) {
            return ['name' => $server->name, 'id' => $server->id];
        }, $servers));

        $this->config->persist();

        return $servers;
    }

    private function getSiteList(int $serverId)
    {
        if ($cachedSites = $this->config->get('cache:server:' . $serverId)) {
            return array_map(function ($data) {
                return new Site($data, $this->forge);
            }, $cachedSites);
        }

        $sites = $this->forge->sites($serverId);

        $this->config->set('cache:server:' . $serverId, array_map(function ($site) {
            return ['name' => $site->name, 'id' => $site->id];
        }, $sites));

        $this->config->persist();

        return $sites;
    }
}
