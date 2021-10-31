<?php

namespace App\Services\Trello;

use App\DataTransferObjects\NewTrelloCardDto;
use App\Entities\TrelloCard;
use App\Entities\TrelloList;

class Card extends AbstractTrello
{
    /**
     * @return \App\Entities\TrelloCard[]
     * @throws \Illuminate\Http\Client\RequestException
     */

    public function fromList($listId)
    {
        return
            $this->get("lists/{$listId}/cards", [
                'fields' => 'all',
            ])
                ->map(function ($card) {
                    return TrelloCard::fromRequest($card);
                })
                ->toArray();
    }

    /**
     * @return TrelloCard
     * @throws \Illuminate\Http\Client\RequestException
     */
    public function findById($cardId)
    {
        return $this->get("cards/{$cardId}")
            ->pipe(function ($card) {
                return TrelloCard::fromRequest($card);
            });
    }


    public function attachUrl($cardId, $name, $url)
    {
        return $this->post("cards/{$cardId}/attachments", [
            'name' => $name,
            'url' => $url,
        ])->toArray();
    }

    public function create(TrelloList $list, NewTrelloCardDto $card): TrelloCard
    {
        return $this->post("cards", [
            'name' => $card->name,
            'idList' => $list->id,
            'desc' => $card->desc,
        ])
            ->pipe(function ($card) {
                return TrelloCard::fromRequest($card);
            });
    }
}
