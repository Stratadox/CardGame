<?php declare(strict_types=1);

namespace Stratadox\CardGame\Match\Match;

use Stratadox\CardGame\Match\Player\PlayerId;
use Stratadox\CardGame\Proposal\ProposalId;

final class StartedSettingUpMatchForProposal implements MatchEvent
{
    private $matchId;
    private $proposalId;
    private $players;

    public function __construct(
        MatchId $matchId,
        ProposalId $proposalId,
        PlayerId ...$players
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

    /** @return PlayerId[] */
    public function players(): array
    {
        return $this->players;
    }

    public function payload(): array
    {
        return [
            'proposal' => $this->proposalId,
            'players' => $this->players,
        ];
    }
}
