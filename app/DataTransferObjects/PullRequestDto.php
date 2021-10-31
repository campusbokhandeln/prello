<?php

namespace App\DataTransferObjects;

class PullRequestDto
{
    public function __construct(
        public string $name,
        public string $body = '',
    )
    {
    }
}
