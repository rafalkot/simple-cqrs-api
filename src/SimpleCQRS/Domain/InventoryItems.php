<?php

declare(strict_types=1);

namespace SimpleCQRS\Domain;

use Ramsey\Uuid\UuidInterface;

interface InventoryItems
{
    public function save(InventoryItem $inventoryItem): void;

    public function getById(UuidInterface $uuid): InventoryItem;
}
