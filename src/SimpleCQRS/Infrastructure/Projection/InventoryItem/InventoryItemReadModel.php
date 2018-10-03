<?php

declare(strict_types=1);

namespace SimpleCQRS\Infrastructure\Projection\InventoryItem;

use Doctrine\DBAL\Connection;
use Prooph\EventStore\Projection\AbstractReadModel;
use SimpleCQRS\Infrastructure\Projection\Table;

final class InventoryItemReadModel extends AbstractReadModel
{
    /**
     * @var Connection
     */
    private $connection;

    public function __construct(Connection $connection)
    {
        $this->connection = $connection;
    }

    public function init(): void
    {
        $tableName = Table::INVENTORY_ITEM;
        $sql = <<<EOT
CREATE TABLE `$tableName` (
  `id` varchar(36) COLLATE utf8_unicode_ci NOT NULL,
  `name` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `count` int(11) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
EOT;
        $statement = $this->connection->prepare($sql);
        $statement->execute();
    }

    public function isInitialized(): bool
    {
        $tableName = Table::INVENTORY_ITEM;
        $sql = "SHOW TABLES LIKE '$tableName';";
        $statement = $this->connection->prepare($sql);
        $statement->execute();
        $result = $statement->fetch();
        if (false === $result) {
            return false;
        }

        return true;
    }

    public function reset(): void
    {
        $tableName = Table::INVENTORY_ITEM;
        $sql = "TRUNCATE TABLE $tableName;";
        $statement = $this->connection->prepare($sql);
        $statement->execute();
    }

    public function delete(): void
    {
        $tableName = Table::INVENTORY_ITEM;
        $sql = "DROP TABLE $tableName;";
        $statement = $this->connection->prepare($sql);
        $statement->execute();
    }

    protected function insert(array $data): void
    {
        $this->connection->insert(Table::INVENTORY_ITEM, $data);
    }

    protected function update(array $data, array $identifier): void
    {
        $this->connection->update(
            Table::INVENTORY_ITEM,
            $data,
            $identifier
        );
    }

    protected function deleteItem(array $identifier): void
    {
        $this->connection->delete(
            Table::INVENTORY_ITEM,
            $identifier
        );
    }

    protected function incrementItems(int $count, string $identifier): void
    {
        $tableName = Table::INVENTORY_ITEM;
        $sql = "UPDATE $tableName SET `count` = `count` + :count WHERE id = :id";

        $this->connection->executeQuery($sql, ['count' => $count, 'id' => $identifier]);
    }

    protected function decrementItems(int $count, string $identifier): void
    {
        $tableName = Table::INVENTORY_ITEM;
        $sql = "UPDATE $tableName SET `count` = `count` - :count WHERE id = :id";

        $this->connection->executeQuery($sql, ['count' => $count, 'id' => $identifier]);
    }
}
