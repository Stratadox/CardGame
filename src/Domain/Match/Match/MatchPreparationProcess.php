<?php declare(strict_types=1);

namespace Stratadox\CardGame\Match\Match;

use Stratadox\CardGame\EventBag;
use Stratadox\CardGame\Match\Player\PlayerIdGenerator;
use Stratadox\CardGame\Match\Match\StartTheMatch;
use Stratadox\CardGame\Proposal\ProposalHasNotBeenAccepted;
use Stratadox\CardGame\Proposal\ProposedMatches;
use Stratadox\CommandHandling\Handler;
use function assert;

final class MatchPreparationProcess implements Handler
{
    private $proposals;
    private $newMatchId;
    private $newPlayerId;
    private $matches;
    private $eventBag;

    public function __construct(
        ProposedMatches $proposals,
        MatchIdGenerator $newMatchId,
        PlayerIdGenerator $newPlayerId,
        Matches $matches,
        EventBag $eventBag
    ) {
        $this->proposals = $proposals;
        $this->newMatchId = $newMatchId;
        $this->newPlayerId = $newPlayerId;
        $this->matches = $matches;
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

        try {
            $match = $proposal->prepare(
                $this->newMatchId->generate(),
                $this->newPlayerId->generate(),
                $this->newPlayerId->generate()
            );
        } catch (ProposalHasNotBeenAccepted $cannotStartYet) {
            // @todo error handling?
            return;
        }

        $this->matches->add($match);
        $this->eventBag->takeFrom($match);
    }
}
