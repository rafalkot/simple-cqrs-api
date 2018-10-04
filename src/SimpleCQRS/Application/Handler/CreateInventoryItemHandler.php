<?php

declare(strict_types=1);

namespace SimpleCQRS\Application\Handler;

use SimpleCQRS\Application\Command\CreateInventoryItem;
use SimpleCQRS\Domain\InventoryItem;
use SimpleCQRS\Domain\InventoryItems;

final class CreateInventoryItemHandler
{
    /**
     * @var InventoryItems
     */
    private $inventoryItems;

    public function __construct(InventoryItems $inventoryItems)
    {
        $this->inventoryItems = $inventoryItems;
    }

    public function __invoke(CreateInventoryItem $command): void
    {
        $inventoryItem = InventoryItem::create($command->id(), $command->name());
        $this->inventoryItems->save($inventoryItem);
    }
}
