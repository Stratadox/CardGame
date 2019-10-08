<?php declare(strict_types=1);

namespace Stratadox\CardGame\Match;

use Stratadox\CardGame\Proposal\ProposalId;

final class StartedMatchForProposal implements MatchEvent
{
    private $matchId;
    private $proposalId;
    private $players;

    public function __construct(
        MatchId $matchId,
        ProposalId $proposalId,
        int ...$players
    ) {
        $this->matchId = $matchId;
        $this->proposalId = $proposalId;
        $this->players = $players;
    }

    public function aggregateId(): MatchId
    {
        return $this->matchId;
    }

    public function proposal(): ProposalId
    {
        return $this->proposalId;
    }

    /** @return int[] */
    public function players(): array
    {
        return $this->players;
    }
}
