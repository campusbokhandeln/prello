<?php

namespace App\Traits;

use App\Actions\Git\CheckGitRepoExistsAction;
use App\Actions\Git\GitGetCurrentBranchAction;
use App\Actions\Git\GitRepoHasNonStagedChangesAction;

trait InteractsWithGitRepo
{
    public function ensureFolderHasGitRepo()
    {
        if(! CheckGitRepoExistsAction::execute(getcwd())) {
            $this->error('No git repo found for current folder');
            exit();
        }
    }

    public function ensureCurrentBranchIsCorrect(): bool
    {
        $currentBranch = GitGetCurrentBranchAction::execute();

        if($currentBranch == 'main') {
            return true;
        }

        return $this->confirm(sprintf("Sure you want to proceed? Current branch is not main, it's %s", $currentBranch));
    }

    public function ensureRepoIsClean(): bool
    {
        return ! GitRepoHasNonStagedChangesAction::execute();
    }
}
