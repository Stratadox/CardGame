<?php declare(strict_types=1);

namespace Stratadox\CardGame\Test\Match;

use DateInterval;
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
    /** @var int */
    private $playerOne;
    /** @var int */
    private $playerTwo;
    /** @var DateInterval */
    private $tooLong;
    /** @var int */
    private $defendingTime = 20;

    protected function setUp(): void
    {
        parent::setUp();
        $this->setUpNewMatch();
        foreach ($this->match->players() as $thePlayer) {
            if ($this->match->itIsTheTurnOf($thePlayer)) {
                $this->playerOne = $thePlayer;
            } else {
                $this->playerTwo = $thePlayer;
            }
        }

        $this->tooLong = $this->interval($this->defendingTime + 1);

        // Turn 1: Player 1 plays a unit
        $this->handle(PlayTheCard::number(0, $this->playerOne, $this->match->id(), $this->id));
        $this->handle(EndCardPlaying::phase($this->playerOne, $this->match->id()));
        $this->handle(EndTheTurn::for($this->match->id(), $this->playerOne));

        // Turn 1: Player 2 plays a unit and attacks
        $this->handle(EndBlocking::phase($this->match->id(), $this->playerOne));
        $this->handle(PlayTheCard::number(1, $this->playerTwo, $this->match->id(), $this->id));
        $this->handle(EndCardPlaying::phase($this->playerTwo, $this->match->id()));
        $this->handle(AttackWithCard::number(0, $this->playerTwo, $this->match->id(), $this->id));
        $this->handle(EndTheTurn::for($this->match->id(), $this->playerTwo));

        $this->assertCount(2, $this->battlefield->cardsInPlay($this->match->id()));
        // Turn 3: See test case
    }

    /** @test */
    function blocking_the_enemy()
    {
         $this->handle(Block::theAttack()
             ->ofAttacker(0)
             ->withDefender(0)
             ->as($this->playerOne)
             ->in($this->match->id())
             ->trackedWith($this->id)
             ->go());
        $this->handle(EndBlocking::phase($this->match->id(), $this->playerOne));

        $this->assertCount(1, $this->battlefield->cardsInPlay($this->match->id()));
    }

    /** @test */
    function not_blocking_the_enemy_after_time_ran_out()
    {
        $this->clock->fastForward($this->tooLong);

        $this->handle(Block::theAttack()
            ->ofAttacker(0)
            ->withDefender(0)
            ->as($this->playerOne)
            ->in($this->match->id())
            ->trackedWith($this->id)
            ->go());
        $this->handle(EndBlocking::phase($this->match->id(), $this->playerOne));

        $this->assertCount(2, $this->battlefield->cardsInPlay($this->match->id()));
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
            ->as($this->playerTwo)
            ->in($this->match->id())
            ->trackedWith($this->id)
            ->go());
        $this->handle(EndBlocking::phase($this->match->id(), $this->playerOne));

        $this->assertCount(2, $this->battlefield->cardsInPlay($this->match->id()));
        $this->assertEquals(
            ['Cannot block at this time'],
            $this->refusals->for($this->id)
        );
    }
}
