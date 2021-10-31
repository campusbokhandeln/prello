<?php

namespace App\Commands;

use App\Actions\GetTrelloCardsAction as GetTrelloCards;
use App\Formatters\Changelog\ConsoleChangelogFormatter;
use App\Formatters\Changelog\ChangelogFormatter;
use App\Formatters\Changelog\MarkdownChangelogFormatter;
use App\Traits\HasTrelloMenus;
use LaravelZero\Framework\Commands\Command;

class ChangelogCommand extends Command
{
    use HasTrelloMenus;

    /**
     * The signature of the command.
     *
     * @var string
     */
    protected $signature = 'changelog {--M|--markdown} {--L|--links}';

    /**
     * The description of the command.
     *
     * @var string
     */
    protected $description = 'Get changelog for Board/List';

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle(GetTrelloCards $getTrelloCards)
    {
        $result = $this->getList(config('prello.settings.changelog.lastBoardId'));
        $this->saveTrelloSelectionFor('changelog', $result);

        $this->getFormatter()
            ->format(
                sprintf("Releaselog - %s", $result->trelloList->name),
                $getTrelloCards->execute($result->trelloList->id),
            );
    }

    public function getFormatter(): ChangelogFormatter
    {
        $format = $this->option('markdown') ? 'markdown' : 'console';

        return match ($format) {
            'markdown' => new MarkdownChangelogFormatter($this),
            default => new ConsoleChangelogFormatter($this),
        };
    }
}
