<?php declare(strict_types=1);

namespace Stratadox\CardGame\Test;

use DateInterval;
use PHPUnit\Framework\TestCase;
use Stratadox\CardGame\Account\AccountId;
use Stratadox\CardGame\Account\OpenAnAccount;
use Stratadox\CardGame\CorrelationId;
use Stratadox\CardGame\EventHandler\AccountOverviewCreator;
use Stratadox\CardGame\EventHandler\BattlefieldUpdater;
use Stratadox\CardGame\EventHandler\BringerOfBadNews;
use Stratadox\CardGame\EventHandler\HandAdjuster;
use Stratadox\CardGame\EventHandler\MatchPublisher;
use Stratadox\CardGame\EventHandler\PlayerListAppender;
use Stratadox\CardGame\EventHandler\ProposalAcceptanceNotifier;
use Stratadox\CardGame\EventHandler\ProposalSender;
use Stratadox\CardGame\EventHandler\StatisticsUpdater;
use Stratadox\CardGame\EventHandler\TurnSwitcher;
use Stratadox\CardGame\Infrastructure\DomainEvents\Dispatcher;
use Stratadox\CardGame\Infrastructure\DomainEvents\EventCollector;
use Stratadox\CardGame\Infrastructure\Test\TestClock;
use Stratadox\CardGame\Match\Command\StartTheMatch;
use Stratadox\CardGame\Proposal\AcceptTheProposal;
use Stratadox\CardGame\Proposal\ProposeMatch;
use Stratadox\CardGame\ReadModel\Account\AccountOverviews;
use Stratadox\CardGame\ReadModel\Match\AllCards;
use Stratadox\CardGame\ReadModel\Match\Battlefield;
use Stratadox\CardGame\ReadModel\Match\Card;
use Stratadox\CardGame\ReadModel\Match\CardsInHand;
use Stratadox\CardGame\ReadModel\Match\OngoingMatch;
use Stratadox\CardGame\ReadModel\Match\OngoingMatches;
use Stratadox\CardGame\ReadModel\PageVisitsStatisticsReport;
use Stratadox\CardGame\ReadModel\PlayerList;
use Stratadox\CardGame\ReadModel\Proposal\AcceptedProposals;
use Stratadox\CardGame\ReadModel\Proposal\MatchProposals;
use Stratadox\CardGame\ReadModel\Refusals;
use Stratadox\CardGame\Visiting\Visit;
use Stratadox\CardGame\Visiting\VisitorId;
use Stratadox\Clock\RewindableClock;
use Stratadox\CommandHandling\Handler;
use function sprintf;

abstract class CardGameTest extends TestCase
{
    /** @var Handler */
    private $input;

    /** @var Configuration[] */
    private $configuration = [];

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

    /** @var Refusals */
    protected $refusals;

    /** @var CorrelationId */
    protected $id;

    /** @var int|null */
    protected $currentPlayer;

    /** @var int|null */
    protected $otherPlayer;

    protected function setUp(): void
    {
        $this->clock = TestClock::make();

        // Normally an identifier is generated for each request, allowing the
        // client to track what happened.
        // For brevity's sake, it's one id for all requests in the unit tests.
        $this->id = CorrelationId::from('foo');

        $this->statistics = new PageVisitsStatisticsReport();
        $this->matchProposals = new MatchProposals($this->clock);
        $this->acceptedProposals = new AcceptedProposals();
        $this->accountOverviews = new AccountOverviews();
        $this->playerList = PlayerList::startEmpty();
        $this->cardsInTheHand = new CardsInHand();
        $this->ongoingMatches = new OngoingMatches();
        $this->battlefield = new Battlefield();
        $this->refusals = new Refusals();
        $this->testCard = [
            new Card('card-type-1'),
            new Card('card-type-2'),
            new Card('card-type-3'),
            new Card('card-type-4'),
            new Card('card-type-5'),
            new Card('card-type-6'),
            new Card('card-type-7'),
            new Card('card-type-3'), // 3 again !
            new Card('card-type-8'),
            new Card('card-type-9'),
        ];

        $this->configuration['unit'] = new UnitTestConfiguration();
        // @todo add a test configuration where every command is delayed

        $eventBag = new EventCollector();
        $allCards = new AllCards(...$this->testCard);
        $this->input = $this->configuration[$_SERVER['configuration'] ?? 'unit']->commandHandler(
            $eventBag,
            $this->clock,
            new Dispatcher(
                    new MatchPublisher($this->ongoingMatches),
                    new HandAdjuster($this->cardsInTheHand, $allCards),
                    new BattlefieldUpdater($this->battlefield, $allCards),
                    new BringerOfBadNews($this->refusals),
                    new StatisticsUpdater($this->statistics),
                    new PlayerListAppender($this->playerList),
                    new PlayerListAppender($this->playerList),
                    new AccountOverviewCreator($this->accountOverviews),
                    new ProposalSender($this->matchProposals),
                    new ProposalAcceptanceNotifier($this->acceptedProposals),
                    new TurnSwitcher($this->ongoingMatches)
                )
        );
    }

    protected function handle(object $command): void
    {
        $this->input->handle($command);
    }

    protected function signUpForTheGame(VisitorId $visitorId): void
    {
        $this->handle(Visit::page('home', 'https://example.com', $visitorId, $this->id));
        $this->handle(OpenAnAccount::forVisitorWith($visitorId, $this->id));
    }

    protected function prepareMatchBetween(
        AccountId $playerOne,
        AccountId $playerTwo
    ): void {
        $this->handle(ProposeMatch::between($playerOne, $playerTwo, $this->id));
        $this->handle(AcceptTheProposal::withId(
            $this->matchProposals->for($playerTwo)[0]->id(),
            $playerTwo,
            $this->id
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

        $this->handle(StartTheMatch::forProposal($proposal->id(), $this->id));

        $this->match = $this->ongoingMatches->forProposal($proposal->id());
    }

    protected function determineStartingPlayer(): void
    {
        foreach ($this->match->players() as $player) {
            if ($this->match->itIsTheTurnOf($player)) {
                $this->currentPlayer = $player;
            } else {
                $this->otherPlayer = $player;
            }
        }
    }

    protected function interval(int $seconds): DateInterval
    {
        return new DateInterval(sprintf(
            'PT%dS',
            $seconds
        ));
    }
}
