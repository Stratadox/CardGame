<?php declare(strict_types=1);

namespace Stratadox\CardGame\Match;

use function assert;

final class Location
{
    private const IN_DECK = 0;
    private const IN_HAND = 1;
    private const IN_PLAY = 2;
    private const IN_ATTACK = 3;
    private const IN_DEFENCE = 3;
    private const IN_VOID = 4;

    private $realm;
    private $position;

    private function __construct(int $realm, ?int $position)
    {
        $this->realm = $realm;
        $this->position = $position;
    }

    public static function inDeck(int $position): Location
    {
        return new self(Location::IN_DECK, $position);
    }

    private static function inHand(int $position): Location
    {
        return new self(Location::IN_HAND, $position);
    }

    public static function inPlay(int $position): Location
    {
        return new self(Location::IN_PLAY, $position);
    }

    public static function attackingAt(int $position): Location
    {
        return new self(Location::IN_ATTACK, $position);
    }

    public static function defendingAgainst(int $position): Location
    {
        return new self(Location::IN_DEFENCE, $position);
    }

    public static function inVoid(): Location
    {
        return new self(Location::IN_VOID, null);
    }

    public function isInHand(): bool
    {
        return $this->realm === Location::IN_HAND;
    }

    public function isInPlay(): bool
    {
        return $this->realm === Location::IN_PLAY
            || $this->realm === Location::IN_ATTACK;
//            || $this->realm === Location::IN_DEFENCE;
    }

    public function isAttacking(): bool
    {
        return $this->realm === Location::IN_ATTACK;
    }

    public function isDefending(): bool
    {
        return $this->realm === Location::IN_DEFENCE;
    }

    public function isAttackingThe(Location $defenderLocation): bool
    {
        return $defenderLocation->isDefending();
        // @todo && $this->isAttacking()
        // @todo && $this->position === $defenderLocation->position;
    }

    public function isInDeck(): bool
    {
        return $this->realm === Location::IN_DECK;
    }

    public function hasHigherPositionThan(Location $other): bool
    {
        assert($this->realm === $other->realm);
        return $this->position > $other->position;
    }

    public function toHand(int $position): Location
    {
        return Location::inHand($position);
    }
}
