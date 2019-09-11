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

    public function remove(ProposalId $id): void
    {
        unset($this->proposals[(string) $id]);
    }

    /** @return MatchProposal[] */
    public function for(AccountId $player, DateTimeInterface $currently = null): array
    {
        $currently = $currently ?: $this->clock->now();
        $proposals = [];
        foreach ($this->proposals as $theProposal) {
            if ($theProposal->canBeAcceptedBy($player, $currently)) {
                $proposals[] = $theProposal;
            }
        }
        return $proposals;
    }
}
