<?php

declare(strict_types=1);

namespace SimpleCQRS\Application\Handler;

use SimpleCQRS\Application\Command\DeactivateInventoryItem;
use SimpleCQRS\Domain\InventoryItems;

final class DeactivateInventoryItemHandler
{
    /**
     * @var InventoryItems
     */
    private $inventoryItems;

    public function __construct(InventoryItems $inventoryItems)
    {
        $this->inventoryItems = $inventoryItems;
    }

    public function __invoke(DeactivateInventoryItem $command): void
    {
        $inventoryItem = $this->inventoryItems->getById($command->id());
        $inventoryItem->deactivate();
        $this->inventoryItems->save($inventoryItem);
    }
}
