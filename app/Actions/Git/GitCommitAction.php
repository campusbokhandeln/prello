<?php

namespace App\Actions\Git;

use Symfony\Component\Process\Exception\ProcessFailedException;
use Symfony\Component\Process\Process;

class GitCommitAction
{
    public function execute(string $commitMessage = 'wip')
    {
        $process = new Process(['git', 'commit', '-m', $commitMessage, '--allow-empty']);
        $result = $process->run();

        // executes after the command finishes
        if (!$process->isSuccessful()) {
            throw new ProcessFailedException($process);
        }

        return $result;
    }
}
