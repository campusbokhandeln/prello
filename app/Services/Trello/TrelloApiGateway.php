<?php

namespace App\Services\Trello;

use Illuminate\Http\Client\PendingRequest;
use Illuminate\Support\Facades\Http;
use UnhandledMatchError;

/**
 * @method Board boards()
 * @method Board board()
 * @method Card cards()
 * @method Card card()
 * @method CardList lists()
 * @method CardList list()
 */
class TrelloApiGateway
{
    protected PendingRequest $client;

    public function __construct(protected string $key, protected string $token)
    {
        $this->client = Http::baseUrl('https://api.trello.com/1/')
            ->withOptions([
                'query' => [
                    'key' => $this->key,
                    'token' => $this->token,
                ]
            ]);
    }

    public function api($service)
    {
        return match ($service) {
            'board', 'boards' => new Board($this),
            'list', 'lists' => new CardList($this),
            'card', 'cards' => new Card($this),
        };
    }

    /**
     * @param string $service method name
     * @param array $args arguments
     *
     * @return AbstractTrello
     *
     * @throws \Exception
     */
    public function __call($service, $args)
    {
        try {
            return $this->api($service);
        } catch (UnhandledMatchError $e) {
            throw new \Exception(sprintf('Undefined method called: "%s"', $service));
        }
    }

    public function getClient(): PendingRequest
    {
        return $this->client;
    }
}
