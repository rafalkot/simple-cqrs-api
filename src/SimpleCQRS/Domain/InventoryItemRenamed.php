<?php

declare(strict_types=1);

namespace SimpleCQRS\Domain;

use Prooph\EventSourcing\AggregateChanged;

final class InventoryItemRenamed extends AggregateChanged
{
    public function name(): string
    {
        return $this->payload['name'];
    }
}
