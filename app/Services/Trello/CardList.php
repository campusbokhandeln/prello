<?php

namespace App\Services\Trello;

use App\Entities\TrelloList;

class CardList extends AbstractTrello
{
    /**
     * @return \App\Entities\TrelloList[]
     * @throws \Illuminate\Http\Client\RequestException
     */
    public function fromBoard($boardId)
    {
        return $this->get("boards/{$boardId}", [
            'fields' => 'name',
            'lists' => 'open',
            'list_fields' => 'all',
        ])
            ->pipe(fn($board) => collect($board->get('lists')))
            ->map(function ($list) {
                return TrelloList::fromRequest($list);
            })
            ->toArray();
    }

    /**
     * @return \App\Entities\TrelloList
     * @throws \Illuminate\Http\Client\RequestException
     */
    public function findById($listId): TrelloList
    {
        return $this->get("lists/{$listId}", [
            'fields' => 'name,idBoard',
        ])
            ->pipe(function ($list) {
                return TrelloList::fromRequest($list);
            });
    }
}
