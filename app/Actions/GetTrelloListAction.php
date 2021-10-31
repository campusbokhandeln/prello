<?php

namespace App\Actions;

use App\Entities\TrelloList;
use App\Services\Trello\TrelloApiGateway;

class GetTrelloListAction
{
    public function __construct(protected TrelloApiGateway $trelloApiGateway)
    {
    }

    /**
     * @return \App\Entities\TrelloList
     * @throws \Illuminate\Http\Client\RequestException
     */
    public function execute($listId): TrelloList
    {
        return $this->trelloApiGateway->list()->findById($listId);
    }
}
