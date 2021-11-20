<?php

namespace App\Actions\Git;

use Symfony\Component\Process\Exception\ProcessFailedException;
use Symfony\Component\Process\Process;

class GitPushAction
{
    public function execute(string $branchName)
    {
        $process = new Process(['git', 'push', '--set-upstream', 'origin', $branchName]);
        $result = $process->run();

        // executes after the command finishes
        if (!$process->isSuccessful()) {
            throw new ProcessFailedException($process);
        }
//            $this->newLine();
//            $this->line($process->getOutput());
        return $result;

    }
}
