<?php declare(strict_types=1);

namespace Stratadox\CardGame\ReadModel\Match;

use Countable;
use Stratadox\CardGame\Match\MatchId;
use Stratadox\CardGame\Proposal\ProposalId;
use function count;

class OngoingMatches implements Countable
{
    /** @var OngoingMatch[] */
    private $matches = [];

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

    public function withId(MatchId $id): OngoingMatch
    {
        foreach ($this->matches as $ongoingMatch) {
            if ($id->is($ongoingMatch->id())) {
                return $ongoingMatch;
            }
        }
        // @todo throw
    }

    public function count(): int
    {
        return count($this->matches);
    }
}
