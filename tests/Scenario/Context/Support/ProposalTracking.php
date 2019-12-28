<?php declare(strict_types=1);

namespace Stratadox\CardGame\Context\Support;

use Stratadox\CardGame\Account\AccountId;
use Stratadox\CardGame\Proposal\MatchProposal;
use Stratadox\CardGame\Proposal\ProposalId;
use Stratadox\CardGame\ReadModel\Proposal\MatchProposals;
use function end;

trait ProposalTracking
{
    /** @var MatchProposals */
    private $matchProposals;
    /** @var MatchProposal[] */
    private $latestProposalFor = [];
    /** @var MatchProposal[] */
    private $latestProposalBy = [];
    /** @var MatchProposal|null */
    private $latestProposal;

    protected function rememberLatestProposalBetween(
        string $proposedBy,
        string $proposedTo
    ): void {
        // @todo better way of matching matches and players
        $proposals = $this->matchProposals->for($this->account($proposedTo));
        $this->latestProposalBy[$proposedBy] = end($proposals);
        $this->latestProposalFor[$proposedTo] = end($proposals);
        $this->latestProposal = end($proposals);
    }

    protected function newestProposalFor(string $player): ProposalId
    {
        if (!isset($this->latestProposalFor[$player])) {
            return ProposalId::from('no-such-proposal');
        }
        return $this->latestProposalFor[$player]->id();
    }

    protected function newestProposal(): ?ProposalId
    {
        return $this->latestProposal ? $this->latestProposal->id() : null;
    }

    abstract protected function account(string $player): AccountId;
}
