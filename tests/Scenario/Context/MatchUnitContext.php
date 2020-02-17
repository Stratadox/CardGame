<?php declare(strict_types=1);

namespace Stratadox\CardGame\Context;

use Behat\Behat\Context\Context;
use Behat\Gherkin\Node\TableNode;
use DateInterval;
use RuntimeException;
use Stratadox\CardGame\Command;
use Stratadox\CardGame\Context\Step\ProposalAccepting;
use Stratadox\CardGame\Context\Step\RefusalVerification;
use Stratadox\CardGame\Context\Step\SigningUp;
use Stratadox\CardGame\Context\Support\Accounts;
use Stratadox\CardGame\Context\Support\Correlation;
use Stratadox\CardGame\Context\Support\ProposalTracking;
use Stratadox\CardGame\Context\Support\VisitorIdentification;
use Stratadox\CardGame\EventHandler\AccountOverviewCreator;
use Stratadox\CardGame\EventHandler\BattlefieldUpdater;
use Stratadox\CardGame\EventHandler\BringerOfBadNews;
use Stratadox\CardGame\EventHandler\HandAdjuster;
use Stratadox\CardGame\EventHandler\MatchPublisher;
use Stratadox\CardGame\EventHandler\ProposalAcceptanceNotifier;
use Stratadox\CardGame\EventHandler\ProposalSender;
use Stratadox\CardGame\EventHandler\TurnSwitcher;
use Stratadox\CardGame\Infrastructure\DomainEvents\Dispatcher;
use Stratadox\CardGame\Infrastructure\Test\TestClock;
use Stratadox\CardGame\Match\Command\AttackWithCard;
use Stratadox\CardGame\Match\Command\Block;
use Stratadox\CardGame\Match\Command\EndBlocking;
use Stratadox\CardGame\Match\Command\EndCardPlaying;
use Stratadox\CardGame\Match\Command\EndTheTurn;
use Stratadox\CardGame\Match\Command\PlayTheCard;
use Stratadox\CardGame\Match\Command\StartTheMatch;
use Stratadox\CardGame\ReadModel\Account\AccountOverviews;
use Stratadox\CardGame\ReadModel\Match\Battlefields;
use Stratadox\CardGame\ReadModel\Match\CardsInHand;
use Stratadox\CardGame\ReadModel\Match\CardTemplate;
use Stratadox\CardGame\ReadModel\Match\CardTemplates;
use Stratadox\CardGame\ReadModel\Match\OngoingMatch;
use Stratadox\CardGame\ReadModel\Match\OngoingMatches;
use Stratadox\CardGame\ReadModel\Proposal\MatchProposals;
use Stratadox\CardGame\ReadModel\Refusals;
use Stratadox\CardGame\Test\UnitTestConfiguration;
use Stratadox\CardGame\Visiting\VisitorId;
use Stratadox\CommandHandling\Handler;
use function array_flip;
use function assert;
use function count;
use function random_int;
use function sprintf;

final class MatchUnitContext implements Context
{
    use RefusalVerification, SigningUp, ProposalAccepting,
        VisitorIdentification, Correlation, Accounts, ProposalTracking;

    private const DEFAULT_MATCH_LABEL = 'A';

    /** @var VisitorId */
    private $visitor;
    /** @var Handler */
    private $input;
    /** @var Refusals */
    private $refusals;
    /** @var OngoingMatches */
    private $ongoingMatches;
    /** @var TestClock */
    private $clock;
    /** @var CardsInHand */
    private $cardsInTheHand;
    /** @var Battlefields */
    private $battlefield;
    /** @var OngoingMatch[] */
    private $matches = [];
    /** @var OngoingMatch|null */
    private $currentMatch;

