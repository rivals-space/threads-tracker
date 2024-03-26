<?php

declare(strict_types=1);

namespace App\MastodonApi\Dto;

use DateTimeInterface;
use Symfony\Component\Serializer\Annotation\SerializedName;

class Notification
{
    public function __construct(
        public string $id,
        public string $type,
        #[SerializedName('created_at')]
        public DateTimeInterface $createdAt,
        public Account $account,
        public Status $status,
    ) {
    }
}
