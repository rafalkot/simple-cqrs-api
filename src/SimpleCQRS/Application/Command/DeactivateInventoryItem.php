<?php

declare(strict_types=1);

namespace SimpleCQRS\Application\Command;

use Assert\Assertion;
use Prooph\Common\Messaging\Command;
use Prooph\Common\Messaging\PayloadTrait;
use Ramsey\Uuid\Uuid;
use Ramsey\Uuid\UuidInterface;

final class DeactivateInventoryItem extends Command
{
    use PayloadTrait;

    public static function with(string $id): DeactivateInventoryItem
    {
        return new self([
            'id' => $id,
        ]);
    }

    public function id(): UuidInterface
    {
        return Uuid::fromString($this->payload['id']);
    }

    protected function setPayload(array $payload): void
    {
        Assertion::keyExists($payload, 'id');
        Assertion::uuid($payload['id']);

        $this->payload = $payload;
    }
}
