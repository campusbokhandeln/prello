<?php

namespace App\Actions;

use App\Entities\TrelloCard;
use App\Services\Trello\TrelloApiGateway;

class CreateTrelloCardUrlAttachmentAction
{
    public function __construct(protected TrelloApiGateway $trelloApiGateway)
    {
    }

    public function execute(TrelloCard $card, string $pullRequestUrl)
    {
        return $this->trelloApiGateway->card()->attachUrl($card->id, $card->name, $pullRequestUrl);
    }
}
