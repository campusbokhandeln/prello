<?php

namespace App\DataTransferObjects;

class NewTrelloCardDto
{
    public function __construct(
        public string $name,
        public string $desc = '',
    )
    {
    }
}
