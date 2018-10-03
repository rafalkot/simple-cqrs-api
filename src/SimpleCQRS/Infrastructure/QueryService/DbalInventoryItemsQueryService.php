<?php

declare(strict_types=1);

namespace SimpleCQRS\Infrastructure\QueryService;

use Doctrine\DBAL\Connection;
use SimpleCQRS\Application\Query\InventoryItemDetailsDTO;
use SimpleCQRS\Application\Query\InventoryItemListDTO;
use SimpleCQRS\Application\Query\InventoryItemsQueryService;
use SimpleCQRS\Infrastructure\Projection\Table;

final class DbalInventoryItemsQueryService implements InventoryItemsQueryService
{
    private $connection;

    public function __construct(Connection $connection)
    {
        $this->connection = $connection;
        $this->connection->setFetchMode(\PDO::FETCH_OBJ);
    }

    public function getAll(): array
    {
        $sql = sprintf('SELECT * FROM %s', Table::INVENTORY_ITEM);

        return array_map(
            function (\stdClass $row) {
                $dto = new InventoryItemListDTO();
                $dto->id = $row->id;
                $dto->name = $row->name;

                return $dto;
            },
            $this->connection->fetchAll($sql)
        );
    }

    public function getById(string $id): InventoryItemDetailsDTO
    {
        $sql = sprintf('SELECT * FROM %s where id = :id', Table::INVENTORY_ITEM);
        $stmt = $this->connection->prepare($sql);
        $stmt->bindValue('id', $id);
        $stmt->execute();
        $result = $stmt->fetch();

        if (false === $result) {
            return null;
        }

        $dto = new InventoryItemDetailsDTO();
        $dto->id = $result->id;
        $dto->name = $result->name;
        $dto->count = (int) $result->count;

        return $dto;
    }
}
