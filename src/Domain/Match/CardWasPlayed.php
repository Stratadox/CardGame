<?php declare(strict_types=1);

namespace Stratadox\CardGame\Match;

final class CardWasPlayed implements MatchEvent
{
    private $match;
    private $player;
    private $card;
    private $type;

    public function __construct(
        MatchId $match,
        PlayerId $player,
        CardId $card,
        CardType $type
    ) {
        $this->match = $match;
        $this->player = $player;
        $this->card = $card;
        $this->type = (string) $type;
    }

    public function aggregateId(): MatchId
    {
        return $this->match;
    }

    public function player(): PlayerId
    {
        return $this->player;
    }

    public function card(): CardId
    {
        return $this->card;
    }

    public function type(): string
    {
        return $this->type;
    }

    public function payload(): array
    {
        return [/*@todo*/];
    }
}