    /**
     * @BeforeScenario
     */
    public function setUp(): void
    {
        $this->clock = TestClock::make();
        $this->visitor = VisitorId::from('some-visitor-id');
        $this->refusals = new Refusals();
        $this->accountOverviews = AccountOverviews::startEmpty();
        $this->matchProposals = new MatchProposals($this->clock);
        $this->ongoingMatches = new OngoingMatches();
        $this->cardsInTheHand = new CardsInHand();
        $this->battlefield = new Battlefields();
        $allCards = new CardTemplates(
            new CardTemplate('card-type-1'),
            new CardTemplate('card-type-2'),
            new CardTemplate('card-type-3'),
            new CardTemplate('card-type-4'),
            new CardTemplate('card-type-5'),
            new CardTemplate('card-type-6'),
            new CardTemplate('card-type-7'),
            new CardTemplate('card-type-3'), // 3 again !
            new CardTemplate('card-type-8'),
            new CardTemplate('card-type-9')
        );
        $this->input = UnitTestConfiguration::withClock($this->clock)->handler(
            new Dispatcher(
                new AccountOverviewCreator($this->accountOverviews),
                new ProposalSender($this->matchProposals),
                new ProposalAcceptanceNotifier($this->matchProposals),
                new MatchPublisher($this->ongoingMatches, $this->matchProposals),
                new HandAdjuster($this->cardsInTheHand, $allCards),
                new BattlefieldUpdater($this->battlefield, $allCards),
                new TurnSwitcher($this->ongoingMatches),
                new BringerOfBadNews($this->refusals)
            )
        );
    }

    /**
     * @Given we're not actually shuffling any decks
     */
    public function wereNotActuallyShufflingAnyDecks()
    {
        // @todo make a flag once we have tests that require actual shuffling
    }

    /**
     * @Given :player1 begins in their match against :player2
     * @Given :player1 begins in their match :match against :player2
     */
    public function beginsInTheirMatchAgainst(
        string $player1,
        string $player2,
        string $match = self::DEFAULT_MATCH_LABEL
    ) {
        do {
            $this->hasSignedUpForTheGame($player1);
            $this->hasSignedUpForTheGame($player2);
            $this->proposesAMatch($player1, $player2);
            $this->acceptsTheProposal($player2);
            $this->theMatchStarts();
            $ongoingMatch = $this->matchFor($player1);
        } while (!$ongoingMatch->itIsTheTurnOf($this->playerNumberOf($player1)));
        $this->matches[$match] = $ongoingMatch;
        $this->currentMatch = $ongoingMatch;
    }

    /**
     * @When the match starts
     */
    public function theMatchStarts()
    {
        $this->handle(StartTheMatch::forProposal(
            $this->newestProposal(),
            $this->correlation()
        ));
    }

    /**
     * @When nobody plays any cards
     * @When :someone does not select any units for the attack
     * @When :someone does not accept the proposal
     * @Given mana can run out
     * @Given the :specific card in the deck is a spell
     * @When :someone does not select any units for defending
     */
    public function nothingToSeeHere()
    {
    }

    /**
     * @Then there will be :amount units on the battlefield
     * @Then there will be :amount unit on the battlefield
     * @Then there will still be :amount units on the battlefield
     * @Then there will be :amount unit on the battlefield of match :match
     * @Then the battlefield will be empty
     * @Then the battlefield of match :match will be empty
     * @Then the battlefield of match :match will still be empty
     */
    public function thereWillBeThisManyUnitsOnTheBattlefield(
        int $amount = 0,
        string $match = self::DEFAULT_MATCH_LABEL
    ) {
        $cards = $this->battlefield->cardsInPlay($this->matches[$match]->id());
        assert(count($cards) === $amount);
    }

    /**
     * @Then there will be :amount ongoing match
     * @Then there will be :amount ongoing matches
     */
    public function thereWillBeThisManyOngoingMatches(int $amount)
    {
        assert(count($this->ongoingMatches) === $amount);
    }

