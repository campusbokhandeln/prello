<?php

namespace App\Actions\Git;

use Illuminate\Support\Str;
use Symfony\Component\Process\Exception\ProcessFailedException;
use Symfony\Component\Process\Process;

class GitRepoHasNonStagedChangesAction
{
    public static function execute(): string
    {
        throw_unless(
            CheckGitRepoExistsAction::execute(),
            new \Exception('Git repo does not exist')
        );

        $output = tap(
            (new Process(['git', 'status', '--porcelain'])),
            function (Process $process) {
                $process->run();

                if (!$process->isSuccessful()) {
                    throw new ProcessFailedException($process);
                }
            }
        )->getOutput();

        return static::hasNonStagedChanges($output);
    }

    public static function hasNonStagedChanges($output): bool
    {
        return Str::of($output)
            ->trim()
            ->explode(PHP_EOL)
            ->filter()
            ->map(fn($line) => Str::of($line))
            ->reject(fn ($line) => $line->startsWith('??'))
            ->reject(fn ($line) => $line->substr(1, 1) == ' ')
            ->isNotEmpty();
    }
}
