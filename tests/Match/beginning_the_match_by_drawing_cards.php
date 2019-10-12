<?php declare(strict_types=1);

namespace Stratadox\CardGame\Test\Match;

use function assert;
use function end as newest_of_the;
use Stratadox\CardGame\Match\Command\StartTheMatch;
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

        $this->bogusCard = new Card('bogus');
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
        $this->handle(StartTheMatch::forProposal($this->proposal, $this->id));

        $match = $this->ongoingMatches->forProposal($this->proposal);

        $this->assertCount(7, $this->cardsInTheHand->ofPlayer(0, $match->id()));
        $this->assertCount(7, $this->cardsInTheHand->ofPlayer(1, $match->id()));

        // we're cheating here, because we haven't truly shuffled their decks
        foreach ($this->cardsInTheHand->ofPlayer(0, $match->id()) as $i => $theCardInHand) {
            $this->assertEquals($this->testCard[$i], $theCardInHand);
            $this->assertNotEquals($this->bogusCard, $theCardInHand);
        }

        foreach ($this->cardsInTheHand->ofPlayer(1, $match->id()) as $i => $theCardInHand) {
            $this->assertEquals($this->testCard[$i], $theCardInHand);
            $this->assertNotEquals($this->bogusCard, $theCardInHand);
        }
    }

    /** @test */
    function not_starting_matches_for_proposals_that_are_still_pending()
    {
        $accountOne = $this->accountOverviews->forVisitor(VisitorId::from('id-1'))->id();
        $accountTwo = $this->accountOverviews->forVisitor(VisitorId::from('id-2'))->id();
        $this->handle(ProposeMatch::between($accountOne, $accountTwo, $this->id));
        $proposals = $this->matchProposals->for($accountTwo);
        $proposal = newest_of_the($proposals);
        assert($proposal instanceof MatchProposal);
        $this->handle(StartTheMatch::forProposal($proposal->id(), $this->id));

        $this->assertEquals(
            ['The proposal is still pending!'],
            $this->refusals->for($this->id)
        );

        $this->expectException(NoSuchMatch::class);
        $this->ongoingMatches->forProposal($this->proposal);
    }

    /** @test */
    function one_of_the_players_has_the_first_turn()
    {
        $this->handle(StartTheMatch::forProposal($this->proposal, $this->id));

        $match = $this->ongoingMatches->forProposal($this->proposal);
        [$playerOne, $playerTwo] = $match->players();

        $this->assertTrue(
            $match->itIsTheTurnOf($playerOne) xor
            $match->itIsTheTurnOf($playerTwo)
        );
    }
}
