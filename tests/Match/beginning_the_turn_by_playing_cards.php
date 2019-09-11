<?php declare(strict_types=1);

namespace Stratadox\CardGame\Test\Match;

use function assert;
use Stratadox\CardGame\Match\Command\PlayTheCard;
use Stratadox\CardGame\ReadModel\Match\OngoingMatch;
use Stratadox\CardGame\Test\CardGameTest;

/**
 * @testdox beginning the turn by playing cards
 */
class beginning_the_turn_by_playing_cards extends CardGameTest
{
    private $currentPlayer;
    private $otherPlayer;

    protected function setUp(): void
    {
        parent::setUp();
        $this->setUpNewMatch();
        assert($this->currentMatch instanceof OngoingMatch);
        foreach ($this->currentMatch->players() as $thePlayer) {
            if ($this->currentMatch->itIsTheTurnOf($thePlayer)) {
                $this->currentPlayer = $thePlayer;
            } else {
                $this->otherPlayer = $thePlayer;
            }
        }
    }

    /** @test */
    function starting_with_no_cards_on_the_battlefield()
    {
        $this->assertEmpty($this->battlefield->cardsInPlay());
    }

    /** @test */
    function playing_the_first_card()
    {
        $this->handle(PlayTheCard::number(0, $this->currentPlayer));

        $this->assertCount(1, $this->battlefield->cardsInPlay());
        $this->assertCount(6, $this->cardsInTheHand->of($this->currentPlayer));
    }

    /** @test */
    function playing_two_cards()
    {
        // we can play "the first card" twice, because after playing the first
        // first card, another card will be the first.

        $this->handle(PlayTheCard::number(0, $this->currentPlayer));
        $this->handle(PlayTheCard::number(0, $this->currentPlayer));

        $this->assertCount(2, $this->battlefield->cardsInPlay());
        $this->assertCount(5, $this->cardsInTheHand->of($this->currentPlayer));
    }

    /** @test */
    function not_playing_cards_after_mana_ran_out()
    {
        // if we had enough mana, we'd be playing 3 cards now... but our test
        // setup only gives us enough basic income to play the first two cards
        // of our hand, or to only play the third card, but not three cards.

        $this->handle(PlayTheCard::number(0, $this->currentPlayer));
        $this->handle(PlayTheCard::number(0, $this->currentPlayer));
        $this->handle(PlayTheCard::number(0, $this->currentPlayer));

        $this->assertCount(2, $this->battlefield->cardsInPlay());
        $this->assertCount(5, $this->cardsInTheHand->of($this->currentPlayer));
        // @todo assert error stream output
    }

    /** @test */
    function no_cards_on_the_board_when_playing_a_spell()
    {
        // the third card in the sample deck is a spell

        $this->handle(PlayTheCard::number(2, $this->currentPlayer));

        $this->assertCount(0, $this->battlefield->cardsInPlay());
        $this->assertCount(6, $this->cardsInTheHand->of($this->currentPlayer));
    }

    // @todo cannot play cards after ending the card playing phase
    // @todo cannot play cards after the card playing phase expired
}
