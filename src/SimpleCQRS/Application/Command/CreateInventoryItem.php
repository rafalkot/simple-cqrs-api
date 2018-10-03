<?php

declare(strict_types=1);

namespace SimpleCQRS\Application\Command;

use Assert\Assertion;
use Prooph\Common\Messaging\Command;
use Prooph\Common\Messaging\PayloadTrait;
use Ramsey\Uuid\Uuid;
use Ramsey\Uuid\UuidInterface;

final class CreateInventoryItem extends Command
{
    use PayloadTrait;

    public static function withData(string $id, string $name): CreateInventoryItem
    {
        return new self(compact('id', 'name'));
    }

    public function id(): UuidInterface
    {
        return Uuid::fromString($this->payload['id']);
    }

    public function name(): string
    {
        return $this->payload['name'];
    }

    protected function setPayload(array $payload): void
    {
        Assertion::keyExists($payload, 'id');
        Assertion::uuid($payload['id']);

        Assertion::keyExists($payload, 'name');
        Assertion::string($payload['name']);
        Assertion::notBlank($payload['name']);

        $this->payload = $payload;
    }
}
