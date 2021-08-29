<?php

namespace App\Service;

use App\Value\Cron;
use RuntimeException;

final class JsonCronProvider implements CronProviderInterface
{
    private const COMMAND_COLUMN = 'command';
    private const SCHEDULE_COLUMN = 'schedule';
    private const ENVIRONMENTS_COLUMN = 'environments';

    private string $configFile;

    public function __construct(string $configFile)
    {
        $this->configFile = $configFile;
    }

    public function get(): array
    {
        $configData = $this->parseConfigFile();

        $cronList = [];
        foreach ($configData as $item) {
            $cronList[] = new Cron(
                $item[self::COMMAND_COLUMN],
                $item[self::SCHEDULE_COLUMN],
                $item[self::ENVIRONMENTS_COLUMN],
            );
        }

        return $cronList;
    }

    private function parseConfigFile(): array
    {
        if (!file_exists($this->configFile)) {
            throw new RuntimeException(sprintf('Config file "%s" does not exist', $this->configFile));
        }

        $config = json_decode(file_get_contents($this->configFile), true);
        if (!$config) {
            throw new RuntimeException(sprintf('Config file "%s" does not contain valid JSON', $this->configFile));
        }

        return $config;
    }
}
