<?php

namespace App\Services\HISSync;

use App\Services\HISSync\Contracts\SyncAdapterInterface;
use App\Services\HISSync\Contracts\SyncHandlerInterface;

class SyncManager
{
    protected float $startTime = 0.0;
    protected float $endTime = 0.0;

    public function __construct(
        protected SyncAdapterInterface $adapter,
        protected SyncHandlerInterface $handler,
    ) {}

    public function syncAll(): void
    {
        $this->startTime = microtime(true);

        foreach ($this->adapter->getAll() as $dto) {
            $this->handler->handle($dto);
        }

        $this->endTime = microtime(true);
    }

    public function syncById(string $externalId): void
    {
        $this->startTime = microtime(true);

        $dto = $this->adapter->getById($externalId);
        if ($dto) {
            $this->handler->handle($dto);
        }

        $this->endTime = microtime(true);
    }

    public function getExecutionTime(): float
    {
        return $this->endTime - $this->startTime;
    }
}
