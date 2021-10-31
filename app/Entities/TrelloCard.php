<?php

namespace App\Entities;

class TrelloCard
{
    public function __construct(
        public string $id,
        public string $name,
        public string $idShort,
        public string $url,
        public string $shortUrl,
        public string $idList,
    )
    {
    }

    public static function fromRequest($card)
    {
        return new self(
            id: $card['id'],
            name: $card['name'],
            idShort: $card['idShort'],
            url: $card['url'],
            shortUrl: $card['shortUrl'],
            idList: $card['idList'],
        );
    }
}
