<?php declare(strict_types=1);

namespace Stratadox\CardGame\Match\Command;

use Stratadox\CardGame\CorrelationId;
use Stratadox\CardGame\Match\MatchId;

final class BlockTheAttacker
{
    /** @var int */
    private $defender;
    /** @var int */
    private $attacker;
    /** @var int */
    private $player;
    /** @var MatchId */
    private $match;
    /** @var CorrelationId */
    private $correlationId;

    private function __construct(
        int $defender,
        int $attacker,
        int $player,
        MatchId $match,
        CorrelationId $correlationId
    ) {
        $this->defender = $defender;
        $this->attacker = $attacker;
        $this->player = $player;
        $this->match = $match;
        $this->correlationId = $correlationId;
    }


    public static function number(
        int $attacker,
        int $defender,
        int $player,
        MatchId $match,
        CorrelationId $correlationId
    ): self {
        return new self($defender, $attacker, $player, $match, $correlationId);
    }

    public function defender(): int
    {
        return $this->defender;
    }

    public function attacker(): int
    {
        return $this->attacker;
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
