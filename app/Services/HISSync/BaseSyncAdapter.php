<?php

namespace App\Services\HISSync;

class BaseSyncAdapter implements Contracts\SyncAdapterInterface
{
    protected float $startTime = 0.0;
    protected float $endTime = 0.0;

    public function __construct()
    {
        $this->startTime = microtime(true);
    }

    public function getAll(): iterable
    {
        // $this->startTime = microtime(true);
        // Logic to fetch all data from the vendor system
        $this->endTime = microtime(true);
        return []; // Replace with actual data fetching logic
    }

    public function getById(string $externalId): ?object
    {
        // $this->startTime = microtime(true);
        // Logic to fetch data by external ID
        $this->endTime = microtime(true);
        return null; // Replace with actual data fetching logic
    }

    public function getExecutionTime(): float
    {
        return $this->endTime - $this->startTime;
    }
}
