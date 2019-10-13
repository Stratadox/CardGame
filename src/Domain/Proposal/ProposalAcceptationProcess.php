<?php declare(strict_types=1);

namespace Stratadox\CardGame\Proposal;

use function assert;
use Stratadox\CardGame\Command;
use Stratadox\CardGame\EventBag;
use Stratadox\CardGame\CommandHandler;
use Stratadox\Clock\Clock;

final class ProposalAcceptationProcess implements CommandHandler
{
    private $clock;
    private $proposals;
    private $eventBag;

    public function __construct(
        Clock $clock,
        ProposedMatches $proposals,
        EventBag $eventBag
    ) {
        $this->clock = $clock;
        $this->proposals = $proposals;
        $this->eventBag = $eventBag;
    }

    public function handle(Command $command): void
    {
        assert($command instanceof AcceptTheProposal);

        $proposal = $this->proposals->withId($command->proposal());
        if (!$proposal || !$command->acceptingPlayer()->is($proposal->proposedTo())) {
            $this->eventBag->add(
                new TriedAcceptingUnknownProposal(
                    $command->correlationId(),
                    'Proposal not found'
                )
            );
            return;
        }

        try {
            $proposal->accept($this->clock->now());
        } catch (ProposalHasAlreadyExpired $weAreTooLate) {
            $this->eventBag->add(
                new TriedAcceptingExpiredProposal(
                    $command->correlationId(),
                    $weAreTooLate->getMessage()
                )
            );
            return;
        }

        $this->eventBag->takeFrom($proposal);
    }
}
