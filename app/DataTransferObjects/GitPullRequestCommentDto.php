<?php

namespace App\DataTransferObjects;

class GitPullRequestCommentDto
{
 public function __construct(public string $body)
 {
 }
}
