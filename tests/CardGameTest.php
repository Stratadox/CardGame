<?php declare(strict_types=1);

namespace Stratadox\CardGame\Test;

use PHPUnit\Framework\Constraint\Constraint;
use PHPUnit\Framework\Constraint\LogicalOr;
use PHPUnit\Framework\Constraint\LogicalXor;
use PHPUnit\Framework\TestCase;
use Ramsey\Uuid\UuidFactory;
use Stratadox\CardGame\EventBag;
use Stratadox\CardGame\EventHandler\AccountOverviewCreator;
use Stratadox\CardGame\EventHandler\PlayerListAppender;
use Stratadox\CardGame\EventHandler\ProposalAcceptanceNotifier;
use Stratadox\CardGame\EventHandler\ProposalSender;
use Stratadox\CardGame\EventHandler\StatisticsUpdater;
use Stratadox\CardGame\Infrastructure\DomainEvents\CommandToEventGlue;
use Stratadox\CardGame\Infrastructure\DomainEvents\Dispatcher;
use Stratadox\CardGame\Infrastructure\DomainEvents\EventCollector;
use Stratadox\CardGame\Infrastructure\Test\InMemoryProposedMatches;
use Stratadox\CardGame\Infrastructure\Test\InMemoryRedirectSources;
use Stratadox\CardGame\Infrastructure\IdentityManagement\DefaultAccountIdGenerator;
use Stratadox\CardGame\Infrastructure\IdentityManagement\DefaultProposalIdGenerator;
use Stratadox\CardGame\Infrastructure\Test\TestClock;
use Stratadox\CardGame\Match\Command\PlayTheCard;
use Stratadox\CardGame\Match\Command\StartTheMatch;
use Stratadox\CardGame\Match\Handler\CardPlayingProcess;
use Stratadox\CardGame\Match\Handler\MatchStartingProcess;
use Stratadox\CardGame\Account\Event\VisitorOpenedAnAccount;
use Stratadox\CardGame\Account\Handler\AccountOpeningProcess;
use Stratadox\CardGame\Account\Command\OpenAnAccount;
use Stratadox\CardGame\Proposal\Event\MatchWasProposed;
use Stratadox\CardGame\Proposal\Event\ProposalWasAccepted;
use Stratadox\CardGame\ReadModel\Account\AccountOverviews;
use Stratadox\CardGame\ReadModel\Match\Battlefield;
use Stratadox\CardGame\ReadModel\Match\UnitCard;
use Stratadox\CardGame\ReadModel\Match\CardsInHand;
use Stratadox\CardGame\ReadModel\Match\OngoingMatch;
use Stratadox\CardGame\ReadModel\Match\OngoingMatches;
use Stratadox\CardGame\ReadModel\PlayerList;
use Stratadox\CardGame\Infrastructure\Test\InMemoryPlayerBase;
use Stratadox\CardGame\Infrastructure\Test\InMemoryVisitorRepository;
use Stratadox\CardGame\AccountId;
use Stratadox\CardGame\Proposal\Command\AcceptTheProposal;
use Stratadox\CardGame\Proposal\Handler\MatchPropositionProcess;
use Stratadox\CardGame\Proposal\Handler\ProposalAcceptationProcess;
use Stratadox\CardGame\Proposal\Command\ProposeMatch;
use Stratadox\CardGame\ReadModel\PageVisitsStatisticsReport;
use Stratadox\CardGame\ReadModel\Proposal\AcceptedProposals;
use Stratadox\CardGame\ReadModel\Proposal\MatchProposals;
use Stratadox\CardGame\Visiting\Event\BroughtVisitor;
use Stratadox\CardGame\Visiting\Event\VisitedPage;
use Stratadox\CardGame\Visiting\Handler\VisitationProcess;
use Stratadox\CardGame\Visiting\Command\Visit;
use Stratadox\CardGame\VisitorId;
use Stratadox\Clock\RewindableClock;
use Stratadox\CommandHandling\AfterHandling;
use Stratadox\CommandHandling\CommandBus;
use Stratadox\CommandHandling\Handler;

abstract class CardGameTest extends TestCase
{
    /** @var Handler */
    private $input;

    /** @var Handler[] */
    private $overruledHandlers = [];

    /** @var RewindableClock */
    protected $clock;

    /** @var PageVisitsStatisticsReport */
    protected $statistics;

    /** @var AccountOverviews */
    protected $accountOverviews;

    /** @var PlayerList */
    protected $playerList;

    /** @var MatchProposals */
    protected $matchProposals;

    /** @var AcceptedProposals */
    protected $acceptedProposals;

    /** @var CardsInHand */
    protected $cardsInTheHand;

    /** @var UnitCard[] */
    protected $testCard = [];

    /** @var OngoingMatches */
    protected $ongoingMatches;

