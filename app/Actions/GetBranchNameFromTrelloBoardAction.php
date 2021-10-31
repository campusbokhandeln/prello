<?php

namespace App\Actions;

use App\Entities\TrelloBoard;
use App\Entities\TrelloCard;
use Illuminate\Support\Str;

class GetBranchNameFromTrelloBoardAction
{
    public function execute(TrelloBoard $board, TrelloCard $card): string
    {
        return Str::slug($board->name) . '/' . Str::slug($card->idShort) . '-' . Str::slug($card->name);
    }
}
