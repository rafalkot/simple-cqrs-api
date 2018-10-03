<?php

declare(strict_types=1);

namespace SimpleCQRS\Application\Command;

use Assert\Assertion;
use Prooph\Common\Messaging\Command;
use Prooph\Common\Messaging\PayloadTrait;
use Ramsey\Uuid\Uuid;
use Ramsey\Uuid\UuidInterface;

final class RenameInventoryItem extends Command
{
    use PayloadTrait;

    public static function withData(string $id, string $newName): CreateInventoryItem
    {
        return new self(compact('id', 'newName'));
    }

    public function id(): UuidInterface
    {
        return Uuid::fromString($this->payload['id']);
    }

    public function newName(): string
    {
        return $this->payload['newName'];
    }

    protected function setPayload(array $payload): void
    {
        Assertion::keyExists($payload, 'id');
        Assertion::uuid($payload['id']);

        Assertion::keyExists($payload, 'newName');
        Assertion::string($payload['newName']);
        Assertion::notBlank($payload['newName']);

        $this->payload = $payload;
    }
}
