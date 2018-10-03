<?php

declare(strict_types=1);

namespace SimpleCQRS\Application\Query;

final class InventoryItemDetailsDTO
{
    /**
     * @var string
     */
    public $id;
    /**
     * @var string
     */
    public $name;
    /**
     * @var int
     */
    public $count;
}