    /**
     * @Then :player will have the following cards in their hand:
     */
    public function playerWillHaveTheseCards(string $player, TableNode $cards)
    {
        $actualCards = $this->cardsInTheHand->ofPlayer(
            $this->playerNumberOf($player),
            $this->matchFor($player)->id()
        );
        assert(count($cards->getHash()) === count($actualCards));
        foreach ($cards as $i => $card) {
            assert($actualCards[$i]->hasTemplate(new CardTemplate($card['Card'])));
        }
    }

    /**
     * @Then either :player1 or :player2 will get the first turn
     */
    public function eitherWillGetTheFirstTurn(string $player1, string $player2)
    {
        assert($this->matchFor($player1) === $this->matchFor($player2));
        assert(
            $this->matchFor($player1)->itIsTheTurnOf(
                $this->playerNumberOf($player1)
            ) XOR
            $this->matchFor($player2)->itIsTheTurnOf(
                $this->playerNumberOf($player2)
            )
        );
    }

    /**
     * @Then :player will be in the :which phase
     * @Then :player will still be in the :which phase
     */
    public function playerWillBeInThisPhase(string $player, string $which)
    {
        switch ($which) {
            case 'play': $phase = OngoingMatch::PHASE_PLAY; break;
            case 'attack': $phase = OngoingMatch::PHASE_ATTACK; break;
            case 'defend': $phase = OngoingMatch::PHASE_DEFEND; break;
            default: throw new RuntimeException("Unknown phase $which");
        }
        $match = $this->matchFor($player);
        assert($match->itIsTheTurnOf($this->playerNumberOf($player)));
        assert($match->phase() === $phase);
    }

    /**
     * @Given :player played the :chosen card in their hand
     * @Given :player played what was then the :chosen card in their hand
     * @Given :player already played the :chosen card in their hand
     * @Given :player also played what was afterwards the :chosen card in their hand
     * @When :player plays the :chosen card in their hand
     * @When :player plays the then :chosen card in their hand
     */
    public function playTheCardFromTheirHand(string $player, string $chosen)
    {
        $this->input->handle(
            PlayTheCard::number(
                $this->cardThatWas($chosen),
                $this->playerNumberOf($player),
                $this->matchFor($player)->id(),
                $this->correlation()
            )
        );
    }

    /**
     * @Then :player will have :amount cards left in their hand
     */
    public function willHaveCardsLeftInTheirHand(string $player, int $amount)
    {
        assert(count($this->cardsOf($player)) === $amount);
    }

    /**
     * @Given the :which phase expired
     * @When the :which phase expires
     */
    public function thePhaseExpired(string $which)
    {
        switch ($which) {
            case 'play': case 'defend': $time = 20; break;
            case 'attack': $time = 10; break;
            default: throw new RuntimeException('Unknown phase ' . $which);
        }
        $this->clock->fastForward(new DateInterval("PT{$time}S"));
    }

    /**
     * @Given :player attacked with the :chosen unit in their army
     * @When :player attacks with the :chosen unit in their army
     */
    public function attacksWithTheUnitInTheirArmy(string $player, string $chosen)
    {
        $this->input->handle(AttackWithCard::number(
            $this->cardThatWas($chosen),
            $this->playerNumberOf($player),
            $this->matchFor($player)->id(),
            $this->correlation()
        ));
    }

    /**
     * @Then there will be :amount attacker on the battlefield
     * @Then there will be :amount attackers on the battlefield
     */
    public function thereWillBeThisManyAttackersOnTheBattlefield(int $amount)
    {
        assert($this->currentMatch !== null);
        $actual = count(
            $this->battlefield->attackers($this->currentMatch->id())
        );
        assert(
            $amount === $actual,
            "There should be $amount attackers, found $actual instead."
        );
    }

    /**
     * @Given :player ended the turn
     * @Given :player ended their turn
     * @When :player ends the turn
     */
    public function endsTheTurn(string $player)
    {
        $this->input->handle(EndTheTurn::for(
            $this->matchFor($player)->id(),
            $this->playerNumberOf($player),
            $this->correlation()
        ));
    }

