<?php declare(strict_types=1);

namespace Stratadox\CardGame\Match\Match;

use function assert;
use Stratadox\CardGame\DomainEventRecorder;
use Stratadox\CardGame\DomainEventRecording;
use Stratadox\CardGame\Match\Player\PlayerId;

final class Match implements DomainEventRecorder
{
    use DomainEventRecording;

    private $id;
    private $turn;
    private $players;

    public function __construct(
        MatchId $id,
        Turn $turn,
        array $events,
        PlayerId ...$players
    ) {
        $this->id = $id;
        $this->turn = $turn;
        $this->players = $players;
        $this->events = $events;
    }

    public static function prepare(
        MatchId $id,
        array $events,
        PlayerId ...$players
    ): self {
        return new self($id, Turn::preparation(), $events, ...$players);
    }

    public function id(): MatchId
    {
        return $this->id;
    }

    /** @return PlayerId[] */
    public function players(): array
    {
        return $this->players;
    }

    public function isBeingPlayedBy(PlayerId $thatPlayer): bool
    {
        foreach ($this->players as $thisPlayer) {
            if ($thatPlayer->is($thisPlayer)) {
                return true;
            }
        }
        return false;
    }

    public function begin(PlayerId $whoBegins): void
    {
        assert($this->isBeingPlayedBy($whoBegins));
        assert($this->turn->hasNotStartedYet());

        $this->turn = $this->turn->beginTurnOf($whoBegins);

        $this->events[] = new MatchHasBegun($this->id(), $whoBegins);
    }
}
