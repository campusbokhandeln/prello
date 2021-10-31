<?php

namespace App\Services\Trello;

use App\Entities\TrelloBoard;

class Board extends AbstractTrello
{
    /**
     * @return \App\Entities\TrelloBoard[]
     * @throws \Illuminate\Http\Client\RequestException
     */
    public function all(): array
    {
        return $this->get('members/me/boards', [
            'fields' => 'name,url',
            'lists' => 'open',
            'list_fields' => 'id,name,idBoard',
        ])
            ->map(fn($board) => TrelloBoard::fromRequest($board))
            ->toArray();
    }

    /**
     * @return \App\Entities\TrelloBoard
     * @throws \Illuminate\Http\Client\RequestException
     */
    public function findById($boardId): TrelloBoard
    {
        return $this->get("boards/{$boardId}", [
            'fields' => 'name,url',
            'lists' => 'open',
            'list_fields' => 'id,name,idBoard',
        ])
            ->pipe(function ($board) {
                return TrelloBoard::fromRequest($board);
            });
    }
}
