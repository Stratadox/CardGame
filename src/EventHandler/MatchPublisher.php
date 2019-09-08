<?php declare(strict_types=1);

namespace Stratadox\CardGame\EventHandler;

use function assert;
use Stratadox\CardGame\DomainEvent;
use Stratadox\CardGame\Match\Event\MatchHasBegun;
use Stratadox\CardGame\Match\Event\StartedSettingUpMatchForProposal;
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
        if ($event instanceof StartedSettingUpMatchForProposal) {
            $this->setupMatch($event);
        } else {
            assert($event instanceof MatchHasBegun);
            $this->startMatch($event);
        }
    }

    private function setupMatch(StartedSettingUpMatchForProposal $event): void
    {
        $this->proposalFor[(string) $event->aggregateId()] = $event->proposalId();
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