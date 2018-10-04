<?php

declare(strict_types=1);

namespace SimpleCQRS\Application\Handler;

use SimpleCQRS\Application\Command\RenameInventoryItem;
use SimpleCQRS\Domain\InventoryItems;

final class RenameInventoryItemHandler
{
    /**
     * @var InventoryItems
     */
    private $inventoryItems;

    public function __construct(InventoryItems $inventoryItems)
    {
        $this->inventoryItems = $inventoryItems;
    }

    public function __invoke(RenameInventoryItem $command): void
    {
        $inventoryItem = $this->inventoryItems->getById($command->id());
        $inventoryItem->changeName($command->newName());
        $this->inventoryItems->save($inventoryItem);
    }
}
