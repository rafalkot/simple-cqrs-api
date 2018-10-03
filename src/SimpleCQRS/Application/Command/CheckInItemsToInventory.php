<?php

declare(strict_types=1);

namespace SimpleCQRS\Application\Command;

use Assert\Assertion;
use Prooph\Common\Messaging\Command;
use Prooph\Common\Messaging\PayloadTrait;
use Ramsey\Uuid\Uuid;
use Ramsey\Uuid\UuidInterface;

final class CheckInItemsToInventory extends Command
{
    use PayloadTrait;

    public static function withData(string $id, int $count): CheckInItemsToInventory
    {
        return new self(compact('id', 'count'));
    }

    public function id(): UuidInterface
    {
        return Uuid::fromString($this->payload['id']);
    }

    public function count(): int
    {
        return $this->payload['count'];
    }

    protected function setPayload(array $payload): void
    {
        Assertion::keyExists($payload, 'id');
        Assertion::uuid($payload['id']);

        Assertion::keyExists($payload, 'count');
        Assertion::integer($payload['count']);
        Assertion::min($payload['count'], 1);

        $this->payload = $payload;
    }
}
