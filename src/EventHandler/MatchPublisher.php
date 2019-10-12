<?php declare(strict_types=1);

namespace Stratadox\CardGame\EventHandler;

use function assert;
use Stratadox\CardGame\DomainEvent;
use Stratadox\CardGame\Match\Event\MatchHasBegun;
use Stratadox\CardGame\Match\Event\StartedMatchForProposal;
use Stratadox\CardGame\ReadModel\Match\OngoingMatch;
use Stratadox\CardGame\ReadModel\Match\OngoingMatches;

final class MatchPublisher implements EventHandler
{
    private $proposalFor = [];
    private $playersFor = [];
    private $matches;

    public function __construct(OngoingMatches $matches)
    {
        $this->matches = $matches;
    }

    public function handle(DomainEvent $event): void
    {
        if ($event instanceof StartedMatchForProposal) {
            $this->setupMatch($event);
        } else {
            assert($event instanceof MatchHasBegun);
            $this->startMatch($event);
        }
    }

    private function setupMatch(StartedMatchForProposal $event): void
    {
        $this->proposalFor[(string) $event->aggregateId()] = $event->proposal();
        $this->playersFor[(string) $event->aggregateId()] = $event->players();
    }

    private function startMatch(MatchHasBegun $event): void
    {
        $this->matches->addFromProposal(
            $this->proposalFor[(string) $event->aggregateId()],
            new OngoingMatch(
                $event->aggregateId(),
                $event->whoBegins(),
                ...$this->playersFor[(string) $event->aggregateId()]
            )
        );
    }
}
