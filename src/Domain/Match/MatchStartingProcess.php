<?php declare(strict_types=1);

namespace Stratadox\CardGame\Match;

use Stratadox\CardGame\EventBag;
use Stratadox\CardGame\Proposal\ProposalHasNotBeenAccepted;
use Stratadox\CardGame\Proposal\ProposedMatches;
use Stratadox\Clock\Clock;
use Stratadox\CommandHandling\Handler;
use function assert;

final class MatchStartingProcess implements Handler
{
    private $proposals;
    private $newMatchId;
    private $newPlayerId;
    private $matches;
    private $decks;
    private $clock;
    private $eventBag;

    public function __construct(
        ProposedMatches $proposals,
        MatchIdGenerator $newMatchId,
        PlayerIdGenerator $newPlayerId,
        Matches $matches,
        DeckForAccount $deckForAccount,
        Clock $clock,
        EventBag $eventBag
    ) {
        $this->proposals = $proposals;
        $this->newMatchId = $newMatchId;
        $this->newPlayerId = $newPlayerId;
        $this->matches = $matches;
        $this->decks = $deckForAccount;
        $this->clock = $clock;
        $this->eventBag = $eventBag;
    }

    public function handle(object $command): void
    {
        assert($command instanceof StartTheMatch);

        $proposal = $this->proposals->withId($command->proposal());
        assert($proposal !== null); // @todo error event instead?

        try {
            $match = $proposal->start(
                $this->newMatchId->generate(),
                new Decks(
                    $this->decks->deckFor($proposal->proposedBy()),
                    $this->decks->deckFor($proposal->proposedBy())
                ),
                $this->clock->now(),
                $this->newPlayerId->generate(),
                $this->newPlayerId->generate()
            );
        } catch (ProposalHasNotBeenAccepted $cannotStartYet) {
            $this->eventBag->add(
                new TriedStartingMatchForPendingProposal($proposal->id())
            );
            return;
        }

        $match->drawOpeningHands();

        $this->matches->add($match);
        $this->eventBag->takeFrom($match);
    }
}
