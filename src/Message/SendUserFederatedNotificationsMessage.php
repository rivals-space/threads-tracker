<?php

declare(strict_types=1);

namespace App\Message;

final readonly class SendUserFederatedNotificationsMessage
{
    /*
     * Add whatever properties and methods you need
     * to hold the data for this message class.
     */

    public function __construct(
        public string $threadsUserId,
    ) {
    }
}
