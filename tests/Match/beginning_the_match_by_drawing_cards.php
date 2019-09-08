<?php declare(strict_types=1);

namespace Stratadox\CardGame\Test\Match;

use PHPUnit\Framework\Constraint\IsEqual;
use Stratadox\CardGame\Match\Command\StartTheMatch;
use Stratadox\CardGame\ProposalId;
use Stratadox\CardGame\ReadModel\Match\UnitCard;
use Stratadox\CardGame\Test\CardGameTest;
use Stratadox\CardGame\VisitorId;

/**
 * @testdox beginning the match by drawing cards
 */
class beginning_the_match_by_drawing_cards extends CardGameTest
{
    /** @var ProposalId */
    private $proposal;

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
            $this->assertTrue(
                $this->testCard[$i]->isTheSameAs($theCardInHand)
            );
            $this->assertFalse(
                (new UnitCard('foo', 0))->isTheSameAs($theCardInHand)
            );
        }

        foreach ($this->cardsInTheHand->of($playerTwo) as $i => $theCardInHand) {
            $this->assertTrue(
                $this->testCard[$i]->isTheSameAs($theCardInHand)
            );
            $this->assertFalse(
                (new UnitCard('foo', 0))->isTheSameAs($theCardInHand)
            );
        }
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
