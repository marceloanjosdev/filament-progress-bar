<?php

namespace MarceloAnjosDev\FilamentProgressBar\Commands;

use Illuminate\Console\Command;

class FilamentProgressBarCommand extends Command
{
    public $signature = 'filament-progress-bar';

    public $description = 'My command';

    public function handle(): int
    {
        $this->comment('All done');

        return self::SUCCESS;
    }
}
