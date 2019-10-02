<?php declare(strict_types=1);

namespace Stratadox\CardGame\Test;

use DateInterval;
use PHPUnit\Framework\Constraint\Constraint;
use PHPUnit\Framework\Constraint\LogicalOr;
use PHPUnit\Framework\Constraint\LogicalXor;
use PHPUnit\Framework\TestCase;
use Ramsey\Uuid\UuidFactory;
use function sprintf;
use Stratadox\CardGame\EventHandler\IllegalMoveNotifier;
use Stratadox\CardGame\EventHandler\ProposalProblemNotifier;
use Stratadox\CardGame\Infrastructure\Test\InMemoryDecks;
use Stratadox\CardGame\Infrastructure\Test\OneAtATimeBus;
use Stratadox\CardGame\EventBag;
use Stratadox\CardGame\EventHandler\AccountOverviewCreator;
use Stratadox\CardGame\EventHandler\BattlefieldUpdater;
use Stratadox\CardGame\EventHandler\HandAdjuster;
use Stratadox\CardGame\EventHandler\MatchPublisher;
use Stratadox\CardGame\EventHandler\PlayerListAppender;
use Stratadox\CardGame\EventHandler\ProposalAcceptanceNotifier;
use Stratadox\CardGame\EventHandler\ProposalSender;
use Stratadox\CardGame\EventHandler\StatisticsUpdater;
use Stratadox\CardGame\Infrastructure\DomainEvents\CommandToEventGlue;
use Stratadox\CardGame\Infrastructure\DomainEvents\Dispatcher;
use Stratadox\CardGame\Infrastructure\DomainEvents\EventCollector;
use Stratadox\CardGame\Infrastructure\IdentityManagement\DefaultMatchIdGenerator;
use Stratadox\CardGame\Infrastructure\IdentityManagement\DefaultPlayerIdGenerator;
use Stratadox\CardGame\Infrastructure\Test\InMemoryMatches;
use Stratadox\CardGame\Infrastructure\Test\InMemoryProposedMatches;
use Stratadox\CardGame\Infrastructure\Test\InMemoryRedirectSources;
use Stratadox\CardGame\Infrastructure\IdentityManagement\DefaultAccountIdGenerator;
use Stratadox\CardGame\Infrastructure\IdentityManagement\DefaultProposalIdGenerator;
use Stratadox\CardGame\Infrastructure\Test\TestClock;
use Stratadox\CardGame\Match\AttackingProcess;
use Stratadox\CardGame\Match\AttackWithCard;
use Stratadox\CardGame\Match\CardPlayingProcess;
use Stratadox\CardGame\Match\CardWasDrawn;
use Stratadox\CardGame\Match\EndCardPlaying;
use Stratadox\CardGame\Match\EndPlayPhaseProcess;
use Stratadox\CardGame\Match\PlayerDidNotHaveTheMana;
use Stratadox\CardGame\Match\SpellVanishedToTheVoid;
use Stratadox\CardGame\Match\PlayTheCard;
use Stratadox\CardGame\Match\StartTheMatch;
use Stratadox\CardGame\Match\MatchHasBegun;
use Stratadox\CardGame\Match\StartedMatchForProposal;
use Stratadox\CardGame\Match\MatchStartingProcess;
use Stratadox\CardGame\Account\VisitorOpenedAnAccount;
use Stratadox\CardGame\Account\AccountOpeningProcess;
use Stratadox\CardGame\Account\OpenAnAccount;
use Stratadox\CardGame\Match\TriedPlayingCardOutOfTurn;
use Stratadox\CardGame\Match\TriedStartingMatchForPendingProposal;
use Stratadox\CardGame\Match\UnitMovedIntoPlay;
use Stratadox\CardGame\Match\UnitMovedToAttack;
use Stratadox\CardGame\Proposal\AcceptTheProposal;
use Stratadox\CardGame\Proposal\MatchPropositionProcess;
use Stratadox\CardGame\Proposal\MatchWasProposed;
use Stratadox\CardGame\Proposal\ProposalAcceptationProcess;
use Stratadox\CardGame\Proposal\ProposalWasAccepted;
use Stratadox\CardGame\Proposal\ProposeMatch;
use Stratadox\CardGame\ReadModel\Account\AccountOverviews;
use Stratadox\CardGame\ReadModel\IllegalMoveStream;
use Stratadox\CardGame\ReadModel\Match\AllCards;
use Stratadox\CardGame\ReadModel\Match\Battlefield;
use Stratadox\CardGame\ReadModel\Match\Card;
use Stratadox\CardGame\ReadModel\Match\CardsInHand;
use Stratadox\CardGame\ReadModel\Match\OngoingMatch;
use Stratadox\CardGame\ReadModel\Match\OngoingMatches;
use Stratadox\CardGame\ReadModel\PlayerList;
use Stratadox\CardGame\Infrastructure\Test\InMemoryPlayerBase;
use Stratadox\CardGame\Infrastructure\Test\InMemoryVisitorRepository;
use Stratadox\CardGame\Account\AccountId;
use Stratadox\CardGame\ReadModel\PageVisitsStatisticsReport;
use Stratadox\CardGame\ReadModel\Proposal\AcceptedProposals;
use Stratadox\CardGame\ReadModel\Proposal\MatchProposals;
use Stratadox\CardGame\ReadModel\ProposalProblemStream;
use Stratadox\CardGame\Visiting\BroughtVisitor;
use Stratadox\CardGame\Visiting\VisitedPage;
use Stratadox\CardGame\Visiting\VisitationProcess;
use Stratadox\CardGame\Visiting\Visit;
use Stratadox\CardGame\Visiting\VisitorId;
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

    /** @var Card[] */
    protected $testCard = [];

    /** @var OngoingMatches */
    protected $ongoingMatches;

    /** @var null|OngoingMatch */
    protected $match;

    /** @var Battlefield */
    protected $battlefield;

    /** @var IllegalMoveStream */
    protected $illegalMove;

    /** @var ProposalProblemStream */
    protected $proposalProblems;

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
        $this->illegalMove = new IllegalMoveStream();
        $this->proposalProblems = new ProposalProblemStream();
        $this->testCard = [
            new Card('card-id-1'),
            new Card('card-id-2'),
            new Card('card-id-3'),
            new Card('card-id-4'),
            new Card('card-id-5'),
            new Card('card-id-6'),
            new Card('card-id-7'),
            new Card('card-id-8'),
            new Card('card-id-9'),
            new Card('card-id-10'),
        ];

        // @todo: extract TestConfiguration
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
        $matchPublisher = new MatchPublisher($this->ongoingMatches);
        $allCards = new AllCards(...$this->testCard);
        $handAdjuster = new HandAdjuster($this->cardsInTheHand, $allCards);
        $battlefieldUpdater = new BattlefieldUpdater($this->battlefield, $allCards);
        $illegalMoveNotifier = new IllegalMoveNotifier($this->illegalMove);
        $proposalProblemNotifier = new ProposalProblemNotifier($this->proposalProblems);
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
            StartedMatchForProposal::class => [
                $matchPublisher,
            ],
            TriedStartingMatchForPendingProposal::class => $proposalProblemNotifier,
            CardWasDrawn::class => $handAdjuster,
            MatchHasBegun::class => $matchPublisher,
            SpellVanishedToTheVoid::class => $handAdjuster,
            UnitMovedIntoPlay::class => [
                $battlefieldUpdater,
                $handAdjuster,
            ],
            UnitMovedToAttack::class => $battlefieldUpdater,
            PlayerDidNotHaveTheMana::class => $illegalMoveNotifier,
            TriedPlayingCardOutOfTurn::class => $illegalMoveNotifier,
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
        $matches = new InMemoryMatches();
        $decks = new InMemoryDecks();
        $uuidFactory = new UuidFactory();
        return new OneAtATimeBus(CommandBus::handling([
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
            StartTheMatch::class => new MatchStartingProcess(
                $proposals,
                new DefaultMatchIdGenerator($uuidFactory),
                new DefaultPlayerIdGenerator($uuidFactory),
                $matches,
                $decks,
                $this->clock,
                $eventBag
            ),
            PlayTheCard::class => new CardPlayingProcess(
                $matches,
                $this->clock,
                $eventBag
            ),
            EndCardPlaying::class => new EndPlayPhaseProcess(
                $matches,
                $eventBag
            ),
            AttackWithCard::class => new AttackingProcess(
                $matches,
                $this->clock,
                $eventBag
            ),
        ]));
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
            $this->matchProposals->for($playerTwo)[0]->id(),
            $playerTwo
        ));
    }

    protected function setUpNewMatch(
        string $id1 = 'id-1',
        string $id2 = 'id-2'
    ): void {
        $visitor1 = VisitorId::from($id1);
        $visitor2 = VisitorId::from($id2);

        $this->signUpForTheGame($visitor1);
        $this->signUpForTheGame($visitor2);

        $this->prepareMatchBetween(
            $this->accountOverviews->forVisitor($visitor1)->id(),
            $this->accountOverviews->forVisitor($visitor2)->id()
        );

        $proposal = $this->acceptedProposals->since($this->clock->now())[0];

        $this->handle(StartTheMatch::forProposal($proposal->id()));

        $this->match = $this->ongoingMatches->forProposal($proposal->id());
    }

    protected function interval(int $seconds): DateInterval
    {
        return new DateInterval(sprintf(
            'PT%dS',
            $seconds
        ));
    }
}
