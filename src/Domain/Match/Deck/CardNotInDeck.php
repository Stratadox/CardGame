<?php declare(strict_types=1);

namespace Stratadox\CardGame\Match\Deck;

use RuntimeException;
use function sprintf;

final class CardNotInDeck extends RuntimeException
{
    public static function deckHasNoCardAt(int $cardNumber): self
    {
        return new self(sprintf('The deck has no card at offset %d', $cardNumber));
    }
}
