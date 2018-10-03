<?php

declare(strict_types=1);

namespace SimpleCQRS\Domain;

use Prooph\EventSourcing\AggregateChanged;

final class InventoryItemDeactivated extends AggregateChanged
{
}
