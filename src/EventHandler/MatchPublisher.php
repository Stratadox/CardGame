<?php declare(strict_types=1);

namespace Stratadox\CardGame\EventHandler;

use Stratadox\CardGame\Match\MatchId;
use Stratadox\CardGame\ReadModel\Proposal\MatchProposal;
use Stratadox\CardGame\ReadModel\Proposal\MatchProposals;
use function assert;
use Stratadox\CardGame\DomainEvent;
use Stratadox\CardGame\Match\Event\MatchStarted;
use Stratadox\CardGame\Match\Event\StartedMatchForProposal;
use Stratadox\CardGame\ReadModel\Match\OngoingMatch;
use Stratadox\CardGame\ReadModel\Match\OngoingMatches;

final class MatchPublisher implements EventHandler
{
    /** @var MatchProposal[] */
    private $proposalFor = [];
    /** @var OngoingMatches */
    private $matches;
    /** @var MatchProposals */
    private $proposals;

    public function __construct(OngoingMatches $matches, MatchProposals $proposals)
    {
        $this->matches = $matches;
        $this->proposals = $proposals;
    }

    public function events(): iterable
    {
        return [
            StartedMatchForProposal::class,
            MatchStarted::class,
        ];
    }

    public function handle(DomainEvent $event): void
    {
        if ($event instanceof StartedMatchForProposal) {
            $this->setupMatch($event);
        } else {
            assert($event instanceof MatchStarted);
            $this->startMatch($event->aggregateId(), $event->whoBegins());
        }
    }

    private function setupMatch(StartedMatchForProposal $event): void
    {
        $this->proposalFor[(string) $event->aggregateId()] = $this->proposals->byId($event->proposal());
    }

    private function startMatch(MatchId $match, int $whoBegins): void
    {
        $this->proposalFor[$match->id()]->begin($match);
        $this->matches->addFromProposal(
            $this->proposalFor[$match->id()]->id(),
            new OngoingMatch($match, $whoBegins)
        );
    }
}
