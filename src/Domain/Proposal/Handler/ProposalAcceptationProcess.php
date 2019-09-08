<?php declare(strict_types=1);

namespace Stratadox\CardGame\Proposal\Handler;

use function assert;
use Stratadox\CardGame\EventBag;
use Stratadox\CardGame\Proposal\Command\AcceptTheProposal;
use Stratadox\CardGame\Proposal\ProposedMatches;
use Stratadox\Clock\Clock;
use Stratadox\CommandHandling\Handler;

final class ProposalAcceptationProcess implements Handler
{
    private $clock;
    private $proposals;
    private $eventBag;

    public function __construct(Clock $clock, ProposedMatches $proposals, EventBag $eventBag)
    {
        $this->clock = $clock;
        $this->proposals = $proposals;
        $this->eventBag = $eventBag;
    }

    public function handle(object $command): void
    {
        assert($command instanceof AcceptTheProposal);

        $proposal = $this->proposals->withId($command->proposalId());
        if (!$proposal) {
            // @todo new TriedToAcceptProposalWithInvalidId($command->proposalId())
            return;
        }

        $proposal->accept($this->clock->now());

        $this->eventBag->takeFrom($proposal);
    }
}
