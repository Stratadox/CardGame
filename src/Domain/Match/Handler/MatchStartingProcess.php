<?php declare(strict_types=1);

namespace Stratadox\CardGame\Match\Handler;

use Stratadox\CardGame\EventBag;
use Stratadox\CardGame\Match\DeckForAccount;
use Stratadox\CardGame\Match\Decks;
use Stratadox\CardGame\Match\Event\TriedStartingMatchWithoutProposal;
use Stratadox\CardGame\Match\Matches;
use Stratadox\CardGame\Match\MatchIdGenerator;
use Stratadox\CardGame\Match\Command\StartTheMatch;
use Stratadox\CardGame\Match\Event\TriedStartingMatchForPendingProposal;
use Stratadox\CardGame\Proposal\ProposalHasNotBeenAccepted;
use Stratadox\CardGame\Proposal\ProposedMatches;
use Stratadox\Clock\Clock;
use Stratadox\CommandHandling\Handler;
use function assert;

final class MatchStartingProcess implements Handler
{
    private $proposals;
    private $newMatchId;
    private $matches;
    private $decks;
    private $clock;
    private $eventBag;

    public function __construct(
        ProposedMatches $proposals,
        MatchIdGenerator $newMatchId,
        Matches $matches,
        DeckForAccount $deckForAccount,
        Clock $clock,
        EventBag $eventBag
    ) {
        $this->proposals = $proposals;
        $this->newMatchId = $newMatchId;
        $this->matches = $matches;
        $this->decks = $deckForAccount;
        $this->clock = $clock;
        $this->eventBag = $eventBag;
    }

    public function handle(object $command): void
    {
        assert($command instanceof StartTheMatch);

        $proposal = $this->proposals->withId($command->proposal());
        if ($proposal === null) {
            $this->eventBag->add(
                new TriedStartingMatchWithoutProposal($command->correlationId())
            );
            return;
        }

        try {
            $match = $proposal->start(
                $this->newMatchId->generate(),
                new Decks(
                    $this->decks->deckFor($proposal->proposedBy()),
                    $this->decks->deckFor($proposal->proposedBy())
                ),
                $this->clock->now(),
                0,
                1
            );
        } catch (ProposalHasNotBeenAccepted $cannotStartYet) {
            $this->eventBag->add(
                new TriedStartingMatchForPendingProposal($command->correlationId())
            );
            return;
        }

        $match->drawOpeningHands();

        $this->matches->add($match);
        $this->eventBag->takeFrom($match);
    }
}
