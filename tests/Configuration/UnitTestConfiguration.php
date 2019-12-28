<?php declare(strict_types=1);

namespace Stratadox\CardGame\Test;

use Ramsey\Uuid\UuidFactory;
use Stratadox\CardGame\Account\AccountOpeningProcess;
use Stratadox\CardGame\Account\OpenAnAccount;
use Stratadox\CardGame\Account\PlayerBase;
use Stratadox\CardGame\CommandHandler;
use Stratadox\CardGame\CorrelationId;
use Stratadox\CardGame\Infrastructure\CommandHandlerAdapter;
use Stratadox\CardGame\Infrastructure\DomainEvents\CommandToEventGlue;
use Stratadox\CardGame\Infrastructure\DomainEvents\Dispatcher;
use Stratadox\CardGame\Infrastructure\DomainEvents\EventCollector;
use Stratadox\CardGame\Infrastructure\IdentityManagement\DefaultAccountIdGenerator;
use Stratadox\CardGame\Infrastructure\IdentityManagement\DefaultMatchIdGenerator;
use Stratadox\CardGame\Infrastructure\IdentityManagement\DefaultProposalIdGenerator;
use Stratadox\CardGame\Infrastructure\Test\InMemoryDecks;
use Stratadox\CardGame\Infrastructure\Test\InMemoryMatches;
use Stratadox\CardGame\Infrastructure\Test\InMemoryPlayerBase;
use Stratadox\CardGame\Infrastructure\Test\InMemoryProposedMatches;
use Stratadox\CardGame\Infrastructure\Test\InMemoryRedirectSources;
use Stratadox\CardGame\Infrastructure\Test\InMemoryVisitorRepository;
use Stratadox\CardGame\Infrastructure\Test\TestClock;
use Stratadox\CardGame\Match\Command\AttackWithCard;
use Stratadox\CardGame\Match\Command\BlockTheAttacker;
use Stratadox\CardGame\Match\Command\CheckIfTurnPhaseExpired;
use Stratadox\CardGame\Match\Command\EndBlocking;
use Stratadox\CardGame\Match\Command\EndCardPlaying;
use Stratadox\CardGame\Match\Command\EndTheTurn;
use Stratadox\CardGame\Match\Command\PlayTheCard;
use Stratadox\CardGame\Match\Command\StartTheMatch;
use Stratadox\CardGame\Match\DeckForAccount;
use Stratadox\CardGame\Match\Handler\AttackingProcess;
use Stratadox\CardGame\Match\Handler\BlockingProcess;
use Stratadox\CardGame\Match\Handler\CardPlayingProcess;
use Stratadox\CardGame\Match\Handler\CombatProcess;
use Stratadox\CardGame\Match\Handler\EndPlayPhaseProcess;
use Stratadox\CardGame\Match\Handler\MatchStartingProcess;
use Stratadox\CardGame\Match\Handler\TurnEndingProcess;
use Stratadox\CardGame\Match\Handler\TurnPhaseExpirationProcess;
use Stratadox\CardGame\Match\Matches;
use Stratadox\CardGame\Proposal\AcceptTheProposal;
use Stratadox\CardGame\Proposal\MatchPropositionProcess;
use Stratadox\CardGame\Proposal\ProposalAcceptationProcess;
use Stratadox\CardGame\Proposal\ProposedMatches;
use Stratadox\CardGame\Proposal\ProposeMatch;
use Stratadox\CardGame\Visiting\AllVisitors;
use Stratadox\CardGame\Visiting\Visit;
use Stratadox\CardGame\Visiting\VisitationProcess;
use Stratadox\Clock\RewindableDateTimeClock;
use Stratadox\CommandHandling\AfterHandling;
use Stratadox\CommandHandling\CommandBus;
use Stratadox\CommandHandling\Handler;

class UnitTestConfiguration implements Configuration
{
    /** @var array */
    private $repositoryForThe;
    /** @var TestClock */
    private $clock;

