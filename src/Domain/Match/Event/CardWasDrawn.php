<?php declare(strict_types=1);

namespace Stratadox\CardGame\Match\Event;

use Stratadox\CardGame\Deck\CardId;
use Stratadox\CardGame\Match\MatchEvent;
use Stratadox\CardGame\Match\MatchId;

final class CardWasDrawn implements MatchEvent
{
    private $match;
    private $player;
    private $card;
    private $offset;

    public function __construct(
        MatchId $match,
        CardId $card,
        int $player,
        int $offset
    ) {
        $this->match = $match;
        $this->card = $card;
        $this->player = $player;
        $this->offset = $offset;
    }

    public function aggregateId(): MatchId
    {
        return $this->match;
    }

    public function match(): MatchId
    {
        return $this->aggregateId();
    }

    public function player(): int
    {
        return $this->player;
    }

    public function card(): CardId
    {
        return $this->card;
    }

    public function offset(): int
    {
        return $this->offset;
    }
}