    /**
     * @Given :player ended the :current phase
     * @When :player ends the :current phase
     */
    public function endThePhase(string $player, string $current)
    {
        $match = $this->matchFor($player);
        $playerNumber = $this->playerNumberOf($player);
        switch ($current) {
            case 'play':
            case 'playing':
                $this->input->handle(EndCardPlaying::phase(
                    $playerNumber,
                    $match->id(),
                    $this->correlation()
                ));
                break;
            case 'defend':
            case 'defending':
                $this->input->handle(EndBlocking::phase(
                    $match->id(),
                    $playerNumber,
                    $this->correlation()
                ));
            break;
            default:
                throw new RuntimeException("Cannot end the $current phase");
        }
    }

    /**
     * @When :player uses their :defending unit to block the :attacking attacker
     * @Given :player used their :defending unit to block the :attacking attacker
     */
    public function usesTheirUnitToBlockTheAttacker(
        string $player,
        string $defending,
        string $attacking
    ) {
        $this->input->handle(
            Block::theAttack()
                ->ofAttacker($this->cardThatWas($attacking))
                ->withDefender($this->cardThatWas($defending))
                ->as($this->playerNumberOf($player))
                ->in($this->currentMatch->id())
                ->trackedWith($this->correlation())
                ->go()
        );
    }

    /**
     * @Then there will be :amount defenders on the battlefield
     * @Then there will be :amount defender on the battlefield
     */
    public function thereWillBeThisManyDefendersOnTheBattlefield(int $amount)
    {
        $actual = count($this->battlefield->defenders($this->currentMatch->id()));
        assert($actual === $amount, "Expected $amount defenders, got $actual.");
    }

    /**
     * @Then :player will have :amount units
     * @Then :player will have :amount unit
     */
    public function willHaveThisManyUnits(string $player, int $amount)
    {
        $actual = count($this->battlefield->cardsInPlayFor(
            $this->playerNumberOf($player),
            $this->matchFor($player)->id()
        ));
        assert($actual === $amount, "Expected $amount units for $player, got $actual.");
    }

    /**
     * @When :player slowly plays a card and attacks
     * @Given :player slowly played a card and attacked
     */
    public function slowlyPlaysACardAndAttacks(string $player)
    {
        $this->clock->fastForward(
            new DateInterval(sprintf('PT%dS', random_int(1, 19)))
        );

        $this->playTheCardFromTheirHand($player, 'first');
        $this->endThePhase($player, 'play');

        $this->clock->fastForward(
            new DateInterval(sprintf('PT%dS', random_int(1, 9)))
        );
        $this->attacksWithTheUnitInTheirArmy($player, 'first');
    }

    protected function refusals(): array
    {
        return $this->refusals->for($this->correlation());
    }

    protected function handle(Command $command): void
    {
        $this->input->handle($command);
    }

    private function playerNumberOf(string $player): int
    {
        if (isset($this->latestProposalBy[$player])) {
            return 0;
        }
        if (isset($this->latestProposalFor[$player])) {
            return 1;
        }
        throw new RuntimeException("$player does not seem to play any matches");
    }

    private function matchFor(string $player): OngoingMatch
    {
        if ($proposal = ($this->latestProposalBy[$player] ?? null)) {
            return $this->ongoingMatches->forProposal($proposal->id());
        }
        if ($proposal = ($this->latestProposalFor[$player] ?? null)) {
            return $this->ongoingMatches->forProposal($proposal->id());
        }
        throw new RuntimeException("$player does not seem to play any matches");
    }

    private function cardsOf(string $player): array
    {
        return $this->cardsInTheHand->ofPlayer(
            $this->playerNumberOf($player),
            $this->matchFor($player)->id()
        );
    }

    private function cardThatWas(string $chosen): int
    {
        return array_flip([
            'first',
            'second',
            'third',
            'fourth',
            'fifth',
            'sixth',
            'seventh',
        ])[$chosen];
    }
}
