<?php

namespace MarceloAnjosDev\FilamentProgressBar\Facades;

use Illuminate\Support\Facades\Facade;

/**
 * @see \MarceloAnjosDev\FilamentProgressBar\FilamentProgressBar
 */
class FilamentProgressBar extends Facade
{
    protected static function getFacadeAccessor()
    {
        return \MarceloAnjosDev\FilamentProgressBar\FilamentProgressBar::class;
    }
}
