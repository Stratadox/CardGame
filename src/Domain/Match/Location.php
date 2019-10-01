<?php declare(strict_types=1);

namespace Stratadox\CardGame\Match;

use function assert;

final class Location
{
    private const IN_DECK = 0;
    private const IN_HAND = 1;
    private const IN_PLAY = 2;
    private const IN_VOID = 3;

    private $realm;
    private $position;

    private function __construct(int $realm, int $position)
    {
        $this->realm = $realm;
        $this->position = $position;
    }

    public static function inDeck(int $position): self
    {
        return new self(self::IN_DECK, $position);
    }

    public static function inHand(int $position): self
    {
        return new self(self::IN_HAND, $position);
    }

    public static function inPlay(int $position): self
    {
        return new self(self::IN_PLAY, $position);
    }

    public static function inVoid(): self
    {
        return new self(self::IN_VOID, 0);
    }

    public function isInHand(): bool
    {
        return $this->realm === self::IN_HAND;
    }

    public function isInPlay(): bool
    {
        return $this->realm === self::IN_PLAY;
    }

    public function isInDeck(): bool
    {
        return $this->realm === self::IN_DECK;
    }

    public function hasHigherPositionThan(Location $other): bool
    {
        assert($this->realm === $other->realm);
        return $this->position > $other->position;
    }

    public function toHand(int $position): self
    {
        return self::inHand($position);
    }
}
