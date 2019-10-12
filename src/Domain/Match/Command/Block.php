<?php declare(strict_types=1);

namespace Stratadox\CardGame\Match\Command;

use Stratadox\CardGame\Match\Command\BlockTheAttacker;
use Stratadox\CardGame\Match\MatchId;

final class Block
{
    /** @var int */
    private $attacker;
    /** @var int|null */
    private $defender;
    /** @var int|null */
    private $player;
    /** @var MatchId|null */
    private $match;

    public function __construct(
        int $attacker,
        ?int $defender,
        ?int $player,
        ?MatchId $match
    ) {
        $this->attacker = $attacker;
        $this->defender = $defender;
        $this->player = $player;
        $this->match = $match;
    }

    public static function attacker(int $attacker): self
    {
        return new self($attacker, null, null, null);
    }

    public function withDefender(int $defender): self
    {
        return new self($this->attacker, $defender, $this->player, $this->match);
    }

    public function as(int $player): self
    {
        return new self($this->attacker, $this->defender, $player, $this->match);
    }

    public function in(MatchId $match): self
    {
        return new self($this->attacker, $this->defender, $this->player, $match);
    }

    public function go(): BlockTheAttacker
    {
        return BlockTheAttacker::number(
            $this->attacker,
            $this->defender,
            $this->player,
            $this->match
        );
    }
}
