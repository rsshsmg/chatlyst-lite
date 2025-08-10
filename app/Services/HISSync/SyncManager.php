<?php

namespace App\Services\HISSync;

use App\Services\HISSync\Contracts\SyncAdapterInterface;
use App\Services\HISSync\Contracts\SyncHandlerInterface;

class SyncManager
{
    public function __construct(
        protected SyncAdapterInterface $adapter,
        protected SyncHandlerInterface $handler,
    ) {}

    public function syncAll(): void
    {
        foreach ($this->adapter->getAll() as $dto) {
            $this->handler->handle($dto);
        }
    }

    public function syncById(string $externalId): void
    {
        $dto = $this->adapter->getById($externalId);
        if ($dto) {
            $this->handler->handle($dto);
        }
    }

    public function getExecutionTime(): float
    {
        return $this->adapter->getExecutionTime();
    }
}