    private function __construct(array $repositories, TestClock $clock)
    {
        $this->repositoryForThe = $repositories + [
            AllVisitors::class => new InMemoryVisitorRepository(),
            PlayerBase::class => new InMemoryPlayerBase(),
            ProposedMatches::class => new InMemoryProposedMatches(),
            Matches::class => new InMemoryMatches(),
            DeckForAccount::class => new InMemoryDecks(),
        ];
        $this->clock = $clock;
    }

    public static function make(): Configuration
    {
        return new self([], TestClock::make());
    }

    public static function withClock(TestClock $clock): self
    {
        return new self([], $clock);
    }

    public function handler(Dispatcher $dispatcher): Handler
    {
        $eventBag = new EventCollector();
        $uuidFactory = new UuidFactory();
        $bus = AfterHandling::invoke(
            new CommandToEventGlue(
                $eventBag,
                $dispatcher
            ),
            CommandBus::handling([
                Visit::class => $this->adapt(new VisitationProcess(
                    $this->repositoryForThe[AllVisitors::class],
                    new InMemoryRedirectSources(),
                    $this->clock,
                    $eventBag
                )),
                OpenAnAccount::class => $this->adapt(new AccountOpeningProcess(
                    new DefaultAccountIdGenerator($uuidFactory),
                    $this->repositoryForThe[AllVisitors::class],
                    $this->repositoryForThe[PlayerBase::class],
                    $eventBag
                )),
                ProposeMatch::class => $this->adapt(new MatchPropositionProcess(
                    new DefaultProposalIdGenerator($uuidFactory),
                    RewindableDateTimeClock::using($this->clock),
                    $this->repositoryForThe[ProposedMatches::class],
                    $this->repositoryForThe[PlayerBase::class],
                    $eventBag
                )),
                AcceptTheProposal::class => $this->adapt(new ProposalAcceptationProcess(
                    $this->clock,
                    $this->repositoryForThe[ProposedMatches::class],
                    $eventBag
                )),
                StartTheMatch::class => $this->adapt(new MatchStartingProcess(
                    $this->repositoryForThe[ProposedMatches::class],
                    new DefaultMatchIdGenerator($uuidFactory),
                    $this->repositoryForThe[Matches::class],
                    $this->repositoryForThe[DeckForAccount::class],
                    $this->clock,
                    $eventBag
                )),
                CheckIfTurnPhaseExpired::class => $this->adapt(new TurnPhaseExpirationProcess(
                    $this->repositoryForThe[Matches::class],
                    $this->clock,
                    $eventBag
                )),
                PlayTheCard::class => $this->adapt(new CardPlayingProcess(
                    $this->repositoryForThe[Matches::class],
                    $this->clock,
                    $eventBag
                )),
                EndCardPlaying::class => $this->adapt(new EndPlayPhaseProcess(
                    $this->repositoryForThe[Matches::class],
                    $this->clock,
                    $eventBag
                )),
                AttackWithCard::class => $this->adapt(new AttackingProcess(
                    $this->repositoryForThe[Matches::class],
                    $this->clock,
                    $eventBag
                )),
                EndTheTurn::class => $this->adapt(new TurnEndingProcess(
                    $this->repositoryForThe[Matches::class],
                    $this->clock,
                    $eventBag
                )),
                BlockTheAttacker::class => $this->adapt(new BlockingProcess(
                    $this->repositoryForThe[Matches::class],
                    $this->clock,
                    $eventBag
                )),
                EndBlocking::class => $this->adapt(new CombatProcess(
                    $this->repositoryForThe[Matches::class],
                    $this->clock,
                    $eventBag
                )),
            ])
        );
        $this->clock->eachPassingSecondApply(static function () use ($bus): void {
            $bus->handle(
                CheckIfTurnPhaseExpired::with(CorrelationId::from('some id'))
            );
        });
        return $bus;
    }

    private function adapt(CommandHandler $handler): Handler
    {
        return new CommandHandlerAdapter($handler);
    }
}
