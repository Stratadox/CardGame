<?php declare(strict_types=1);

namespace Stratadox\CardGame\Test\Match;

use DateInterval;
use Stratadox\CardGame\CorrelationId;
use Stratadox\CardGame\Match\Command\AttackWithCard;
use Stratadox\CardGame\Match\Command\Block;
use Stratadox\CardGame\Match\Command\EndCardPlaying;
use Stratadox\CardGame\Match\Command\EndBlocking;
use Stratadox\CardGame\Match\Command\EndTheTurn;
use Stratadox\CardGame\Match\Command\PlayTheCard;
use Stratadox\CardGame\ReadModel\Match\OngoingMatch;
use Stratadox\CardGame\Test\CardGameTest;

/**
 * @testdox fending off the enemy attackers
 */
class fending_off_the_enemy_attackers extends CardGameTest
{
    /** @var DateInterval */
    private $tooLong;
    /** @var int */
    private $defendingTime = 20;

    protected function setUp(): void
    {
        parent::setUp();
        $this->setUpNewMatch();
        $this->determineCurrentPlayer();

        $this->tooLong = $this->interval($this->defendingTime);

        // Turn 1: Player 1 plays two units
        $this->handle(PlayTheCard::number(
            0,
            $this->currentPlayer,
            $this->match->id(),
            $this->id
        ));
        $this->handle(PlayTheCard::number(
            0,
            $this->currentPlayer,
            $this->match->id(),
            $this->id
        ));

        $this->handle(EndCardPlaying::phase(
            $this->currentPlayer,
            $this->match->id(),
            $this->id
        ));
        $this->handle(EndTheTurn::for(
            $this->match->id(),
            $this->currentPlayer,
            $this->id
        ));

        // Turn 1: Player 2 plays two units and attacks
        $this->handle(PlayTheCard::number(
            0,
            $this->otherPlayer,
            $this->match->id(),
            $this->id
        ));
        $this->handle(PlayTheCard::number(
            0,
            $this->otherPlayer,
            $this->match->id(),
            $this->id
        ));
        $this->handle(EndCardPlaying::phase(
            $this->otherPlayer,
            $this->match->id(),
            $this->id
        ));
        $this->handle(AttackWithCard::number(
            0,
            $this->otherPlayer,
            $this->match->id(),
            $this->id
        ));
        $this->handle(AttackWithCard::number(
            1,
            $this->otherPlayer,
            $this->match->id(),
            $this->id
        ));
        $this->handle(EndTheTurn::for(
            $this->match->id(),
            $this->otherPlayer,
            $this->id
        ));

        $this->assertCount(
            4,
            $this->battlefield->cardsInPlay($this->match->id())
        );
        // Turn 3: See test case
    }

    /** @test */
    function starting_in_the_defend_phase()
    {
        $this->assertEquals(OngoingMatch::PHASE_DEFEND, $this->match->phase());
    }

    /** @test */
    function moving_to_play_phase_after_defending()
    {
        $this->handle(EndBlocking::phase(
            $this->match->id(),
            $this->currentPlayer,
            $this->id
        ));
        $this->assertEquals(OngoingMatch::PHASE_PLAY, $this->match->phase());
    }

    /** @test */
    function no_defenders_before_defending()
    {
        $this->assertEmpty($this->battlefield->defenders($this->match->id()));
    }

    /** @test */
    function sending_a_unit_to_defend()
    {
        $this->handle(Block::theAttack()
            ->ofAttacker(0)
            ->withDefender(0)
            ->as($this->currentPlayer)
            ->in($this->match->id())
            ->trackedWith($this->id)
            ->go());
        $defenders = $this->battlefield->defenders($this->match->id());
        $this->assertCount(1, $defenders);
        $this->assertTrue($defenders[0]->hasTemplate($this->testCard[0]));
    }

    /** @test */
    function sending_two_units_to_defend()
    {
        $this->handle(Block::theAttack()
            ->ofAttacker(0)
            ->withDefender(0)
            ->as($this->currentPlayer)
            ->in($this->match->id())
            ->trackedWith($this->id)
            ->go());
        $this->handle(Block::theAttack()
            ->ofAttacker(1)
            ->withDefender(1)
            ->as($this->currentPlayer)
            ->in($this->match->id())
            ->trackedWith($this->id)
            ->go());
        $defenders = $this->battlefield->defenders($this->match->id());
        $this->assertCount(2, $defenders);
        $this->assertTrue($defenders[0]->hasTemplate($this->testCard[0]));
        $this->assertTrue($defenders[1]->hasTemplate($this->testCard[1]));
    }

    /** @test */
    function killing_an_attacker()
    {
         $this->handle(Block::theAttack()
             ->ofAttacker(0)
             ->withDefender(1)
             ->as($this->currentPlayer)
             ->in($this->match->id())
             ->trackedWith($this->id)
             ->go());
        $this->handle(EndBlocking::phase(
            $this->match->id(),
            $this->currentPlayer,
            $this->id
        ));

        $this->assertCount(3, $this->battlefield->cardsInPlay($this->match->id()));
        $this->assertCount(2, $this->battlefield->cardsInPlayFor(
            $this->currentPlayer,
            $this->match->id()
        ));
        $this->assertCount(1, $this->battlefield->cardsInPlayFor(
            $this->otherPlayer,
            $this->match->id()
        ));
    }

