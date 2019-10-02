<?php declare(strict_types=1);

namespace Stratadox\CardGame\Match;

use RuntimeException;
use function sprintf;
use Throwable;

final class NoSuchCard extends RuntimeException
{
    public static function atPosition($position, Throwable $exception): self
    {
        return new self(sprintf(
            'There is no card at position #%d: %s',
            $position,
            $exception->getMessage()
        ), $exception->getCode(), $exception);
    }
}
