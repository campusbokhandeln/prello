<?php

namespace App\Commands;

use App\Actions\CreateTrelloCardAction as CreateTrelloCard;
use App\DataTransferObjects\NewTrelloCardDto;
use App\Entities\TrelloCard;
use App\Services\Settings\Settings;
use App\Traits\HasTrelloMenus;
use LaravelZero\Framework\Commands\Command;

class TodoCommand extends Command
{
    use HasTrelloMenus;

    /**
     * The signature of the command.
     *
     * @var string
     */
    protected $signature = 'todo {title?} {--title=} {--desc=}';

    /**
     * The description of the command.
     *
     * @var string
     */
    protected $description = 'Quick Trello Note';

    protected ?TrelloCard $createdCard;

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle(CreateTrelloCard $createTrelloCard)
    {
        $card = $this->askUserForNewCard();

        $result = $this->getList(config('prello.settings.quick.lastBoardId'));
        $this->saveTrelloSelectionFor('quick', $result);

        $this->printNewCardInfo($card);

        $this->task(
            sprintf("Adding todo on %s -> %s", $result->trelloBoard->name, $result->trelloList->name),
            fn() => $this->createdCard = $createTrelloCard->execute($result->trelloList, $card)
        );

        $this->printResult();
    }

    protected function askUserForNewCard(): NewTrelloCardDto
    {
        try {
            if($title = $this->argument('title')) {
                return new NewTrelloCardDto($title, '');
            }

            $title = $this->option('title') ?: $this->ask('Card Title');
            $desc = $this->option('desc') ?: $this->ask('Description', '');

            return new NewTrelloCardDto($title, $desc);
        } catch (\Throwable $e) {
            $this->error('Card not OK');
            exit();
        }
    }

    /**
     * @param NewTrelloCardDto $card
     */
    protected function printNewCardInfo(NewTrelloCardDto $card): void
    {
        $this->info('Trello card:');
        $this->line($card->name);
        $this->line($card->desc);
        $this->newLine();
    }

    protected function printResult(): void
    {
        $this->newLine();
        $this->info(sprintf("Link to trello-card: %s", $this->createdCard->url));
        $this->newLine();
        $this->alert('Happy Coding!');
    }
}
