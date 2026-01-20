<?php

namespace MarceloAnjosDev\FilamentProgressBar\Progress;

final readonly class ProgressManager
{
    public function __construct(
        private ProgressRepositoryInterface $repository,
    ) {}

    public function all(): array
    {
        return $this->repository->all();
    }

    public function get(string $key): ?array
    {
        return $this->repository->get($key);
    }

    public function complete(string $key): void
    {
        $this->repository->forget($key);
    }

    public function init(string $key, int $totalRecords, bool $autoComplete = false, ?string $label = null): void
    {
        $totalRecords = $this->normalizeNonNegativeInt($totalRecords);

        $this->repository->put($key, [
            'current' => 0,
            'total' => $totalRecords,
            'percent' => 0,
            'label' => $label,
            'message' => null,
            'auto_complete' => $autoComplete,
            'updated_at' => now()->utc()->toIso8601String(),
        ]);
    }

    public function update(string $key, int $step = 1, string $message = ''): void
    {
        $step = max(0, $step);

        $existing = $this->repository->get($key);

        // If the progress bar does not exist anymore, do not recreate it implicitly.
        // This avoids "zombie" bars when multiple jobs update after completion.
        if ($existing === null) {
            return;
        }

        $current = is_int($existing['current'] ?? null) ? $existing['current'] : 0;
        $total = is_int($existing['total'] ?? null) ? $existing['total'] : null;
        $label = is_string($existing['label'] ?? null) ? $existing['label'] : null;

        $autoComplete = is_bool($existing['auto_complete'] ?? null)
            ? $existing['auto_complete']
            : false;

        $current += $step;

        if (is_int($total) && $total > 0) {
            $current = min($current, $total);
        }

        $this->repository->put($key, [
            'current' => $current,
            'total' => $total,
            'percent' => $this->computePercent($current, $total),
            'label' => $label,
            'auto_complete' => $autoComplete,
            'message' => $message !== '' ? $message : null,
            'updated_at' => now()->utc()->toIso8601String(),
        ]);

        if ($autoComplete && is_int($total) && $total > 0 && $current >= $total) {
            $this->complete($key);
        }
    }

    private function normalizeNonNegativeInt(?int $value): ?int
    {
        if ($value === null) {
            return null;
        }

        return max(0, $value);
    }

    private function computePercent(int $current, ?int $total): int
    {
        if (! is_int($total) || $total <= 0) {
            return 0;
        }

        return (int) max(0, min(100, round(($current / $total) * 100)));
    }
}
