<?php declare(strict_types=1);

namespace Stratadox\CardGame\Match\Command;

use Stratadox\CardGame\Command;
use Stratadox\CardGame\CorrelationId;
use Stratadox\CardGame\Match\MatchId;

final class EndCardPlaying implements Command
{
    /** @var int */
    private $player;
    /** @var MatchId */
    private $match;
    /** @var CorrelationId */
    private $correlationId;

    private function __construct(
        int $player,
        MatchId $match,
        CorrelationId $correlationId
    ) {
        $this->player = $player;
        $this->match = $match;
    }

    public static function phase(
        int $player,
        MatchId $match,
        CorrelationId $correlationId
    ): self {
        return new self($player, $match, $correlationId);
    }

    public function player(): int
    {
        return $this->player;
    }

    public function match(): MatchId
    {
        return $this->match;
    }

    public function correlationId(): CorrelationId
    {
        return $this->correlationId;
    }
}
