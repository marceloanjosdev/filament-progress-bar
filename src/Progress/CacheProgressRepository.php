<?php

namespace MarceloAnjosDev\FilamentProgressBar\Progress;

use Illuminate\Contracts\Cache\Repository as CacheRepository;

final readonly class CacheProgressRepository implements ProgressRepositoryInterface
{
    public function __construct(
        private CacheRepository $cache,
        private string $prefix,
        private int $ttlSeconds,
    ) {}

    public function all(): array
    {
        $keys = $this->cache->get($this->keysIndexKey(), []);

        $items = [];
        $keptKeys = [];

        foreach ($keys as $key) {
            $payload = $this->get($key);

            if ($payload === null) {
                continue; // expired -> drop it
            }

            $keptKeys[] = $key;
            $items[] = $payload + ['key' => $key];
        }

        if ($keptKeys !== $keys) {
            $this->cache->put($this->keysIndexKey(), $keptKeys, $this->ttlSeconds);
        }

        return $items;
    }

    public function get(string $key): ?array
    {
        return $this->cache->get($this->itemKey($key));
    }

    public function put(string $key, array $payload): void
    {
        $this->cache->put($this->itemKey($key), $payload, $this->ttlSeconds);

        $keys = $this->cache->get($this->keysIndexKey(), []);
        if (! in_array($key, $keys, true)) {
            $keys[] = $key;
            $this->cache->put($this->keysIndexKey(), $keys, $this->ttlSeconds);
        }
    }

    public function forget(string $key): void
    {
        $this->cache->forget($this->itemKey($key));

        $keys = $this->cache->get($this->keysIndexKey(), []);
        $keys = array_values(array_filter($keys, fn (string $k) => $k !== $key));
        $this->cache->put($this->keysIndexKey(), $keys, $this->ttlSeconds);
    }

    private function keysIndexKey(): string
    {
        return "{$this->prefix}:keys";
    }

    private function itemKey(string $key): string
    {
        return "{$this->prefix}:item:{$key}";
    }
}
