<?php

namespace App\Services\Trello;

use App\Services\Trello\TrelloApiGateway as Client;
use Illuminate\Support\Collection;

class AbstractTrello
{
    public function __construct(protected Client $client)
    {
    }

    protected function get($path, array $params = []): Collection
    {
        return Collection::wrap(
            $this->client
                ->getClient()
                ->get($path, $params)
                ->throw()
                ->json()
        );
    }

    protected function post($path, array $data): Collection
    {
        return Collection::wrap(
            $this->client
                ->getClient()
                ->post($path, $data)
                ->throw()
                ->json()
        );
    }

}
