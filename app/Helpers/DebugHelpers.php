<?php

use Illuminate\Support\Facades\Log;

// TODO 機能してない
if (! function_exists('debugError')) {
    function debugError(Throwable $error): void
    {
        Log::error([
            'message' => $error->getMessage(),
            'file' => $error->getFile(),
            'line' => $error->getLine(),
            'trace' => $error->getTraceAsString(),
        ]);
    }
}
