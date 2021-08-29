<?php

namespace App\Job\Service;

use Symfony\Component\Process\Process;

class JobRunner
{
    private string $projectDir;

    public function __construct(string $projectDir)
    {
        $this->projectDir = $projectDir;
    }

    public function run(string $job): void
    {
        $command = sprintf('php %s/bin/console %s', $this->projectDir, $job);
        $commandChunks = array_filter(explode(' ', $command));

        $process = new Process($commandChunks);
        $process->run();
    }
}
