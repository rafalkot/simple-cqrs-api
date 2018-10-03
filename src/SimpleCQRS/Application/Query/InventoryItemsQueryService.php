<?php

declare(strict_types=1);

namespace SimpleCQRS\Application\Query;

interface InventoryItemsQueryService
{
    /**
     * @return InventoryItemListDTO[]
     */
    public function getAll(): array;

    public function getById(string  $id): InventoryItemDetailsDTO;
}
