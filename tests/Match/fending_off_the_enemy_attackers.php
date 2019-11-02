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
        $this->determineStartingPlayer();

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
        $this->handle(EndBlocking::phase( // @todo do we need to? No attackers.
            $this->match->id(),
            $this->otherPlayer,
            $this->id
        ));
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
    function blocking_the_enemy()
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
    function not_ending_the_blocking_phase_of_the_enemy_turn()
    {
        $this->handle(Block::theAttack()
            ->ofAttacker(0)
            ->withDefender(0)
            ->as($this->currentPlayer)
            ->in($this->match->id())
            ->trackedWith(CorrelationId::from('irrelevant'))
            ->go());

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
        // @todo How to implement this??
        $this->markTestSkipped('Do the more important time-related tests first');

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
