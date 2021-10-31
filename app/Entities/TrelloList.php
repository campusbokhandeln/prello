<?php

namespace App\Entities;

class TrelloList
{
    public function __construct(
        public string $id,
        public string $name,
        public string $idBoard,
    )
    {
    }

    public static function fromRequest($list)
    {
        return new self(
            id: $list['id'],
            name: $list['name'],
            idBoard: $list['idBoard']
        );
    }
}
