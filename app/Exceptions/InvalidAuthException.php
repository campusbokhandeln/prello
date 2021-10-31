<?php

namespace App\Exceptions;

use Exception;

class InvalidAuthException extends Exception
{
    public static function create()
    {
        return new self('No trello token, run `prello install`.');
    }
}
