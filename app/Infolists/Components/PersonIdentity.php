<?php

namespace App\Infolists\Components;

use App\Models\Identity;
use Filament\Infolists\Components\Concerns\HasContainerGridLayout;
use Filament\Infolists\Components\Entry;
use Filament\Support\Concerns\CanBeContained;
use Illuminate\Database\Eloquent\Model;

class PersonIdentity extends Entry
{
    use CanBeContained;
    use HasContainerGridLayout;

    protected string $view = 'infolists.components.person-identity';

    protected array $identity = [];

    public function getChildComponentContainers(bool $withHidden = false): array
    {
        if ((! $withHidden) && $this->isHidden()) {
            return [];
        }

        $containers = [];

        foreach ($this->getState() ?? [] as $itemKey => $itemData) {
            $this->identity[$itemKey] = $this->setIdentity($itemKey, $itemData);

            $container = $this
                ->getChildComponentContainer()
                ->getClone()
                ->statePath($itemKey)
                ->inlineLabel(false);

            if ($itemData instanceof Model) {
                $container->record($itemData);
            }

            $containers[$itemKey] = $container;
        }

        return $containers;
    }

    public function setIdentity(string $key, Model $identity): static
    {
        $this->identity[$key] = $identity;

        return $this;
    }

    public function getIdentity(): array
    {
        return $this->identity;
    }
}
