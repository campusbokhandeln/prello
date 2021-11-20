<?php

namespace App\Traits;

use App\Actions\CheckGitRepoExistsAction;
use App\Actions\GitGetCurrentBranchAction;

trait InteractsWithGitRepo
{
    public function ensureFolderHasGitRepo()
    {
        if(! CheckGitRepoExistsAction::execute(getcwd())) {
            $this->error('No git repo found for current folder');
            exit();
        }
    }

    public function ensureCurrentBranchIsCorrect()
    {
        $currentBranch = GitGetCurrentBranchAction::execute();

        if($currentBranch == 'main') {
            return true;
        }

        return $this->confirm(sprintf("Sure you want to proceed? Current branch is not main, it's %s", $currentBranch));
    }
}
