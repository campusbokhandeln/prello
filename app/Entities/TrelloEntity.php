<?php

namespace App\Entities;

use App\Support\TrelloSelection;

interface TrelloEntity
{
    public function setTrelloSelection(TrelloSelection $trelloSelection);

    /**
     * @return TrelloEntity[]
     */
    public function getChildren(): array;

    public function getName(): string;

    public function getTitle(): string;
}
