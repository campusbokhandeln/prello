<?php

namespace App\Entities;

use App\Support\TrelloSelection;

class TrelloNull implements TrelloEntity
{

    public function setTrelloSelection(TrelloSelection $trelloSelection)
    {
        // TODO: Implement setTrelloSelection() method.
    }

    public function getChildren(): array
    {
        return [];
    }

    public function getName(): string
    {
        return 'null';
    }

    public function getTitle(): string
    {
        return 'Select Board';
    }
}
