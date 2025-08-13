<?php

namespace App\Services\HISSync;

class BaseSyncHandler implements Contracts\SyncHandlerInterface
{
    protected float $startTime = 0.0;
    protected float $endTime = 0.0;

    public function handle(object $jobTitles): mixed
    {
        $this->startTime = microtime(true);
        // Logic to fetch all data from the vendor system
        $this->endTime = microtime(true);
        return []; // Replace with actual data fetching logic
    }

    public function getExecutionTime(): float
    {
        return $this->endTime - $this->startTime;
    }
}
