<?php

declare(strict_types=1);

namespace Rahbar\PaymentBridge;

final class ProviderRegistry
{
    /** @var array<string, ProviderAdapter> */
    private array $adapters = [];

    public function register(ProviderAdapter $adapter): void
    {
        $id = $adapter->id();
        if (!preg_match('/^[a-z0-9_-]{2,40}$/', $id)) {
            throw new \InvalidArgumentException('Provider adapter ID is invalid.');
        }
        if (isset($this->adapters[$id])) {
            throw new \LogicException('Provider adapter is already registered: ' . $id);
        }
        $this->adapters[$id] = $adapter;
    }

    public function get(string $id): ProviderAdapter
    {
        if (!isset($this->adapters[$id])) {
            throw new \OutOfBoundsException('Unknown provider adapter: ' . $id);
        }
        return $this->adapters[$id];
    }

    /** @return string[] */
    public function ids(): array
    {
        return array_keys($this->adapters);
    }
}
