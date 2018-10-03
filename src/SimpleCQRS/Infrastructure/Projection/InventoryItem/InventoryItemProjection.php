<?php

declare(strict_types=1);

namespace SimpleCQRS\Infrastructure\Projection\InventoryItem;

use Prooph\Bundle\EventStore\Projection\ReadModelProjection;
use Prooph\EventStore\Projection\ReadModelProjector;
use SimpleCQRS\Domain\InventoryItemCreated;
use SimpleCQRS\Domain\InventoryItemDeactivated;
use SimpleCQRS\Domain\InventoryItemRenamed;
use SimpleCQRS\Domain\ItemsCheckedInToInventory;
use SimpleCQRS\Domain\ItemsRemovedFromInventory;

final class InventoryItemProjection implements ReadModelProjection
{
    public function project(ReadModelProjector $projector): ReadModelProjector
    {
        $projector->fromStream('event_stream')
            ->when([
                InventoryItemCreated::class => function ($state, InventoryItemCreated $event) {
                    /** @var InventoryItemReadModel $readModel */
                    $readModel = $this->readModel();
                    $readModel->stack('insert', [
                        'id' => $event->aggregateId(),
                        'name' => $event->name(),
                        'count' => 0,
                    ]);
                },
                InventoryItemRenamed::class => function ($state, InventoryItemRenamed $event) {
                    /** @var InventoryItemReadModel $readModel */
                    $readModel = $this->readModel();
                    $readModel->stack(
                        'update',
                        [
                            'name' => $event->name(),
                        ],
                        [
                            'id' => $event->aggregateId(),
                        ]
                    );
                },
                ItemsCheckedInToInventory::class => function ($state, ItemsCheckedInToInventory $event) {
                    /** @var InventoryItemReadModel $readModel */
                    $readModel = $this->readModel();
                    $readModel->stack(
                        'incrementItems',
                        $event->count(),
                        $event->aggregateId()
                    );
                },
                ItemsRemovedFromInventory::class => function ($state, ItemsRemovedFromInventory $event) {
                    /** @var InventoryItemReadModel $readModel */
                    $readModel = $this->readModel();
                    $readModel->stack(
                        'decrementItems',
                        $event->count(),
                        $event->aggregateId()
                    );
                },
                InventoryItemDeactivated::class => function ($state, InventoryItemDeactivated $event) {
                    /** @var InventoryItemReadModel $readModel */
                    $readModel = $this->readModel();
                    $readModel->stack(
                        'deleteItem',
                        [
                            'id' => $event->aggregateId(),
                        ]
                    );
                },
            ]);

        return $projector;
    }
}
