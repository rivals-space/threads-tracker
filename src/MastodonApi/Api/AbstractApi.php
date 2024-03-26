<?php

declare(strict_types=1);

namespace App\MastodonApi\Api;

use Symfony\Component\Serializer\SerializerInterface;

abstract class AbstractApi
{
    public function __construct(
        protected ApiHttpClient $client,
        protected SerializerInterface $serializer,
    ) {
    }
}
