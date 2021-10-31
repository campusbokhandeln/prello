<?php

namespace App\Entities;

use App\Support\TrelloSelection;

class TrelloCard implements TrelloEntity
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

    public function setTrelloSelection(TrelloSelection $trelloSelection)
    {
        $trelloSelection->trelloCard = $this;
    }

    /**
     * @return TrelloEntity[]
     */
    public function getChildren(): array
    {
        return [];
    }

    public function getName(): string
    {
        return $this->name;
    }

    public function getTitle(): string
    {
        return 'Select Card';
    }
}
