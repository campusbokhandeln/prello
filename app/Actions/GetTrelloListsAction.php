<?php

namespace App\Actions;

use App\Entities\TrelloList;
use App\Services\Trello\TrelloApiGateway;

class GetTrelloListsAction
{
    public function __construct(protected TrelloApiGateway $trelloApiGateway)
    {
    }

    /**
     * @return \App\Entities\TrelloList[]
     * @throws \Illuminate\Http\Client\RequestException
     */
    public function execute($boardId): array
    {
        return $this->trelloApiGateway->lists()->fromBoard($boardId);
    }
}
