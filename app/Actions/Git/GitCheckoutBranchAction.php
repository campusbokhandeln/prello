<?php

namespace App\Actions\Git;

use Symfony\Component\Process\Exception\ProcessFailedException;
use Symfony\Component\Process\Process;

class GitCheckoutBranchAction
{
    public function __construct(
        protected GitCommitAction $gitCommitAction,
        protected GitPushAction   $gitPushAction,
    )
    {
    }

    public function execute(string $branchName)
    {
        $process = new Process(['git', 'checkout', '-b', $branchName]);
        $result = $process->run();

        // executes after the command finishes
        if (!$process->isSuccessful()) {
            throw new ProcessFailedException($process);
        }

        $this->gitCommitAction->execute('Started working');
        $this->gitPushAction->execute($branchName);

        return $result;

    }
}
