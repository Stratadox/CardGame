<?php declare(strict_types=1);

namespace Stratadox\CardGame\Match\Command;

use function assert;
use Stratadox\CardGame\CorrelationId;
use Stratadox\CardGame\Match\MatchId;

final class Block
{
    /** @var int|null */
    private $attacker;
    /** @var int|null */
    private $defender;
    /** @var int|null */
    private $player;
    /** @var MatchId|null */
    private $match;
    /** @var CorrelationId|null */
    private $correlationId;

    private function __construct(
        ?int $attacker,
        ?int $defender,
        ?int $player,
        ?MatchId $match,
        ?CorrelationId $correlationId
    ) {
        $this->attacker = $attacker;
        $this->defender = $defender;
        $this->player = $player;
        $this->match = $match;
        $this->correlationId = $correlationId;
    }

    public static function theAttack(): self
    {
        return new self(null, null, null, null, null);
    }

    public function ofAttacker(int $attacker): self
    {
        $clone = clone $this;
        $clone->attacker = $attacker;
        return $clone;
    }

    public function withDefender(int $defender): self
    {
        $clone = clone $this;
        $clone->defender = $defender;
        return $clone;
    }

    public function as(int $player): self
    {
        $clone = clone $this;
        $clone->player = $player;
        return $clone;
    }

    public function in(MatchId $match): self
    {
        $clone = clone $this;
        $clone->match = $match;
        return $clone;
    }

    public function trackedWith(CorrelationId $correlationId): self
    {
        $clone = clone $this;
        $clone->correlationId = $correlationId;
        return $clone;
    }

    public function go(): BlockTheAttacker
    {
        assert($this->attacker !== null);
        assert($this->defender !== null);
        assert($this->player !== null);
        assert($this->match !== null);
        assert($this->correlationId !== null);
        return BlockTheAttacker::number(
            $this->attacker,
            $this->defender,
            $this->player,
            $this->match,
            $this->correlationId
        );
    }
}
