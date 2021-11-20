<?php

namespace App\Actions\Git;

use App\Actions\CreateTrelloCardUrlAttachmentAction;
use App\Actions\Git\GitPullRequestCommentAction;
use App\DataTransferObjects\PullRequestDto;
use App\DataTransferObjects\GitPullRequestCommentDto;
use App\Entities\TrelloCard;
use Symfony\Component\Process\Exception\ProcessFailedException;
use Symfony\Component\Process\Process;
use function tap;

class GitPullRequestAction
{
    public function __construct(
        protected GitPullRequestCommentAction         $createPullRequestComment,
        protected CreateTrelloCardUrlAttachmentAction $createTrelloCardUrlAttachment
    )
    {
    }

    public function execute(PullRequestDto $pullRequestDto, TrelloCard $card): string|null
    {
        $createPullRequest = tap(
            new Process(['gh', 'pr', 'create', "--title={$pullRequestDto->name}", "--body={$pullRequestDto->body}"]),
            function ($createPullRequest) {
                $createPullRequest->run();
                throw_unless($createPullRequest->isSuccessful(), ProcessFailedException::class, $createPullRequest);
            }
        );

        $this->createPullRequestComment->execute(
            new GitPullRequestCommentDto("![](https://github.trello.services/images/mini-trello-icon.png) [{$card->name}]({$card->url})")
        );

        return tap(
            $createPullRequest->getOutput(),
            fn($pullRequestUrl) => $this->createTrelloCardUrlAttachment->execute($card, $pullRequestUrl)
        );
    }
}
