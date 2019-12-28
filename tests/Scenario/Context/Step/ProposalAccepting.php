<?php declare(strict_types=1);

namespace Stratadox\CardGame\Context\Step;

use Stratadox\CardGame\Account\AccountId;
use Stratadox\CardGame\Command;
use Stratadox\CardGame\CorrelationId;
use Stratadox\CardGame\Proposal\AcceptTheProposal;
use Stratadox\CardGame\Proposal\ProposalId;
use Stratadox\CardGame\Proposal\ProposeMatch;

trait ProposalAccepting
{
    /** @var string|null */
    private $proposer;
    /** @var string|null */
    private $proposedTo;

    /**
     * @Given :proposer proposed a match to :receiver
     * @When :proposer proposes a match to :receiver
     */
    public function proposesAMatch(string $proposer, string $receiver)
    {
        $this->proposer = $proposer;
        $this->proposedTo = $receiver;
        $this->handle(ProposeMatch::between(
            $this->account($proposer),
            $this->account($receiver),
            $this->correlation()
        ));
        $this->rememberLatestProposalBetween($proposer, $receiver);
    }

    /**
     * @When :player accepts the proposal
     * @Given :player accepted the proposal
     */
    public function acceptsTheProposal(string $player)
    {
        $this->handle(AcceptTheProposal::withId(
            $this->newestProposalFor($player),
            $this->account($player),
            $this->correlation()
        ));
    }

    abstract protected function rememberLatestProposalBetween(
        string $proposedBy,
        string $proposedTo
    ): void;
    abstract protected function newestProposalFor(string $player): ProposalId;
    abstract protected function correlation(): CorrelationId;
    abstract protected function account(string $player): AccountId;
    abstract protected function handle(Command $command): void;
}
