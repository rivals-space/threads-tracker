<?php

declare(strict_types=1);

namespace App\MastodonApi\Dto;

use DateTimeInterface;
use Symfony\Component\Serializer\Attribute\SerializedName;

class Status
{
    public function __construct(
        public string $id,
        #[SerializedName('created_at')]
        public DateTimeInterface $createdAt,
        public string $content,
    ) {
    }
}
