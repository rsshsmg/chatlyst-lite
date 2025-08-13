<?php

namespace App\Exceptions;

use Exception;

class DuplicateDataException extends Exception
{
    protected array $duplicates;

    public function __construct(array $duplicates, string $message = 'Duplicate data detected')
    {
        $this->duplicates = $duplicates;
        parent::__construct($message);
    }

    public function getDuplicates(): array
    {
        return $this->duplicates;
    }

    public function render()
    {
        return response()->json([
            'error' => $this->getMessage(),
            'duplicates' => $this->duplicates,
            'code' => 422
        ], 422);
    }
}
