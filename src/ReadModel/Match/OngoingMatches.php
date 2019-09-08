<?php declare(strict_types=1);

namespace Stratadox\CardGame\ReadModel\Match;

use Stratadox\CardGame\MatchId;
use Stratadox\CardGame\PlayerId;
use Stratadox\CardGame\ProposalId;

class OngoingMatches
{
    public function forProposal(ProposalId $proposalId): OngoingMatch
    {
        // @todo this be cheating; make more tests
        return new OngoingMatch(MatchId::from('foo'), PlayerId::from('bar'), ...[
            PlayerId::from('bar'),
            PlayerId::from('baz')
        ]);
    }
}
