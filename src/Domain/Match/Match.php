<?php declare(strict_types=1);

namespace Stratadox\CardGame\Match;

use Stratadox\CardGame\DomainEventRecorder;
use Stratadox\CardGame\DomainEventRecording;
use Stratadox\CardGame\Match\Event\MatchHasBegun;
use Stratadox\CardGame\MatchId;
use Stratadox\CardGame\PlayerId;

final class Match implements DomainEventRecorder
{
    use DomainEventRecording;

    private $id;
    private $turnOf;
    private $players;

    public function __construct(
        MatchId $id,
        PlayerId $whoBegins,
        array $events,
        Player ...$players
    ) {
        $this->id = $id;
        $this->turnOf = $whoBegins;
        $this->players = $players;
        $this->events = $events;
        $this->events[] = new MatchHasBegun($id, $whoBegins);
    }

    public static function fromSetup(
        MatchId $id,
        PlayerId $whoBegins,
        array $events,
        Player ...$players
    ): self {
        return new self($id, $whoBegins, $events, ...$players);
    }
}
