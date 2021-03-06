<?php declare(strict_types=1);

namespace Stratadox\CardGame\Proposal;

use function assert;
use DateInterval;
use Stratadox\CardGame\Command;
use Stratadox\CardGame\EventBag;
use Stratadox\CardGame\Account\PlayerAccount;
use Stratadox\CardGame\Account\PlayerBase;
use Stratadox\CardGame\Account\AccountId;
use Stratadox\CardGame\CommandHandler;
use Stratadox\Clock\Clock;
use Stratadox\Clock\RewindableClock;

final class MatchPropositionProcess implements CommandHandler
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

    public function handle(Command $proposition): void
    {
        assert($proposition instanceof ProposeMatch);

        $proposal = $this->proposeMatch(
            $this->players->withId($proposition->proposedBy()),
            $proposition->proposedTo(),
            $this->clock->fastForward(new DateInterval('PT30S')),
            $this->newIdentity->generate()
        );

        $this->proposals->add($proposal);

        $this->eventBag->takeFrom($proposal);
    }

    private function proposeMatch(
        PlayerAccount $proposingPlayer,
        AccountId $otherPlayer,
        Clock $validUntil,
        ProposalId $id
    ): MatchProposal {
        return $proposingPlayer->proposeMatchTo(
            $otherPlayer,
            $validUntil->now(),
            $id
        );
    }
}
