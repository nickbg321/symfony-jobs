<?php

namespace App\Job\Service\CronProvider;

use App\Job\Value\Cron;

interface CronProviderInterface
{
    /**
     * @return Cron[]
     */
    public function get(): array;
}
