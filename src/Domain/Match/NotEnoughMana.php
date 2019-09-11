<?php declare(strict_types=1);

namespace Stratadox\CardGame\Match;

use RuntimeException;
use function sprintf;

final class NotEnoughMana extends RuntimeException implements CannotPlayThisCard
{
    public static function toPlay(CardId $card): self
    {
        return new self(sprintf('Not enough mana to play the card `%s`.', $card));
    }
}
