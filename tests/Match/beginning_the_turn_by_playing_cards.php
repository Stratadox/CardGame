<?php declare(strict_types=1);

namespace Stratadox\CardGame\Test\Match;

use DateInterval;
use Stratadox\CardGame\Match\Command\EndCardPlaying;
use Stratadox\CardGame\Match\Command\PlayTheCard;
use Stratadox\CardGame\Test\CardGameTest;

/**
 * @testdox beginning the turn by playing cards
 */
class beginning_the_turn_by_playing_cards extends CardGameTest
{
    /** @var int */
    private $currentPlayer;
    /** @var int */
    private $otherPlayer;
    /** @var DateInterval */
    private $justOverTheCardPlayingTimeLimit;

    protected function setUp(): void
    {
        parent::setUp();
        $this->setUpNewMatch();
        foreach ($this->match->players() as $thePlayer) {
            if ($this->match->itIsTheTurnOf($thePlayer)) {
                $this->currentPlayer = $thePlayer;
            } else {
                $this->otherPlayer = $thePlayer;
            }
        }
        $this->justOverTheCardPlayingTimeLimit = $this->interval(20);
    }

    /** @test */
    function starting_with_no_cards_on_the_battlefield()
    {
        $this->assertEmpty($this->battlefield->cardsInPlay($this->match->id()));
    }

    /** @test */
    function playing_the_first_card()
    {
        $this->handle(PlayTheCard::number(0, $this->currentPlayer, $this->match->id(), $this->id));

        $this->assertCount(1, $this->battlefield->cardsInPlay($this->match->id()));
        $this->assertCount(6, $this->cardsInTheHand->ofPlayer($this->currentPlayer, $this->match->id()));
    }

    /** @test */
    function playing_two_cards()
    {
        // we can play "the first card" twice, because after playing the first
        // first card, another card will be the first.

        $this->handle(PlayTheCard::number(0, $this->currentPlayer, $this->match->id(), $this->id));
        $this->handle(PlayTheCard::number(0, $this->currentPlayer, $this->match->id(), $this->id));

        $this->assertCount(2, $this->battlefield->cardsInPlay($this->match->id()));
        $this->assertCount(5, $this->cardsInTheHand->ofPlayer($this->currentPlayer, $this->match->id()));
    }

    /** @test */
    function not_playing_cards_after_mana_ran_out()
    {
        // if we had enough mana, we'd be playing 3 cards now... but our test
        // setup only gives us enough basic income to play the first two cards
        // of our hand, or to only play the third card, but not three cards.

        $this->handle(PlayTheCard::number(0, $this->currentPlayer, $this->match->id(), $this->id));
        $this->handle(PlayTheCard::number(0, $this->currentPlayer, $this->match->id(), $this->id));
        $this->handle(PlayTheCard::number(0, $this->currentPlayer, $this->match->id(), $this->id));

        $this->assertCount(2, $this->battlefield->cardsInPlay($this->match->id()));
        $this->assertCount(5, $this->cardsInTheHand->ofPlayer($this->currentPlayer, $this->match->id()));

        $this->assertEquals(
            ['Not enough mana!'],
            $this->refusals->for($this->id)
        );
    }

    /** @test */
    function no_cards_on_the_board_when_playing_a_spell()
    {
        // the third card in the sample deck is a spell

        $this->handle(PlayTheCard::number(2, $this->currentPlayer, $this->match->id(), $this->id));

        $this->assertCount(0, $this->battlefield->cardsInPlay($this->match->id()));
        $this->assertCount(6, $this->cardsInTheHand->ofPlayer($this->currentPlayer, $this->match->id()));
    }

    /** @test */
    function not_playing_cards_in_the_other_players_turn()
    {
        $this->handle(PlayTheCard::number(0, $this->otherPlayer, $this->match->id(), $this->id));

        $this->assertEmpty($this->battlefield->cardsInPlay($this->match->id()));

        $this->assertEquals(
            ['Cannot play cards right now'],
            $this->refusals->for($this->id)
        );
    }

    /** @test */
    function not_playing_cards_in_the_other_players_turn_twice()
    {
        $this->handle(PlayTheCard::number(0, $this->otherPlayer, $this->match->id(), $this->id));
        $this->handle(PlayTheCard::number(0, $this->otherPlayer, $this->match->id(), $this->id));

        $this->assertEmpty($this->battlefield->cardsInPlay($this->match->id()));

        $this->assertEquals(
            ['Cannot play cards right now', 'Cannot play cards right now'],
            $this->refusals->for($this->id)
        );
    }

    /** @test */
    function no_illegal_move_messages_when_all_moves_were_legal()
    {
        $this->handle(PlayTheCard::number(0, $this->currentPlayer, $this->match->id(), $this->id));

        $this->assertEmpty($this->refusals->for($this->id));
    }

    /** @test */
    function not_playing_cards_after_ending_the_card_playing_phase()
    {
        $this->handle(EndCardPlaying::phase($this->currentPlayer, $this->match->id()));

        $this->handle(PlayTheCard::number(0, $this->currentPlayer, $this->match->id(), $this->id));

        $this->assertCount(0, $this->battlefield->cardsInPlay($this->match->id()));
        $this->assertCount(7, $this->cardsInTheHand->ofPlayer($this->currentPlayer, $this->match->id()));
    }

    /** @test */
    function not_playing_cards_after_the_card_playing_phase_expired()
    {
        $this->clock->fastForward($this->justOverTheCardPlayingTimeLimit);

        $this->handle(PlayTheCard::number(0, $this->currentPlayer, $this->match->id(), $this->id));

        $this->assertCount(0, $this->battlefield->cardsInPlay($this->match->id()));
        $this->assertCount(7, $this->cardsInTheHand->ofPlayer($this->currentPlayer, $this->match->id()));
    }

    /** @test */
    function playing_a_card_while_another_match_is_also_going_on()
    {
        $ourMatchId = $this->match->id();

        $this->setUpNewMatch('unrelated', 'players');

        $theirMatchId = $this->match->id();

        $this->handle(PlayTheCard::number(0, $this->currentPlayer, $ourMatchId, $this->id));

        $this->assertCount(0, $this->battlefield->cardsInPlay($theirMatchId));
        $this->assertCount(1, $this->battlefield->cardsInPlay($ourMatchId));
        $this->assertCount(6, $this->cardsInTheHand->ofPlayer($this->currentPlayer, $ourMatchId));
    }
}
