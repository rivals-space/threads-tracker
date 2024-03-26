<?php

declare(strict_types=1);

namespace App\Enum;

enum Operation: string
{
    case WATCH = 'watch';
    case UNWATCH = 'unwatch';
    case LIST = 'list';
    case UNWATCH_ALL = 'unwatch-all';
}
