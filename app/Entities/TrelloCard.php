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
        public bool   $subscribed,
        public array $members,
    )
    {
    }

    public static function fromRequest($card)
    {
        $members = collect(data_get($card, 'members', []))
            ->map(fn($m) => TrelloMember::fromRequest($m));

        return new self(
            id: $card['id'],
            name: $card['name'],
            idShort: $card['idShort'],
            url: $card['url'],
            shortUrl: $card['shortUrl'],
            idList: $card['idList'],
            subscribed: $card['subscribed'],
            members: $members->toArray(),
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
        if (!count($this->members)) {
            return sprintf("%s", $this->name);
        }

        $members = collect($this->members)
            ->pluck('initials')
            ->join(', ');

        return sprintf("%s (%s)", $this->name, $members);
    }

    public function getTitle(): string
    {
        return 'Select Card';
    }
}
