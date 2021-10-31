<?php

namespace App\Support;

use App\Entities\TrelloBoard;
use App\Entities\TrelloCard;
use App\Entities\TrelloList;

class TrelloSelection
{
    public function __construct(
        public ?TrelloBoard $trelloBoard = null,
        public ?TrelloList $trelloList = null,
        public ?TrelloCard $trelloCard = null,
    )
    {
    }


}
