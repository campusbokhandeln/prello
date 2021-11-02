<?php

namespace App\Formatters\Changelog;
use App\Entities\TrelloCard;
use App\Formatters\Changelog\ChangelogFormatter;
use LaravelZero\Framework\Commands\Command;
use Symfony\Component\Console\Helper\TableStyle;
use function collect;

class ConsoleChangelogFormatter implements ChangelogFormatter
{

    public function __construct(protected Command $command)
    {
    }


    /**
     * @param string $title
     * @param \App\Entities\TrelloCard[] $cards
     */
    public function format(string $title, array $cards, $links = false)
    {
        $cardRows = collect($cards)
            ->map(fn(TrelloCard $card) => [
                "- {$card->getName()}",
            ]);

        $this->command->title($title);
        $this->command->table(['Uppgift'], $cardRows);
    }
}
