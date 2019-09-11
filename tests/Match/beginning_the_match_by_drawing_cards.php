<?php declare(strict_types=1);

namespace Stratadox\CardGame\Test\Match;

use function assert;
use function end as newest_of_the;
use PHPUnit\Framework\Constraint\IsEqual;
use Stratadox\CardGame\Match\CardId;
use Stratadox\CardGame\Match\StartTheMatch;
use Stratadox\CardGame\Proposal\ProposeMatch;
use Stratadox\CardGame\Proposal\ProposalId;
use Stratadox\CardGame\ReadModel\Match\NoSuchMatch;
use Stratadox\CardGame\ReadModel\Match\Card;
use Stratadox\CardGame\ReadModel\Proposal\MatchProposal;
use Stratadox\CardGame\Test\CardGameTest;
use Stratadox\CardGame\Visiting\VisitorId;

/**
 * @testdox beginning the match by drawing cards
 */
class beginning_the_match_by_drawing_cards extends CardGameTest
{
    /** @var ProposalId */
    private $proposal;

    /** @var Card */
    private $bogusCard;

    protected function setUp(): void
    {
        parent::setUp();

        $visitor1 = VisitorId::from('id-1');
        $visitor2 = VisitorId::from('id-2');

        $this->signUpForTheGame($visitor1);
        $this->signUpForTheGame($visitor2);

        $accountOne = $this->accountOverviews->forVisitor($visitor1)->id();
        $accountTwo = $this->accountOverviews->forVisitor($visitor2)->id();

        $this->prepareMatchBetween($accountOne, $accountTwo);

        $this->proposal = $this->acceptedProposals->since($this->clock->now())[0]->id();

        $this->bogusCard = new Card(CardId::from('bogus'), 'bogus', 0);
    }

    /** @test */
    function no_matches_for_non_existing_proposals()
    {
        $this->expectException(NoSuchMatch::class);

        $this->ongoingMatches->forProposal(ProposalId::from('non-existing-id'));
    }

    /** @test */
    function drawing_the_initial_hands_when_the_match_starts()
    {
        $this->handle(StartTheMatch::forProposal($this->proposal));

        $match = $this->ongoingMatches->forProposal($this->proposal);
        [$playerOne, $playerTwo] = $match->players();

        $this->assertCount(7, $this->cardsInTheHand->of($playerOne));
        $this->assertCount(7, $this->cardsInTheHand->of($playerTwo));

        // we're cheating here, because we haven't truly shuffled their decks
        foreach ($this->cardsInTheHand->of($playerOne) as $i => $theCardInHand) {
            $this->assertEquals($this->testCard[$i], $theCardInHand);
            $this->assertNotEquals($this->bogusCard, $theCardInHand);
        }

        foreach ($this->cardsInTheHand->of($playerTwo) as $i => $theCardInHand) {
            $this->assertEquals($this->testCard[$i], $theCardInHand);
            $this->assertNotEquals($this->bogusCard, $theCardInHand);
        }
    }

    /** @test */
    function not_starting_matches_for_proposals_that_are_still_pending()
    {
        $accountOne = $this->accountOverviews->forVisitor(VisitorId::from('id-1'))->id();
        $accountTwo = $this->accountOverviews->forVisitor(VisitorId::from('id-2'))->id();
        $this->handle(ProposeMatch::between($accountOne, $accountTwo));
        $proposals = $this->matchProposals->for($accountTwo);
        $proposal = newest_of_the($proposals);
        assert($proposal instanceof MatchProposal);
        $this->handle(StartTheMatch::forProposal($proposal->id()));

        $this->expectException(NoSuchMatch::class);

        $this->ongoingMatches->forProposal($this->proposal);
    }

    /** @test */
    function one_of_the_players_has_the_first_turn()
    {
        $this->handle(StartTheMatch::forProposal($this->proposal));

        $match = $this->ongoingMatches->forProposal($this->proposal);
        [$playerOne, $playerTwo] = $match->players();

        $this->assertEitherButNotBoth(
            true,
            new IsEqual($match->itIsTheTurnOf($playerOne)),
            new IsEqual($match->itIsTheTurnOf($playerTwo))
        );
    }
}
