<?php

namespace App\Commands;


use App\Actions\GetBranchNameFromTrelloBoardAction as GetBranchName;
use App\Actions\GitCheckoutBranchAction as CheckoutBranch;
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
    protected $signature = 'checkout';

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

        $result = $this->getCard(config('prello.settings.pr.lastBoardId'), config('prello.settings.pr.lastListId'));
        $this->saveTrelloSelectionFor('pr', $result);

        $branch = $getBranchName->execute($result->trelloBoard, $result->trelloCard);

        try {
            $this->info(sprintf("Checkout %s", $branch));

            $this->task(
                "Checkout branch {$branch}",
                fn() => $checkoutBranch->execute($branch)
            );

            $this->alert('Happy coding!');
        } catch (ProcessFailedException $e) {
            $this->error($e->getMessage());
        }
    }
}
