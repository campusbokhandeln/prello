<?php

namespace App\Entities;

class TrelloBoard
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
}
