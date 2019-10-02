<?php declare(strict_types=1);

namespace Stratadox\CardGame\Test\Match;

use function assert;
use Stratadox\CardGame\Match\AttackWithCard;
use Stratadox\CardGame\Match\EndCardPlaying;
use Stratadox\CardGame\Match\PlayerId;
use Stratadox\CardGame\Match\PlayTheCard;
use Stratadox\CardGame\Test\CardGameTest;

/**
 * @testdox ending the turn by selecting units for the attack
 */
class ending_the_turn_by_selecting_units_for_the_attack extends CardGameTest
{
    /** @var PlayerId */
    private $currentPlayer;
    /** @var PlayerId */
    private $otherPlayer;

    protected function setUp(): void
    {
        parent::setUp();
        $this->setUpNewMatch();
        assert($this->match !== null);
        foreach ($this->match->players() as $thePlayer) {
            if ($this->match->itIsTheTurnOf($thePlayer)) {
                $this->currentPlayer = $thePlayer;
            } else {
                $this->otherPlayer = $thePlayer;
            }
        }
        $this->handle(PlayTheCard::number(1, $this->currentPlayer));
        $this->handle(PlayTheCard::number(0, $this->currentPlayer));
        $this->handle(EndCardPlaying::phase($this->currentPlayer));
    }

    /** @test */
    function no_attacking_units_before_making_a_selection()
    {
        $this->assertCount(0, $this->battlefield->attackers($this->match->id()));
    }

    /** @test */
    function selecting_a_unit_for_the_attack()
    {
        $this->handle(AttackWithCard::number(0, $this->currentPlayer));

        $this->assertCount(1, $this->battlefield->attackers($this->match->id()));
    }

    /** @test */
    function selecting_two_units_for_the_attack()
    {
        $this->handle(AttackWithCard::number(0, $this->currentPlayer));
        $this->handle(AttackWithCard::number(1, $this->currentPlayer));

        $this->assertCount(2, $this->battlefield->attackers($this->match->id()));
    }

    /** @test */
    function not_attacking_with_non_existing_cards()
    {
        $this->handle(AttackWithCard::number(2, $this->currentPlayer));

        $this->assertCount(0, $this->battlefield->attackers($this->match->id()));
    }
}
