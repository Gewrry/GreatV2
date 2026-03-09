<?php

namespace App\Services\RPT;

use Illuminate\Validation\ValidationException;

class ValidationService
{
    /**
     * Throw a validation exception with a specific message.
     */
    protected function fail(string $key, string $message)
    {
        throw ValidationException::withMessages([
            $key => [$message],
        ]);
    }
}
