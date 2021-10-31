<?php

namespace App\Actions;

use App\Entities\TrelloBoard;
use App\Services\Trello\TrelloApiGateway;

class GetTrelloBoardsAction
{

    public function __construct(protected TrelloApiGateway $trelloApiGateway)
    {
    }

    /**
     * @return \App\Entities\TrelloBoard[]
     * @throws \Illuminate\Http\Client\RequestException
     */
    public function execute(): array
    {
        return $this->trelloApiGateway->boards()->all();
    }
}
