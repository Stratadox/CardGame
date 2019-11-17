<?php declare(strict_types=1);

namespace Stratadox\CardGame\Match\Event;

use Stratadox\CardGame\Match\MatchEvent;
use Stratadox\CardGame\Match\MatchId;

final class SpellVanishedToTheVoid implements MatchEvent
{
    /** @var MatchId */
    private $match;
    /** @var int */
    private $player;
    /** @var int */
    private $offset;

    public function __construct(
        MatchId $match,
        int $player,
        int $offset
    ) {
        $this->match = $match;
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

    public function offset(): int
    {
        return $this->offset;
    }
}
