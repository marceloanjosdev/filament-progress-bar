<?php

use Illuminate\Support\Facades\Route;
use MarceloAnjosDev\FilamentProgressBar\Http\Controllers\ProgressBarsController;

Route::get(
    config('filament-progress-bar.polling.route', '/filament-progress-bar/progress'),
    ProgressBarsController::class
)->middleware(config('filament-progress-bar.polling.middleware', ['web', 'auth']));
