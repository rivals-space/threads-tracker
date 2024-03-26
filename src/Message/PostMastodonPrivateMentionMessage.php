<?php

declare(strict_types=1);

namespace App\Message;

final readonly class PostMastodonPrivateMentionMessage
{
    public function __construct(
        public string $acct,
        public string $message,
    ) {
    }
}
