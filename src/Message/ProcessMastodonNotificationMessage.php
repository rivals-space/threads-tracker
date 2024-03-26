<?php

declare(strict_types=1);

namespace App\Message;

use App\MastodonApi\Dto\Notification;

final readonly class ProcessMastodonNotificationMessage
{
    public function __construct(
        public Notification $notification,
    ) {
    }
}
