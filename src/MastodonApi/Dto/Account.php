<?php

declare(strict_types=1);

namespace App\MastodonApi\Dto;

use Symfony\Component\Serializer\Attribute\SerializedName;

class Account
{
    public function __construct(
        public string $id,
        public string $username,
        public string $acct,
        #[SerializedName('display_name')]
        public string $displayName,
    ) {
    }
}
