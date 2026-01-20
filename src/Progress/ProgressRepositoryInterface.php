<?php

namespace MarceloAnjosDev\FilamentProgressBar\Progress;

interface ProgressRepositoryInterface
{
    public function all(): array;

    public function get(string $key): ?array;

    public function put(string $key, array $payload): void;

    public function forget(string $key): void;
}
