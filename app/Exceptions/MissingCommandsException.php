<?php

namespace App\Exceptions;

use Exception;

class MissingCommandsException extends Exception
{
    public static function create()
    {
        return new self('Prello needs git and gh command line commands installed');
    }
}
