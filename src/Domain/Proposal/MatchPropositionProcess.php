<?php declare(strict_types=1);

namespace Stratadox\CardGame\Proposal;

use function assert;
use DateInterval;
use Stratadox\CardGame\EventBag;
use Stratadox\CardGame\Account\PlayerAccount;
use Stratadox\CardGame\Account\PlayerBase;
use Stratadox\CardGame\Account\AccountId;
use Stratadox\Clock\Clock;
use Stratadox\Clock\RewindableClock;
use Stratadox\CommandHandling\Handler;

final class MatchPropositionProcess implements Handler
{
    private $newIdentity;
    private $clock;
    private $proposals;
    private $players;
    private $eventBag;

    public function __construct(
        ProposalIdGenerator $identityGenerator,
        RewindableClock $clock,
        ProposedMatches $proposals,
        PlayerBase $players,
        EventBag $eventBag
    ) {
        $this->newIdentity = $identityGenerator;
        $this->clock = $clock;
        $this->proposals = $proposals;
        $this->players = $players;
        $this->eventBag = $eventBag;
    }

    public function handle(object $proposition): void
    {
        assert($proposition instanceof ProposeMatch);

        $proposal = $this->proposeMatch(
            $this->players->withId($proposition->proposedBy()),
            $proposition->proposedTo(),
            // @todo cloning the clock is leaky abstraction, fix in right place
            (clone $this->clock)->fastForward(new DateInterval('PT30S')),
            $this->newIdentity->generate()
        );

        $this->proposals->add($proposal);

        $this->eventBag->takeFrom($proposal);
    }

    private function proposeMatch(
        PlayerAccount $thePlayerThatProposesTheMatch,
        AccountId $theOtherPlayer,
        Clock $validUntil,
        ProposalId $id
    ): MatchProposal {
        return $thePlayerThatProposesTheMatch->proposeMatchTo(
            $theOtherPlayer,
            $validUntil->now(),
            $id
        );
    }
}
