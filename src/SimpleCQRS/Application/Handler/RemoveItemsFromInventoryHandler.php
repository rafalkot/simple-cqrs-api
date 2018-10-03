<?php

declare(strict_types=1);

namespace SimpleCQRS\Application\Handler;

use SimpleCQRS\Application\Command\RemoveItemsFromInventory;
use SimpleCQRS\Domain\InventoryItems;

final class RemoveItemsFromInventoryHandler
{
    /**
     * @var InventoryItems
     */
    private $inventoryItems;

    /**
     * InventoryCommandHandler constructor.
     *
     * @param InventoryItems $inventoryItems
     */
    public function __construct(InventoryItems $inventoryItems)
    {
        $this->inventoryItems = $inventoryItems;
    }

    public function __invoke(RemoveItemsFromInventory $command): void
    {
        $inventoryItem = $this->inventoryItems->getById($command->id());
        $inventoryItem->remove($command->count());
        $this->inventoryItems->save($inventoryItem);
    }
}
