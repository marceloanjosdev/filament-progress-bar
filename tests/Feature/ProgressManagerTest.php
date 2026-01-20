<?php

use MarceloAnjosDev\FilamentProgressBar\Tests\Support\ProgressManagerFactory;

it('initializes a progress bar with total and label', function () {
    [$progress, $repo] = ProgressManagerFactory::make();

    $progress->init('k1', totalRecords: 10, autoComplete: false, label: 'Import');

    $item = $repo->get('k1');

    expect($item)->not->toBeNull()
        ->and($item['current'])->toBe(0)
        ->and($item['total'])->toBe(10)
        ->and($item['percent'])->toBe(0)
        ->and($item['label'])->toBe('Import')
        ->and($item['auto_complete'])->toBeFalse();
});

it('updates increments current and computes percent when total exists', function () {
    [$progress, $repo] = ProgressManagerFactory::make();

    $progress->init('k1', totalRecords: 10, autoComplete: false, label: 'Import');
    $progress->update('k1', step: 3, message: 'Working');

    $item = $repo->get('k1');

    expect($item['current'])->toBe(3)
        ->and($item['total'])->toBe(10)
        ->and($item['percent'])->toBe(30)
        ->and($item['message'])->toBe('Working');
});

it('caps current to total', function () {
    [$progress, $repo] = ProgressManagerFactory::make();

    $progress->init('k1', totalRecords: 10, autoComplete: false, label: 'Import');
    $progress->update('k1', step: 999);

    $item = $repo->get('k1');

    expect($item['current'])->toBe(10)
        ->and($item['percent'])->toBe(100);
});

it('does not recreate a progress bar after it was completed by default', function () {
    [$progress, $repo] = ProgressManagerFactory::make();

    $progress->init('k1', totalRecords: 2, autoComplete: false, label: 'Import');

    $progress->complete('k1');

    // Update after completion should not recreate (prevents zombies)
    $progress->update('k1');

    expect($repo->get('k1'))->toBeNull();
});

it('auto completes when current reaches total', function () {
    [$progress, $repo] = ProgressManagerFactory::make();

    $progress->init('k1', totalRecords: 2, autoComplete: true, label: 'Import');

    $progress->update('k1'); // current 1
    expect($repo->get('k1'))->not->toBeNull();

    $progress->update('k1'); // reaches total -> complete
    expect($repo->get('k1'))->toBeNull();
});
