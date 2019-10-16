<?php declare(strict_types=1);

namespace Stratadox\CardGame\Match\Event;

use Stratadox\CardGame\Deck\CardId;
use Stratadox\CardGame\Match\MatchEvent;
use Stratadox\CardGame\Match\MatchId;

final class UnitDied implements MatchEvent
{
    /** @var MatchId */
    private $match;
    /** @var CardId */
    private $card;

    public function __construct(MatchId $match, CardId $card)
    {
        $this->match = $match;
        $this->card = $card;
    }

    public function aggregateId(): MatchId
    {
        return $this->match;
    }

    public function match(): MatchId
    {
        return $this->aggregateId();
    }

    public function card(): CardId
    {
        return $this->card;
    }
}