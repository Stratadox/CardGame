<?php declare(strict_types=1);

namespace Stratadox\CardGame\RestInterface\Access;

use RuntimeException;

final class NobodyToAnnounce extends RuntimeException
{
    public static function atThisPoint(): self
    {
        return new self('There is no authenticated user to announce.');
    }
}
