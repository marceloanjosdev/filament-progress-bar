<?php

namespace MarceloAnjosDev\FilamentProgressBar\Tests\Support;

use MarceloAnjosDev\FilamentProgressBar\Progress\ProgressRepositoryInterface;

final class InMemoryProgressRepository implements ProgressRepositoryInterface
{
    /** @var array<string, array<string, mixed>> */
    private array $items = [];

    /** @return array<int, array<string, mixed>> */
    public function all(): array
    {
        $result = [];

        foreach ($this->items as $key => $payload) {
            $result[] = $payload + ['key' => $key];
        }

        return $result;
    }

    /** @return array<string, mixed>|null */
    public function get(string $key): ?array
    {
        return $this->items[$key] ?? null;
    }

    /** @param array<string, mixed> $payload */
    public function put(string $key, array $payload): void
    {
        $this->items[$key] = $payload;
    }

    public function forget(string $key): void
    {
        unset($this->items[$key]);
    }
}
