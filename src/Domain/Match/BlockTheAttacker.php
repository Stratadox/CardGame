<?php declare(strict_types=1);

namespace Stratadox\CardGame\Match;

final class BlockTheAttacker
{
    /** @var int */
    private $defender;
    /** @var int */
    private $attacker;
    /** @var PlayerId */
    private $player;

    public function __construct(int $defender, int $attacker, PlayerId $player)
    {
        $this->defender = $defender;
        $this->attacker = $attacker;
        $this->player = $player;
    }

    public static function number(
        int $attacker,
        int $defender,
        PlayerId $player
    ): self {
        return new self($defender, $attacker, $player);
    }

    public function defender(): int
    {
        return $this->defender;
    }

    public function attacker(): int
    {
        return $this->attacker;
    }

    public function player(): PlayerId
    {
        return $this->player;
    }
}
