<?php

namespace MarceloAnjosDev\FilamentProgressBar\Http\Controllers;

use Illuminate\Http\JsonResponse;
use MarceloAnjosDev\FilamentProgressBar\Progress\ProgressManager;

final class ProgressBarsController
{
    public function __invoke(ProgressManager $progressManager): JsonResponse
    {
        return response()->json([
            'items' => $progressManager->all(),
        ]);
    }
}
