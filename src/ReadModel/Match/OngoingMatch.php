<?php declare(strict_types=1);

namespace Stratadox\CardGame\ReadModel\Match;

use Stratadox\CardGame\Match\MatchId;

final class OngoingMatch
{
    public const PHASE_DEFEND = 'defend';
    public const PHASE_PLAY = 'play';
    public const PHASE_ATTACK = 'attack';

    private $id;
    private $turn;
    private $phase = self::PHASE_PLAY;

    public function __construct(MatchId $match, int $whoStarts)
    {
        $this->id = $match;
        $this->turn = $whoStarts;
    }

    public function id(): MatchId
    {
        return $this->id;
    }

    /** @return int[] */
    public function players(): array
    {
        return [0, 1];
    }

    public function startTurnOf(int $player): void
    {
        $this->turn = $player;
        $this->phase = self::PHASE_DEFEND;
    }

    public function itIsTheTurnOf(int $player): bool
    {
        return $this->turn === $player;
    }

    public function startDefendPhase(): void
    {
        $this->phase = self::PHASE_DEFEND;
    }

    public function startPlayPhase(): void
    {
        $this->phase = self::PHASE_PLAY;
    }

    public function startAttackPhase(): void
    {
        $this->phase = self::PHASE_ATTACK;
    }

    public function phase(): string
    {
        return $this->phase;
    }
}
