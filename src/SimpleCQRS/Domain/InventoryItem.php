<?php

declare(strict_types=1);

namespace SimpleCQRS\Domain;

use Prooph\EventSourcing\AggregateChanged;
use Prooph\EventSourcing\AggregateRoot;
use Ramsey\Uuid\Uuid;
use Ramsey\Uuid\UuidInterface;

final class InventoryItem extends AggregateRoot
{
    /**
     * @var UuidInterface
     */
    private $id;

    /**
     * @var bool
     */
    private $activated;

    /**
     * @return InventoryItem
     */
    public static function create(UuidInterface $id, string $name)
    {
        if (empty($name)) {
            throw new \InvalidArgumentException('Empty name');
        }

        $instance = new self();
        $instance->recordThat(
            InventoryItemCreated::occur(
                $id->toString(),
                [
                    'name' => $name,
                ]
            )
        );

        return $instance;
    }

    public function changeName(string $newName): void
    {
        if (empty($newName)) {
            throw new \InvalidArgumentException('Empty name');
        }

        $this->recordThat(
            InventoryItemRenamed::occur(
                $this->aggregateId(),
                [
                    'name' => $newName,
                ]
            )
        );
    }

    public function remove(int $count): void
    {
        if ($count <= 0) {
            throw new \InvalidArgumentException('Cant remove negative count from inventory');
        }

        $this->recordThat(
            ItemsRemovedFromInventory::occur(
                $this->aggregateId(),
                [
                    'count' => $count,
                ]
            )
        );
    }

    public function checkIn(int $count): void
    {
        if ($count <= 0) {
            throw new \InvalidArgumentException('Must have a count greater than 0 to add to inventory');
        }

        $this->recordThat(
            ItemsCheckedInToInventory::occur(
                $this->aggregateId(),
                [
                    'count' => $count,
                ]
            )
        );
    }

    public function deactivate(): void
    {
        if (false === $this->activated) {
            throw new \InvalidArgumentException('Already deactivated');
        }

        $this->recordThat(InventoryItemDeactivated::occur($this->aggregateId()));
    }

    public function id(): UuidInterface
    {
        return $this->id;
    }

    protected function aggregateId(): string
    {
        return $this->id->toString();
    }

    protected function apply(AggregateChanged $event): void
    {
        switch (get_class($event)) {
            case InventoryItemCreated::class:
                $this->id = Uuid::fromString($event->aggregateId());
                $this->activated = true;
                break;
            case InventoryItemDeactivated::class:
                $this->activated = false;
                break;
        }
    }
}
