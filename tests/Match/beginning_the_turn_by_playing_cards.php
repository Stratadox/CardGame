<?php declare(strict_types=1);

namespace Stratadox\CardGame\Test\Match;

use Stratadox\CardGame\Match\Command\PlayTheCard;
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
        $this->handle(PlayTheCard::number(0, $this->currentPlayer));
        $this->handle(PlayTheCard::number(0, $this->currentPlayer));

        $this->assertCount(2, $this->battlefield->cardsInPlay());
        $this->assertCount(5, $this->cardsInTheHand->of($this->currentPlayer));
    }

    // @todo no cards on the board when playing a spell
}
