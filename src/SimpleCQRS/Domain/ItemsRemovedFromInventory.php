<?php

declare(strict_types=1);

namespace SimpleCQRS\Domain;

use Prooph\EventSourcing\AggregateChanged;

final class ItemsRemovedFromInventory extends AggregateChanged
{
    public function count(): int
    {
        return $this->payload['count'];
    }
}
