<?php declare(strict_types=1);

namespace Stratadox\CardGame\Match\Event;

use Stratadox\CardGame\Deck\CardId;
use Stratadox\CardGame\Match\MatchEvent;
use Stratadox\CardGame\Match\MatchId;

final class UnitRegrouped implements MatchEvent
{
    /** @var MatchId */
    private $match;
    /** @var CardId */
    private $card;
    /** @var int */
    private $player;

    public function __construct(MatchId $match, CardId $card, int $player)
    {
        $this->match = $match;
        $this->card = $card;
        $this->player = $player;
    }

    public function aggregateId(): MatchId
    {
        return $this->match;
    }

    public function card(): CardId
    {
        return $this->card;
    }

    public function player(): int
    {
        return $this->player;
    }

    public function match(): MatchId
    {
        return $this->aggregateId();
    }
}
