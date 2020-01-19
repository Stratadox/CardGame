<?php declare(strict_types=1);

namespace Stratadox\CardGame\ReadModel\Proposal;

use DateTimeInterface;
use Stratadox\CardGame\Account\AccountId;
use Stratadox\CardGame\Proposal\ProposalId;
use Stratadox\Clock\Clock;

class MatchProposals
{
    /** @var MatchProposal[] */
    private $proposals = [];
    private $clock;

    public function __construct(Clock $clock)
    {
        $this->clock = $clock;
    }

    public function add(MatchProposal $proposal): void
    {
        $this->proposals[(string) $proposal->id()] = $proposal;
    }

    /** @return MatchProposal[] */
    public function for(AccountId $player, DateTimeInterface $currently = null): array
    {
        $currently = $currently ?: $this->clock->now();
        $proposals = [];
        foreach ($this->proposals as $proposal) {
            if ($proposal->canBeAcceptedBy($player, $currently)) {
                $proposals[] = $proposal;
            }
        }
        return $proposals;
    }

    public function byId(ProposalId $id): MatchProposal
    {
        return $this->proposals[(string) $id];
    }
}
