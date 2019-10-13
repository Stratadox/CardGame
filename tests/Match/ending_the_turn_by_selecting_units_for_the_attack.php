<?php declare(strict_types=1);

namespace Stratadox\CardGame\Test\Match;

use Stratadox\CardGame\Match\Command\AttackWithCard;
use Stratadox\CardGame\Match\Command\EndCardPlaying;
use Stratadox\CardGame\Match\Command\EndTheTurn;
use Stratadox\CardGame\Match\Command\PlayTheCard;
use Stratadox\CardGame\Test\CardGameTest;

/**
 * @testdox ending the turn by selecting units for the attack
 */
class ending_the_turn_by_selecting_units_for_the_attack extends CardGameTest
{
    /** @var int */
    private $currentPlayer;
    /** @var int */
    private $otherPlayer;

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
        $this->handle(
            PlayTheCard::number(1, $this->currentPlayer, $this->match->id(), $this->id)
        );
        $this->handle(
            PlayTheCard::number(0, $this->currentPlayer, $this->match->id(), $this->id)
        );
        $this->handle(EndCardPlaying::phase($this->currentPlayer, $this->match->id(), $this->id));
    }

    /** @test */
    function no_attacking_units_before_making_a_selection()
    {
        $this->assertCount(0, $this->battlefield->attackers($this->match->id()));
    }

    /** @test */
    function selecting_a_unit_for_the_attack()
    {
        $this->handle(
            AttackWithCard::number(0, $this->currentPlayer, $this->match->id(), $this->id)
        );

        $this->assertCount(1, $this->battlefield->attackers($this->match->id()));
    }

    /** @test */
    function selecting_two_units_for_the_attack()
    {
        $this->handle(
            AttackWithCard::number(0, $this->currentPlayer, $this->match->id(), $this->id)
        );
        $this->handle(
            AttackWithCard::number(1, $this->currentPlayer, $this->match->id(), $this->id)
        );

        $this->assertCount(2, $this->battlefield->attackers($this->match->id()));
    }

    /** @test */
    function not_attacking_with_non_existing_cards()
    {
        $this->handle(
            AttackWithCard::number(2, $this->currentPlayer, $this->match->id(), $this->id)
        );

        $this->assertCount(0, $this->battlefield->attackers($this->match->id()));
        $this->assertEquals(['That card does not exist'], $this->refusals->for($this->id));
    }

    /** @test */
    function ending_the_turn_after_the_attack()
    {
        $this->handle(EndTheTurn::for($this->match->id(), $this->currentPlayer, $this->id));

        $this->assertFalse($this->match->itIsTheTurnOf($this->currentPlayer));
        $this->assertTrue($this->match->itIsTheTurnOf($this->otherPlayer));
    }

    /** @test */
    function not_attacking_after_ending_the_turn()
    {
        $this->handle(EndTheTurn::for($this->match->id(), $this->currentPlayer, $this->id));

        $this->handle(
            AttackWithCard::number(1, $this->currentPlayer, $this->match->id(), $this->id)
        );

        $this->assertCount(0, $this->battlefield->attackers($this->match->id()));
        $this->assertEquals(['Cannot attack at this time'], $this->refusals->for($this->id));
    }
}
