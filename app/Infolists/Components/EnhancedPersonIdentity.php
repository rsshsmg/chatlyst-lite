<?php

namespace App\Infolists\Components;

use Filament\Infolists\Components\RepeatableEntry;
use Filament\Infolists\Components\Entry;
use Illuminate\Database\Eloquent\Model;

class EnhancedPersonIdentity extends RepeatableEntry
{
    protected string $view = 'infolists.components.person-identity';

    protected ?int $currentIndex = null;

    public function getCurrentRecord(): ?Model
    {
        $records = $this->getRecords();
        $index = $this->getCurrentIndex();

        return $records[$index] ?? null;
    }

    public function getCurrentIndex(): int
    {
        return $this->currentIndex ?? 0;
    }

    public function setCurrentIndex(int $index): static
    {
        $this->currentIndex = $index;
        return $this;
    }

    public function getRecords(): array
    {
        return $this->getState() ?? [];
    }

    public function getRecordCount(): int
    {
        return count($this->getRecords());
    }

    public function hasRecords(): bool
    {
        return $this->getRecordCount() > 0;
    }

    public function getRecordAt(int $index): ?Model
    {
        $records = $this->getRecords();
        return $records[$index] ?? null;
    }

    public function isFirstRecord(): bool
    {
        return $this->getCurrentIndex() === 0;
    }

    public function isLastRecord(): bool
    {
        return $this->getCurrentIndex() === ($this->getRecordCount() - 1);
    }

    public function nextRecord(): static
    {
        if (!$this->isLastRecord()) {
            $this->currentIndex++;
        }
        return $this;
    }

    public function previousRecord(): static
    {
        if (!$this->isFirstRecord()) {
            $this->currentIndex--;
        }
        return $this;
    }
}