    /** @var null|OngoingMatch */
    protected $currentMatch;

    /** @var Battlefield */
    protected $battlefield;

    protected function setUp(): void
    {
        $this->clock = TestClock::make();

        $this->statistics = new PageVisitsStatisticsReport();
        $this->matchProposals = new MatchProposals($this->clock);
        $this->acceptedProposals = new AcceptedProposals();
        $this->accountOverviews = new AccountOverviews();
        $this->playerList = PlayerList::startEmpty();
        $this->cardsInTheHand = new CardsInHand();
        $this->ongoingMatches = new OngoingMatches();
        $this->battlefield = new Battlefield();
        $this->testCard = [
            new UnitCard('test 1', 2),
            new UnitCard('test 2', 4),
            new UnitCard('test 3', 3),
            new UnitCard('test 4', 1),
            new UnitCard('test 5', 2),
            new UnitCard('test 6', 5),
            new UnitCard('test 7', 2),
        ];

        $eventBag = new EventCollector();
        $this->input = AfterHandling::invoke(
            new CommandToEventGlue(
                $eventBag,
                $this->eventDispatcher()
            ),
            $this->commandBus($eventBag)
        );
    }

    private function eventDispatcher(): Dispatcher
    {
        return new Dispatcher([
            BroughtVisitor::class => new StatisticsUpdater($this->statistics),
            VisitedPage::class => new StatisticsUpdater($this->statistics),
            VisitorOpenedAnAccount::class => [
                new PlayerListAppender($this->playerList),
                new AccountOverviewCreator($this->accountOverviews)
            ],
            MatchWasProposed::class => new ProposalSender($this->matchProposals),
            ProposalWasAccepted::class => new ProposalAcceptanceNotifier(
                $this->acceptedProposals
            ),
        ]);
    }

    protected function overruleCommandHandler(
        string $command,
        Handler $handler
    ): void {
        $this->overruledHandlers[$command] = $handler;
    }

    private function commandBus(EventBag $eventBag): Handler
    {
        $visitors = new InMemoryVisitorRepository();
        $playerBase = new InMemoryPlayerBase();
        $proposals = new InMemoryProposedMatches();
        $uuidFactory = new UuidFactory();
        return CommandBus::handling([
            Visit::class => new VisitationProcess(
                $visitors,
                new InMemoryRedirectSources(),
                $this->clock,
                $eventBag
            ),
            OpenAnAccount::class => new AccountOpeningProcess(
                new DefaultAccountIdGenerator($uuidFactory),
                $visitors,
                $playerBase,
                $eventBag
            ),
            ProposeMatch::class => new MatchPropositionProcess(
                new DefaultProposalIdGenerator($uuidFactory),
                $this->clock,
                $proposals,
                $playerBase,
                $eventBag
            ),
            AcceptTheProposal::class => new ProposalAcceptationProcess(
                $this->clock,
                $proposals,
                $eventBag
            ),
            StartTheMatch::class => new MatchStartingProcess(),
            PlayTheCard::class => new CardPlayingProcess(),
        ]);
    }

    protected function handle(object $command): void
    {
        $this->input->handle($command);
    }

    protected function signUpForTheGame(VisitorId $visitorId): void
    {
        $this->handle(Visit::page('home', 'https://example.com', $visitorId));
        $this->handle(OpenAnAccount::forVisitorWith($visitorId));
    }

    protected function prepareMatchBetween(
        AccountId $playerOne,
        AccountId $playerTwo
    ): void {
        $this->handle(ProposeMatch::between($playerOne, $playerTwo));
        $this->handle(AcceptTheProposal::withId(
            $this->matchProposals->for($playerTwo)[0]->id()
        ));
    }

    protected function setUpNewMatch(): void
    {
        $visitor1 = VisitorId::from('id-1');
        $visitor2 = VisitorId::from('id-2');

        $this->signUpForTheGame($visitor1);
        $this->signUpForTheGame($visitor2);

        $this->prepareMatchBetween(
            $this->accountOverviews->forVisitor($visitor1)->id(),
            $this->accountOverviews->forVisitor($visitor2)->id()
        );

        $proposal = $this->acceptedProposals->since($this->clock->now())[0];

        $this->handle(StartTheMatch::forProposal($proposal->id()));

        $this->currentMatch = $this->ongoingMatches->forProposal($proposal->id());
    }

    protected function assertEither(
        $value,
        Constraint ...$constraints
    ): void {
        $this->assertThat($value, LogicalOr::fromConstraints(...$constraints));
    }

    protected function assertEitherButNotBoth(
        $value,
        Constraint $constraint1,
        Constraint $constraint2
    ): void {
        $this->assertThat($value, LogicalXor::fromConstraints(
            $constraint1,
            $constraint2
        ));
    }
}
