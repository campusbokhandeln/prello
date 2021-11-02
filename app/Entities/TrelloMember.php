<?php

namespace App\Entities;

class TrelloMember
{

    public function __construct(
        public string $id,
        public ?string $avatarUrl,
        public ?string $fullName,
        public ?string $initials,
        public ?string $username,
    )
    {
    }

    public static function fromRequest($member)
    {
        return new self(
            id: $member['id'],
            avatarUrl: $member['avatarUrl'],
            fullName: $member['fullName'],
            initials: $member['initials'],
            username: $member['username'],
        );
    }
}