    /** @test */
    function dying_while_defending()
    {
         $this->handle(Block::theAttack()
             ->ofAttacker(1)
             ->withDefender(0)
             ->as($this->currentPlayer)
             ->in($this->match->id())
             ->trackedWith($this->id)
             ->go());
        $this->handle(EndBlocking::phase(
            $this->match->id(),
            $this->currentPlayer,
            $this->id
        ));

        $this->assertCount(
            3,
            $this->battlefield->cardsInPlay($this->match->id())
        );
        $this->assertCount(
            1,
            $this->battlefield->cardsInPlayFor($this->currentPlayer, $this->match->id())
        );
        $this->assertCount(
            2,
            $this->battlefield->cardsInPlayFor($this->otherPlayer, $this->match->id())
        );
    }

    /** @test */
    function not_blocking_the_enemy_after_time_ran_out()
    {
        $this->clock->fastForward($this->tooLong);

        $this->handle(Block::theAttack()
            ->ofAttacker(0)
            ->withDefender(0)
            ->as($this->currentPlayer)
            ->in($this->match->id())
            ->trackedWith($this->id)
            ->go());

        $this->assertCount(
            4,
            $this->battlefield->cardsInPlay($this->match->id())
        );
        $this->assertEquals(
            ['Cannot block at this time'],
            $this->refusals->for($this->id)
        );
    }

    /** @test */
    function not_blocking_in_the_enemy_turn()
    {
        $this->handle(Block::theAttack()
            ->ofAttacker(0)
            ->withDefender(0)
            ->as($this->otherPlayer)
            ->in($this->match->id())
            ->trackedWith($this->id)
            ->go());

        $this->assertCount(
            4,
            $this->battlefield->cardsInPlay($this->match->id())
        );
        $this->assertEquals(
            ['Cannot block at this time'],
            $this->refusals->for($this->id)
        );
    }

    /** @test */
    function not_blocking_with_non_existing_cards()
    {
        $this->handle(Block::theAttack()
            ->ofAttacker(0)
            ->withDefender(5)
            ->as($this->currentPlayer)
            ->in($this->match->id())
            ->trackedWith($this->id)
            ->go());

        $this->assertCount(
            4,
            $this->battlefield->cardsInPlay($this->match->id())
        );
        $this->assertEquals(
            ['No such defender'],
            $this->refusals->for($this->id)
        );
    }

    /** @test */
    function not_blocking_if_there_are_no_attackers()
    {
        $this->handle(EndTheTurn::for(
            $this->match->id(),
            $this->currentPlayer,
            $this->id
        ));

        $this->handle(EndTheTurn::for(
            $this->match->id(),
            $this->otherPlayer,
            $this->id
        ));

        $this->handle(Block::theAttack()
            ->ofAttacker(0)
            ->withDefender(0)
            ->as($this->currentPlayer)
            ->in($this->match->id())
            ->trackedWith($this->id)
            ->go());

        $this->assertEquals(
            ['Cannot block at this time'],
            $this->refusals->for($this->id)
        );
    }

    /** @test */
    function not_ending_the_blocking_phase_of_the_enemy_turn()
    {
        $this->handle(EndBlocking::phase(
            $this->match->id(),
            $this->otherPlayer,
            $this->id
        ));

        $this->assertCount(
            4,
            $this->battlefield->cardsInPlay($this->match->id())
        );
        $this->assertEquals(
            ['Cannot start the combat at this time'],
            $this->refusals->for($this->id)
        );
    }

    /** @test */
    function not_manually_ending_the_combat_phase_after_the_combat_phase_expired()
    {
        $this->clock->fastForward($this->tooLong);

        $this->handle(EndBlocking::phase(
            $this->match->id(),
            $this->currentPlayer,
            $this->id
        ));

        $this->assertEquals(
            ['Cannot start the combat at this time'],
            $this->refusals->for($this->id)
        );
    }

    /** @test */
    function automatically_ending_the_combat_phase_after_the_combat_phase_expired()
    {
        $this->handle(Block::theAttack()
            ->ofAttacker(0)
            ->withDefender(1)
            ->as($this->currentPlayer)
            ->in($this->match->id())
            ->trackedWith($this->id)
            ->go());

        $this->clock->fastForward($this->tooLong);

        $this->assertCount(3, $this->battlefield->cardsInPlay($this->match->id()));
        $this->assertCount(2, $this->battlefield->cardsInPlayFor(
            $this->currentPlayer,
            $this->match->id()
        ));
        $this->assertCount(1, $this->battlefield->cardsInPlayFor(
            $this->otherPlayer,
            $this->match->id()
        ));
    }
}
