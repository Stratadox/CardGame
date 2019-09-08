<?php declare(strict_types=1);

namespace Stratadox\CardGame\Match\Handler;

use function assert;
use Stratadox\CardGame\EventBag;
use Stratadox\CardGame\Match\Command\StartTheMatch;
use Stratadox\CardGame\Match\DecidesWhoStarts;
use Stratadox\CardGame\Match\Decks;
use Stratadox\CardGame\Match\Matches;
use Stratadox\CardGame\Proposal\ProposalHasNotBeenAccepted;
use Stratadox\CardGame\Proposal\ProposedMatches;
use Stratadox\CommandHandling\Handler;

final class MatchStartingProcess implements Handler
{
    private $newMatchId;
    private $newPlayerId;
    private $proposals;
    private $deckToUse;
    private $matches;
    private $whoStarts;
    private $eventBag;

    public function __construct(
        MatchIdGenerator $newMatchId,
        PlayerIdGenerator $newPlayerId,
        ProposedMatches $proposals,
        Decks $deckToUse,
        Matches $matches,
        DecidesWhoStarts $whoStarts,
        EventBag $eventBag
    ) {
        $this->newMatchId = $newMatchId;
        $this->newPlayerId = $newPlayerId;
        $this->proposals = $proposals;
        $this->deckToUse = $deckToUse;
        $this->matches = $matches;
        $this->whoStarts = $whoStarts;
        $this->eventBag = $eventBag;
    }

    public function handle(object $command): void
    {
        assert($command instanceof StartTheMatch);

        $proposal = $this->proposals->withId($command->proposal());
        if (!$proposal) {
            // @todo error handling?
            return;
        }
        $playerOne = $this->newPlayerId->generate();
        $playerTwo = $this->newPlayerId->generate();
        try {
            $setup = $proposal->begin(
                $this->newMatchId->generate(),
                $playerOne,
                $playerTwo
            );
        } catch (ProposalHasNotBeenAccepted $cannotStartYet) {
            // @todo error handling?
            return;
        }

        $setup->addDeckFor($playerOne, $this->deckToUse->for($proposal->proposedBy()));
        $setup->addDeckFor($playerTwo, $this->deckToUse->for($proposal->proposedTo()));

        $match = $setup->beginMatch($this->whoStarts->chooseBetween(
            $playerOne,
            $playerTwo
        ));

        $this->matches->add($match);
        $this->eventBag->takeFrom($match);
    }
}
