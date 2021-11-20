<?php

namespace App\Commands;


use App\Actions\GetBranchNameFromTrelloBoardAction as GetBranchName;
use App\Actions\GitCheckoutBranchAction as CheckoutBranch;
use App\Services\Trello\TrelloApiGateway;
use App\Support\TrelloSelection;
use App\Traits\HasTrelloMenus;
use App\Traits\InteractsWithGitRepo;
use LaravelZero\Framework\Commands\Command;
use Symfony\Component\Process\Exception\ProcessFailedException;

class CheckoutCommand extends Command
{
    use HasTrelloMenus;
    use InteractsWithGitRepo;

    /**
     * The signature of the command.
     *
     * @var string
     */
    protected $signature = 'checkout {--card=}';

    /**
     * The description of the command.
     *
     * @var string
     */
    protected $description = 'Checkout a Trello Card.';

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle(CheckoutBranch $checkoutBranch, GetBranchName $getBranchName)
    {
        $this->ensureFolderHasGitRepo();

        if(! $this->ensureCurrentBranchIsCorrect()) {
            $this->info('exiting..');
            return;
        }

        $result = $this->getTrelloSelection();
        $this->saveTrelloSelectionFor('pr', $result);

        $branch = $getBranchName->execute($result->trelloBoard, $result->trelloCard);

        if(! $this->confirm(sprintf("Checkout %s", $branch))) {
            $this->info('exiting..');
            return;
        }

        try {
            $this->task(
                "Checkout branch {$branch}",
                fn() => $checkoutBranch->execute($branch)
            );

            $this->alert('Happy coding!');
        } catch (ProcessFailedException $e) {
            $this->error($e->getMessage());
        }
    }

    public function getTrelloSelection(): TrelloSelection
    {
        // Create PR
        if ($cardId = $this->option('card')) {
            try {
                $trello = app(TrelloApiGateway::class);
                $card = $trello->card()->findById($cardId);
                $list = $trello->list()->findById($card->idList);
                $board = $trello->board()->findById($list->idBoard);

                return new TrelloSelection($board, $list, $card);

            } catch (\Throwable $e) {
                $this->error('Card not found');
                exit();
            }
        }

        return $this->getCard(config('prello.settings.pr.lastBoardId'), config('prello.settings.pr.lastListId'));
    }
}
