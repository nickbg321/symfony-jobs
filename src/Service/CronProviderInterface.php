<?php

namespace App\Service;

use App\Value\Cron;

interface CronProviderInterface
{
    /**
     * @return Cron[]
     */
    public function get(): array;
}
