<?php declare(strict_types=1);

namespace Stratadox\CardGame\Context;

use Behat\Behat\Context\Context;
use Behat\Behat\Tester\Exception\PendingException;
use Behat\Gherkin\Node\TableNode;
use DateInterval;
use Stratadox\CardGame\Infrastructure\Test\TestClient;
use Stratadox\CardGame\Infrastructure\Test\TestClock;
use function assert;
use function count;
use function in_array;
use function ucfirst;

final class RestContext implements Context
{
    private const DEFAULT_MATCH_LABEL = 'A';

    /** @var TestClock */
    private $clock;
    /** @var TestClient */
    private $go;
    /** @var TestClient[] */
    private $let = [];
    /** @var string[] */
    private $playersIn = [];

    /**
     * @BeforeScenario
     */
    public function setUp(): void
    {
        $this->clock = TestClock::make();
        $this->go = $this->newClient();
    }

    private function newClient(): TestClient
    {
        return new TestClient($this->clock);
    }

    /**
     * @Given mana can run out
     * @Given the :specific card in the deck is a spell
     * @When no proposals are made
     * @When nobody plays any cards
     * @When :someone does not select any units for the attack
     * @When :someone does not accept the proposal
     * @When :someone does not select any units for defending
     */
    public function nothingToSeeHere()
    {
    }

    /**
     * @Then that is not possible, because :reason
     */
    public function thatIsNotPossibleBecause(string $reason)
    {
        $found = in_array(ucfirst($reason), $this->go->flashMessages());
        foreach ($this->let as $client) {
            if (!$found) {
                $found = in_array(ucfirst($reason), $client->flashMessages());
            }
        }
        assert($found);
    }

    /**
     * @Given I visited the :which page
     * @When I visit the :which page
     */
    public function iVisitedThePage(string $which)
    {
        $this->go->visit($which);
    }

    /**
     * @When I open an account
     */
    public function iOpenAnAccount()
    {
        $this->go->do('open account');
    }

    /**
     * @Given :player has signed up for the game
     */
    public function hasSignedUpForTheGame(string $player)
    {
        if (!isset($this->let[$player])) {
            $this->let[$player] = $this->newClient();
        }
        $this->let[$player]->visit('home');
        $this->let[$player]->do('open account');
    }

    /**
     * @Then my account will be a guest account
     */
    public function myAccountWillBeAGuestAccount()
    {
        $this->go->visit('account');
        assert($this->go->see('type') === 'guest');
    }

    /**
     * @Then the player list will be empty
     */
    public function thePlayerListWillBeEmpty()
    {
        $this->go->visit('player list');
        assert(empty($this->go->see('players')));
    }

    /**
     * @Then the player list will not be empty
     */
    public function thePlayerListWillNotBeEmpty()
    {
        $this->go->visit('player list');
        assert(!empty($this->go->see('players')));
    }

    /**
     * @Then :player will have :amount open match proposal
     * @Then :player will have :amount open match proposals
     */
    public function willHaveThisManyOpenMatchProposals(string $player, int $amount)
    {
        assert($amount === count($this->let[$player]->see('open proposals')));
    }

    /**
     * @Then :player will have :amount of their proposals accepted
     */
    public function willHaveProposalsAccepted(string $player, int $amount)
    {
        assert($amount === count($this->let[$player]->see('proposals accepted')));
    }

    /**
     * @Then :player will have accepted :amount proposals
     * @Then :player will have accepted :amount proposal
     */
    public function willHaveAcceptedProposals(string $player, int $amount)
    {
        assert($amount === count($this->let[$player]->see('accepted proposals')));
    }

    /**
     * @Given the proposal has expired
     * @When the proposal expires
     */
    public function theProposalExpires()
    {
        $this->clock->fastForward(new DateInterval('PT1H'));
    }

    /**
     * @Given the proposal has almost expired
     */
    public function theProposalHasAlmostExpired()
    {
        $this->clock->fastForward(new DateInterval('PT30S'));
    }

    /**
     * @Given :proposer proposed a match to :receiver
     * @When :proposer proposes a match to :receiver
     */
    public function proposesAMatch(string $proposer, string $receiver)
    {
        $this->let[$proposer]->do('propose', [
            'to' => $this->let[$receiver]->see('account id')
        ]);
    }

    /**
     * @When :player accepts the proposal
     * @Given :player accepted the proposal
     */
    public function acceptsTheProposal(string $player)
    {
        $this->let[$player]->do('accept', [
            'proposal' => $this->let[$player]->see('open proposals')[0]
        ]);
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
        } while (!$this->let[$player1]->see('my turn'));
        $this->playersIn[$match] = [$player1, $player2];
    }

    /**
     * @When the match starts
     */
    public function theMatchStarts()
    {
        throw new PendingException();
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
        throw new PendingException();
    }

    /**
     * @Then there will be :amount ongoing match
     * @Then there will be :amount ongoing matches
     */
    public function thereWillBeThisManyOngoingMatches(int $amount)
    {
        throw new PendingException();
    }

    /**
     * @Then :player will have the following cards in their hand:
     */
    public function playerWillHaveTheseCards(string $player, TableNode $cards)
    {
        throw new PendingException();
    }

    /**
     * @Then either :player1 or :player2 will get the first turn
     */
    public function eitherWillGetTheFirstTurn(string $player1, string $player2)
    {
        throw new PendingException();
    }

    /**
     * @Then :player will be in the :which phase
     * @Then :player will still be in the :which phase
     */
    public function playerWillBeInThisPhase(string $player, string $which)
    {
        throw new PendingException();
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
        throw new PendingException();
    }

    /**
     * @Then :player will have :amount cards left in their hand
     */
    public function willHaveCardsLeftInTheirHand(string $player, int $amount)
    {
        throw new PendingException();
    }

    /**
     * @Given the :which phase expired
     * @When the :which phase expires
     */
    public function thePhaseExpired(string $which)
    {
        throw new PendingException();
    }

    /**
     * @Given :player attacked with the :chosen unit in their army
     * @When :player attacks with the :chosen unit in their army
     */
    public function attacksWithTheUnitInTheirArmy(string $player, string $chosen)
    {
        throw new PendingException();
    }

    /**
     * @Then there will be :amount attacker on the battlefield
     * @Then there will be :amount attackers on the battlefield
     */
    public function thereWillBeThisManyAttackersOnTheBattlefield(int $amount)
    {
        throw new PendingException();
    }

    /**
     * @Given :player ended the turn
     * @Given :player ended their turn
     * @When :player ends the turn
     */
    public function endsTheTurn(string $player)
    {
        throw new PendingException();
    }

    /**
     * @Given :player ended the :current phase
     * @When :player ends the :current phase
     */
    public function endThePhase(string $player, string $current)
    {
        throw new PendingException();
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
        throw new PendingException();
    }

    /**
     * @Then there will be :amount defenders on the battlefield
     * @Then there will be :amount defender on the battlefield
     */
    public function thereWillBeThisManyDefendersOnTheBattlefield(int $amount)
    {
        throw new PendingException();
    }

    /**
     * @Then :player will have :amount units
     * @Then :player will have :amount unit
     */
    public function willHaveThisManyUnits(string $player, int $amount)
    {
        throw new PendingException();
    }

    /**
     * @When :player slowly plays a card and attacks
     * @Given :player slowly played a card and attacked
     */
    public function slowlyPlaysACardAndAttacks(string $player)
    {
        throw new PendingException();
    }
}
