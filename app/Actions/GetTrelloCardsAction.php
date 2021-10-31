<?php

namespace App\Actions;

use App\Entities\TrelloCard;
use App\Services\Trello\TrelloApiGateway;

class GetTrelloCardsAction
{
    public function __construct(protected TrelloApiGateway $trelloApiGateway)
    {
    }

    /**
     * @return \App\Entities\TrelloCard[]
     * @throws \Illuminate\Http\Client\RequestException
     */
    public function execute($listId): array
    {
        return $this->trelloApiGateway->cards()->fromList($listId);
    }
}
