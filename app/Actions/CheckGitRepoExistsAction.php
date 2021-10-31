<?php

namespace App\Actions;

use Illuminate\Support\Facades\File;

class CheckGitRepoExistsAction
{
    public static function execute($directory = null)
    {
        $directory ??= getcwd();
        return File::isDirectory(sprintf("%s/.git", $directory));
    }
}
