<?php declare(strict_types=1);

namespace Stratadox\CardGame\Match\Command;

use Stratadox\CardGame\Command;
use Stratadox\CardGame\CorrelationId;
use Stratadox\CardGame\Match\MatchId;

final class EndTheTurn implements Command
{
    /** @var MatchId */
    private $match;
    /** @var int */
    private $player;
    /** @var CorrelationId */
    private $correlationId;

    private function __construct(
        MatchId $match,
        int $player,
        CorrelationId $correlationId
    ) {
        $this->match = $match;
        $this->player = $player;
        $this->correlationId = $correlationId;
    }


    public static function for(
        MatchId $match,
        int $player,
        CorrelationId $correlationId
    ): self {
        return new self($match, $player, $correlationId);
    }

    public function match(): MatchId
    {
        return $this->match;
    }

    public function player(): int
    {
        return $this->player;
    }

    public function correlationId(): CorrelationId
    {
        return $this->correlationId;
    }
}
