<?php declare(strict_types=1);

namespace Stratadox\CardGame\Match;

use Stratadox\CardGame\DomainEventRecording;
use Stratadox\CardGame\Proposal\ProposalId;
use function array_map;

final class MatchSetup
{
    // @todo get rid of this
    use DomainEventRecording;

    private $id;
    private $players;
    private $deckFor = [];

    public function __construct(
        MatchId $id,
        MatchEvent $creationEvent,
        PlayerId ...$players
    ) {
        $this->id = $id;
        $this->players = $players;
        $this->events[] = $creationEvent;
    }

    public static function fromProposal(
        ProposalId $proposalId,
        MatchId $matchId,
        PlayerId ...$players
    ): self {
        return new self(
            $matchId,
            new StartedSettingUpMatchForProposal($matchId, $proposalId, ...$players),
            ...$players
        );
    }

    public function addDeckFor(PlayerId $player, Deck $deck): void
    {
        $this->deckFor[$player->id()] = $deck;
    }

    public function beginMatch(PlayerId $whoBegins): Match
    {
        return Match::fromSetup(
            $this->id,
            $whoBegins,
            $this->events,
            ...array_map(
                function (PlayerId $id): Player {
                    return $this->preparePlayer($id);
                },
                $this->players
            )
        );
    }

    private function preparePlayer(PlayerId $id): Player
    {
        return Player::with($id, $this->deckFor[(string) $id]);
    }
}
