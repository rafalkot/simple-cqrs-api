<?php

declare(strict_types=1);

namespace SimpleCQRS\Application\Handler;

use SimpleCQRS\Application\Command\CheckInItemsToInventory;
use SimpleCQRS\Domain\InventoryItems;

final class CheckInItemsToInventoryHandler
{
    /**
     * @var InventoryItems
     */
    private $inventoryItems;

    public function __construct(InventoryItems $inventoryItems)
    {
        $this->inventoryItems = $inventoryItems;
    }

    public function __invoke(CheckInItemsToInventory $command): void
    {
        $inventoryItem = $this->inventoryItems->getById($command->id());
        $inventoryItem->checkIn($command->count());
        $this->inventoryItems->save($inventoryItem);
    }
}
