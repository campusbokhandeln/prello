<?php

namespace App\Entities;

use App\Actions\GetTrelloCardsAction;
use App\Support\TrelloSelection;

class TrelloList implements TrelloEntity
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

    public function setTrelloSelection(TrelloSelection $trelloSelection)
    {
        $trelloSelection->trelloList = $this;
    }

    /**
     * @return TrelloEntity[]|TrelloCard[]
     */
    public function getChildren(): array
    {
        return app(GetTrelloCardsAction::class)->execute($this->id);
    }

    public function getName(): string
    {
        return $this->name;
    }

    public function getTitle(): string
    {
        return sprintf('%s -> Select Card', $this->name);
    }
}
