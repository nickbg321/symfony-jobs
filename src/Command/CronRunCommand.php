<?php

namespace App\Command;

use App\Service\CronProviderInterface;
use App\Value\Cron;
use Cron\CronExpression;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Process\Process;

final class CronRunCommand extends Command
{
    private const ALL_ENVIRONMENTS = 'all';

    protected static $defaultName = 'cron:run';
    protected static $defaultDescription = 'Starts all cron jobs which are scheduled to run at the time of executing';

    private CronProviderInterface $cronProvider;
    private string $environment;
    private string $projectDir;

    public function __construct(CronProviderInterface $cronProvider, string $environment, string $projectDir)
    {
        parent::__construct();

        $this->cronProvider = $cronProvider;
        $this->environment = $environment;
        $this->projectDir = $projectDir;
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        foreach ($this->cronProvider->get() as $cron) {
            if ($this->shouldRunOnCurrentEnv($cron)) {
                $expression = CronExpression::factory($cron->getSchedule());
                if ($expression->isDue()) {
                    $this->runCommand($cron);
                    $output->writeln(sprintf('Running command "%s"', $cron->getCommand()));
                }
            }
        }

        return Command::SUCCESS;
    }

    private function shouldRunOnCurrentEnv(Cron $cron): bool
    {
        $allowedEnvs = [$this->environment, self::ALL_ENVIRONMENTS];
        $intersect = array_intersect($allowedEnvs, $cron->getEnvironments());

        return !empty($intersect);
    }

    private function runCommand(Cron $cron): void
    {
        $command = sprintf('php %s/bin/console %s', $this->projectDir, $cron->getCommand());
        $commandChunks = array_filter(explode(' ', $command));

        $process = new Process($commandChunks);
        $process->run();
    }
}
