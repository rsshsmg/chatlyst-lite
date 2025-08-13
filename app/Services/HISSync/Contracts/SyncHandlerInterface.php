<?php

namespace App\Services\HISSync\Contracts;

use Illuminate\Support\Collection;

interface SyncHandlerInterface
{
    /**
     * Simpan data dari DTO ke dalam model internal.
     */
    public function handle(object $dto): mixed;

    public function getExecutionTime(): float;
}
