<?php

namespace App\Actions;

use App\DataTransferObjects\NewTrelloCardDto;
use App\Entities\TrelloCard;
use App\Entities\TrelloList;
use App\Services\Trello\TrelloApiGateway;

class CreateTrelloCardAction
{
    public function __construct(protected TrelloApiGateway $trelloApiGateway)
    {
    }

    /**
     * @return \App\Entities\TrelloCard
     * @throws \Illuminate\Http\Client\RequestException
     */
    public function execute(TrelloList $list, NewTrelloCardDto $card): TrelloCard
    {
        return $this->trelloApiGateway->card()->create($list, $card);
    }
}
