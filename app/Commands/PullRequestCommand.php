<?php

namespace App\Commands;

use App\Actions\GetBranchNameFromTrelloBoardAction as GetBranchName;
use App\Actions\GitCheckoutBranchAction as CheckoutBranch;
use App\Actions\GitPullRequestAction as PullRequest;
use App\DataTransferObjects\PullRequestDto;
use App\Services\Trello\TrelloApiGateway;
use App\Support\TrelloSelection;
use App\Traits\HasTrelloMenus;
use App\Traits\InteractsWithGitRepo;
use LaravelZero\Framework\Commands\Command;
use Symfony\Component\Process\Exception\ProcessFailedException;

class PullRequestCommand extends Command
{
    use HasTrelloMenus;
    use InteractsWithGitRepo;

    /**
     * The signature of the command.
     *
     * @var string
     */
    protected $signature = 'pr {--card=}';

    /**
     * The description of the command.
     *
     * @var string
     */
    protected $description = 'Create a Pull Request.';

    protected ?string $pullRequestUrl;

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle(CheckoutBranch $checkoutBranch, PullRequest $pullRequest, GetBranchName $getBranchName,)
    {
        $this->ensureFolderHasGitRepo();

        $result = $this->getTrelloSelection();
        $this->saveTrelloSelectionFor('pr', $result);

        $branch = $getBranchName->execute($result->trelloBoard, $result->trelloCard);
        $pullRequestDto = new PullRequestDto($result->trelloCard->name, $result->trelloCard->name);

        if (!$this->confirm(sprintf("Checkout and create PR for: %s", $branch))) {
            $this->info('exiting..');
            return;
        }

        try {
            $this->task(
                sprintf("Checkout branch %s", $branch),
                fn() => $checkoutBranch->execute($branch)
            );

            $this->task(
                "Create Pull Request",
                fn() => $this->pullRequestUrl = $pullRequest->execute($pullRequestDto, $result->trelloCard)
            );

            $this->info(sprintf("PullRequest url: %s", $this->pullRequestUrl));
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
