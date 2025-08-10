<?php

namespace App\Services\HISSync\Contracts;

interface SyncAdapterInterface
{
    /**
     * Ambil seluruh data dari sistem vendor.
     *
     * @return iterable<object> DTO list
     */
    public function getAll(): iterable;

    /**
     * Ambil data berdasarkan ID eksternal.
     *
     * @return object|null DTO
     */
    public function getById(string $externalId): ?object;

    public function getExecutionTime(): float;
}
