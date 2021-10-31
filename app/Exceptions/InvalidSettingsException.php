<?php

namespace App\Exceptions;

use Exception;

class InvalidSettingsException extends Exception
{
    public static function create()
    {
        return new self('No settings found, run `prello install`.');
    }
}
