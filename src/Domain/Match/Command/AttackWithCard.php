<?php declare(strict_types=1);

namespace Stratadox\CardGame\Match\Command;

use Stratadox\CardGame\CorrelationId;
use Stratadox\CardGame\Match\MatchId;

final class AttackWithCard
{
    /** @var int */
    private $offset;
    /** @var int */
    private $player;
    /** @var MatchId */
    private $match;
    /** @var CorrelationId */
    private $correlationId;

    private function __construct(
        int $offset,
        int $player,
        MatchId $match,
        CorrelationId $correlationId
    ) {
        $this->offset = $offset;
        $this->player = $player;
        $this->match = $match;
        $this->correlationId = $correlationId;
    }

    public static function number(
        int $offset,
        int $player,
        MatchId $match,
        CorrelationId $correlationId
    ): self {
        return new self($offset, $player, $match, $correlationId);
    }

    public function cardNumber(): int
    {
        return $this->offset;
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
