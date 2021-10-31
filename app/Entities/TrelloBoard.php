<?php

namespace App\Entities;

use App\Support\TrelloSelection;

class TrelloBoard implements TrelloEntity
{

    /**
     * @param string $id
     * @param string $name
     * @param string $url
     * @param TrelloList[] $lists
     */
    public function __construct(
        public string $id,
        public string $name,
        public string $url,
        public array $lists = [],
    )
    {
    }

    public static function fromRequest($board)
    {
        $lists = collect($board['lists'])->map(fn($list) => new TrelloList(...$list));

        return new self(
            id: $board['id'],
            name: $board['name'],
            url: $board['url'],
            lists: $lists->toArray(),
        );
    }

    public function setTrelloSelection(TrelloSelection $trelloSelection)
    {
        $trelloSelection->trelloBoard = $this;
    }

    /**
     * @return TrelloEntity[]|TrelloList[]
     */
    public function getChildren(): array
    {
        return $this->lists;
    }

    public function getName(): string
    {
        return $this->name;
    }

    public function getTitle(): string
    {
        return sprintf('%s -> Select List', $this->name);
    }
}
