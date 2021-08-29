<?php

namespace App\Job\Value;

final class Cron
{
    private string $command;
    private string $schedule;
    private array $environments;

    public function __construct(string $command, string $schedule, array $environments)
    {
        $this->command = $command;
        $this->schedule = $schedule;
        $this->environments = $environments;
    }

    public function getCommand(): string
    {
        return $this->command;
    }

    public function getSchedule(): string
    {
        return $this->schedule;
    }

    public function getEnvironments(): array
    {
        return $this->environments;
    }
}
