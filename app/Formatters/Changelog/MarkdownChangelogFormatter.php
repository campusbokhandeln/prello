<?php

namespace App\Formatters\Changelog;
use App\Entities\TrelloCard;
use App\Formatters\Changelog\ChangelogFormatter;
use LaravelZero\Framework\Commands\Command;
use function collect;

class MarkdownChangelogFormatter implements ChangelogFormatter
{

    public function __construct(protected Command $command)
    {
    }


    /**
     * @param string $title
     * @param TrelloCard[] $cards
     */
    public function format(string $title, array $cards, $links = false)
    {
        $this->command->line("# " . $title);
        $this->command->newLine();

        collect($cards)
            ->map(fn(TrelloCard $card) => "- " . $card->name)
            ->each(fn(string $cardName) => $this->command->line($cardName));

    }
}
