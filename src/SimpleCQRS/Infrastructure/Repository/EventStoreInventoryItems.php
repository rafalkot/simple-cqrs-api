<?php

declare(strict_types=1);

namespace SimpleCQRS\Infrastructure\Repository;

use Prooph\EventSourcing\Aggregate\AggregateRepository;
use Ramsey\Uuid\UuidInterface;
use SimpleCQRS\Domain\InventoryItem;
use SimpleCQRS\Domain\InventoryItems;

final class EventStoreInventoryItems extends AggregateRepository implements InventoryItems
{
    public function save(InventoryItem $inventoryItem): void
    {
        $this->saveAggregateRoot($inventoryItem);
    }

    public function getById(UuidInterface $uuid): InventoryItem
    {
        /** @var InventoryItem|null $item */
        $item = $this->getAggregateRoot($uuid->toString());

        if (!$item) {
            throw new \Exception('Aggregate '.$uuid->toString().' not found');
        }

        return $item;
    }
}
