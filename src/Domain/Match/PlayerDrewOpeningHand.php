<?php declare(strict_types=1);

namespace Stratadox\CardGame\Match;

final class PlayerDrewOpeningHand implements MatchEvent
{
    private $match;
    private $player;
    private $cards;

    public function __construct(MatchId $match, PlayerId $player, CardId ...$cards)
    {
        $this->match = $match;
        $this->player = $player;
        $this->cards = $cards;
    }

    public function aggregateId(): MatchId
    {
        return $this->match;
    }

    public function player(): PlayerId
    {
        return $this->player;
    }

    /** @return CardId[] */
    public function cards(): array
    {
        return $this->cards;
    }

    public function payload(): array
    {
        return [/*@todo*/];
    }
}
