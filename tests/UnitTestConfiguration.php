<?php declare(strict_types=1);

namespace Stratadox\CardGame\Test;

use Ramsey\Uuid\UuidFactory;
use Stratadox\CardGame\Account\AccountOpeningProcess;
use Stratadox\CardGame\Account\OpenAnAccount;
use Stratadox\CardGame\CommandHandler;
use Stratadox\CardGame\EventBag;
use Stratadox\CardGame\Infrastructure\CommandHandlerAdapter;
use Stratadox\CardGame\Infrastructure\DomainEvents\CommandToEventGlue;
use Stratadox\CardGame\Infrastructure\DomainEvents\Dispatcher;
use Stratadox\CardGame\Infrastructure\IdentityManagement\DefaultAccountIdGenerator;
use Stratadox\CardGame\Infrastructure\IdentityManagement\DefaultMatchIdGenerator;
use Stratadox\CardGame\Infrastructure\IdentityManagement\DefaultProposalIdGenerator;
use Stratadox\CardGame\Infrastructure\Test\InMemoryDecks;
use Stratadox\CardGame\Infrastructure\Test\InMemoryMatches;
use Stratadox\CardGame\Infrastructure\Test\InMemoryPlayerBase;
use Stratadox\CardGame\Infrastructure\Test\InMemoryProposedMatches;
use Stratadox\CardGame\Infrastructure\Test\InMemoryRedirectSources;
use Stratadox\CardGame\Infrastructure\Test\InMemoryVisitorRepository;
use Stratadox\CardGame\Match\Command\AttackWithCard;
use Stratadox\CardGame\Match\Command\BlockTheAttacker;
use Stratadox\CardGame\Match\Command\EndBlocking;
use Stratadox\CardGame\Match\Command\EndCardPlaying;
use Stratadox\CardGame\Match\Command\EndTheTurn;
use Stratadox\CardGame\Match\Command\PlayTheCard;
use Stratadox\CardGame\Match\Command\StartTheMatch;
use Stratadox\CardGame\Match\Handler\AttackingProcess;
use Stratadox\CardGame\Match\Handler\BlockingProcess;
use Stratadox\CardGame\Match\Handler\CardPlayingProcess;
use Stratadox\CardGame\Match\Handler\CombatProcess;
use Stratadox\CardGame\Match\Handler\EndPlayPhaseProcess;
use Stratadox\CardGame\Match\Handler\MatchStartingProcess;
use Stratadox\CardGame\Match\Handler\TurnEndingProcess;
use Stratadox\CardGame\Proposal\AcceptTheProposal;
use Stratadox\CardGame\Proposal\MatchPropositionProcess;
use Stratadox\CardGame\Proposal\ProposalAcceptationProcess;
use Stratadox\CardGame\Proposal\ProposeMatch;
use Stratadox\CardGame\Visiting\Visit;
use Stratadox\CardGame\Visiting\VisitationProcess;
use Stratadox\Clock\RewindableClock;
use Stratadox\CommandHandling\AfterHandling;
use Stratadox\CommandHandling\CommandBus;
use Stratadox\CommandHandling\Handler;

class UnitTestConfiguration implements Configuration
{
    public function commandHandler(
        EventBag $eventBag,
        RewindableClock $clock,
        Dispatcher $dispatcher
    ): Handler {
        $visitors = new InMemoryVisitorRepository();
        $playerBase = new InMemoryPlayerBase();
        $proposals = new InMemoryProposedMatches();
        $matches = new InMemoryMatches();
        $decks = new InMemoryDecks();
        $uuidFactory = new UuidFactory();
        return AfterHandling::invoke(
            new CommandToEventGlue(
                $eventBag,
                $dispatcher
            ),
            CommandBus::handling([
                Visit::class => $this->adapt(new VisitationProcess(
                    $visitors,
                    new InMemoryRedirectSources(),
                    $clock,
                    $eventBag
                )),
                OpenAnAccount::class => $this->adapt(new AccountOpeningProcess(
                    new DefaultAccountIdGenerator($uuidFactory),
                    $visitors,
                    $playerBase,
                    $eventBag
                )),
                ProposeMatch::class => $this->adapt(new MatchPropositionProcess(
                    new DefaultProposalIdGenerator($uuidFactory),
                    $clock,
                    $proposals,
                    $playerBase,
                    $eventBag
                )),
                AcceptTheProposal::class => $this->adapt(new ProposalAcceptationProcess(
                    $clock,
                    $proposals,
                    $eventBag
                )),
                StartTheMatch::class => $this->adapt(new MatchStartingProcess(
                    $proposals,
                    new DefaultMatchIdGenerator($uuidFactory),
                    $matches,
                    $decks,
                    $clock,
                    $eventBag
                )),
                PlayTheCard::class => $this->adapt(new CardPlayingProcess(
                    $matches,
                    $clock,
                    $eventBag
                )),
                EndCardPlaying::class => $this->adapt(new EndPlayPhaseProcess(
                    $matches,
                    $clock,
                    $eventBag
                )),
                AttackWithCard::class => $this->adapt(new AttackingProcess(
                    $matches,
                    $clock,
                    $eventBag
                )),
                EndTheTurn::class => $this->adapt(new TurnEndingProcess(
                    $matches,
                    $clock,
                    $eventBag
                )),
                BlockTheAttacker::class => $this->adapt(new BlockingProcess(
                    $matches,
                    $clock,
                    $eventBag
                )),
                EndBlocking::class => $this->adapt(new CombatProcess(
                    $matches,
                    $clock,
                    $eventBag
                )),
            ])
        );
    }

    private function adapt(CommandHandler $handler): Handler
    {
        return new CommandHandlerAdapter($handler);
    }
}
