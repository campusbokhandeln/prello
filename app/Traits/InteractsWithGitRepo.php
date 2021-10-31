<?php

namespace App\Traits;

use App\Actions\CheckGitRepoExistsAction;

trait InteractsWithGitRepo
{
    public function ensureFolderHasGitRepo()
    {
        if(! CheckGitRepoExistsAction::execute(getcwd())) {
            $this->error('No git repo found for current folder');
            exit();
        }
    }
}
