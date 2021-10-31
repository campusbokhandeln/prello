<?php

namespace App\Formatters\Changelog;

use App\Entities\TrelloCard;
use LaravelZero\Framework\Commands\Command;

interface ChangelogFormatter
{
    public function __construct(Command $command);

    /**
     * @param string $title
     * @param TrelloCard[] $cards
     */
    public function format(string $title, array $cards);
}
