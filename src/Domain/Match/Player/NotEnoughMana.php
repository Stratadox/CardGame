<?php declare(strict_types=1);

namespace Stratadox\CardGame\Match\Player;

use RuntimeException;
use function sprintf;
use Stratadox\CardGame\Match\Card\CardId;

final class NotEnoughMana extends RuntimeException implements CannotPlayThisCard
{
    public static function toPlayTheCard(): self
    {
        return new self(sprintf('Not enough mana.'));
    }
}
