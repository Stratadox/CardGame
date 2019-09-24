<?php declare(strict_types=1);

namespace Stratadox\CardGame\Match\Player;

use RuntimeException;
use function sprintf;

final class NoSuchPlayer extends RuntimeException
{
    public static function weDoNotKnow(PlayerId $id): self
    {
        return new self(sprintf('There is no player with id %s!', $id));
    }
}
