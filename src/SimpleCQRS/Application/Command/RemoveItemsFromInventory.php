<?php

declare(strict_types=1);

namespace SimpleCQRS\Application\Command;

use Assert\Assertion;
use Prooph\Common\Messaging\Command;
use Prooph\Common\Messaging\PayloadTrait;
use Ramsey\Uuid\Uuid;
use Ramsey\Uuid\UuidInterface;

final class RemoveItemsFromInventory extends Command
{
    use PayloadTrait;

    public static function withData(string $id, int $count): RemoveItemsFromInventory
    {
        return new self(compact('id', 'count'));
    }

    public function id(): UuidInterface
    {
        return Uuid::fromString($this->payload['newId']);
    }

    public function count(): int
    {
        return $this->payload['count'];
    }

    protected function setPayload(array $payload): void
    {
        Assertion::keyExists($payload, 'newId');
        Assertion::uuid($payload['newId']);

        Assertion::keyExists($payload, 'count');
        Assertion::integer($payload['count']);
        Assertion::min($payload['count'], 1);

        $this->payload = $payload;
    }
}
