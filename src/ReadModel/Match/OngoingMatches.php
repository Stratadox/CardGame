<?php declare(strict_types=1);

namespace Stratadox\CardGame\ReadModel\Match;

use Stratadox\CardGame\ProposalId;

class OngoingMatches
{
    /** @var OngoingMatch[] */
    private $matches;

    public function addFromProposal(ProposalId $proposal, OngoingMatch $match): void
    {
        $this->matches[$proposal->id()] = $match;
    }

    /** @throws NoSuchMatch */
    public function forProposal(ProposalId $proposal): OngoingMatch
    {
        if (!isset($this->matches[$proposal->id()])) {
            throw NoSuchMatch::forProposal($proposal);
        }
        return $this->matches[$proposal->id()];
    }
}
