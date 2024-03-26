<?php

declare(strict_types=1);

namespace App\Message;

final readonly class CheckThreadUserMessage
{
    public function __construct(
        public string $username,
    ) {
    }
}
