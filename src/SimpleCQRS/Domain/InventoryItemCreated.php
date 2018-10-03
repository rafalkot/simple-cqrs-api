<?php

declare(strict_types=1);

namespace SimpleCQRS\Domain;

use Prooph\EventSourcing\AggregateChanged;

final class InventoryItemCreated extends AggregateChanged
{
    public function name(): string
    {
        return $this->payload['name'];
    }
}
