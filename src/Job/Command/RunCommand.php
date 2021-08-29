<?php

namespace App\Job\Command;

use App\Job\Service\CronProvider\CronProviderInterface;
use App\Job\Service\JobRunner;
use App\Job\Value\Cron;
use Cron\CronExpression;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

final class RunCommand extends Command
{
    private const ALL_ENVIRONMENTS = 'all';

    protected static $defaultName = 'job:run';
    protected static $defaultDescription = 'Starts all cron jobs which are scheduled to run at the time of executing';

    private CronProviderInterface $cronProvider;
    private JobRunner $jobRunner;
    private string $environment;

    public function __construct(CronProviderInterface $cronProvider, JobRunner $jobRunner, string $environment)
    {
        parent::__construct();

        $this->cronProvider = $cronProvider;
        $this->jobRunner = $jobRunner;
        $this->environment = $environment;
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        foreach ($this->cronProvider->get() as $cron) {
            if ($this->shouldRunOnCurrentEnv($cron)) {
                $expression = CronExpression::factory($cron->getSchedule());
                if ($expression->isDue()) {
                    $this->jobRunner->run($cron->getCommand());
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
}
