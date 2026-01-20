<?php

namespace MarceloAnjosDev\FilamentProgressBar\Tests\Support;

use MarceloAnjosDev\FilamentProgressBar\Progress\ProgressManager;

final class ProgressManagerFactory
{
    public static function make(): array
    {
        $repository = new InMemoryProgressRepository();
        $manager = new ProgressManager($repository);

        return [$manager, $repository];
    }
}
