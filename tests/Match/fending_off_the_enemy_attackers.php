<?php declare(strict_types=1);

namespace Stratadox\CardGame\Test\Match;

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
        $this->handle(PlayTheCard::number(0, $this->playerOne, $this->match->id(), $this->id));
        $this->handle(PlayTheCard::number(1, $this->playerOne, $this->match->id(), $this->id));
        $this->handle(EndCardPlaying::phase($this->playerOne, $this->match->id()));
        $this->handle(EndTheTurn::for($this->match->id(), $this->playerOne));

        $this->handle(PlayTheCard::number(1, $this->playerTwo, $this->match->id(), $this->id));
        $this->handle(EndCardPlaying::phase($this->playerTwo, $this->match->id()));
        $this->handle(AttackWithCard::number(0, $this->playerTwo, $this->match->id()));
        $this->handle(EndTheTurn::for($this->match->id(), $this->playerTwo));
    }

    /** @test */
    function blocking_the_enemy()
    {
         $this->handle(Block::attacker(0)
             ->withDefender(0)
             ->as($this->playerOne)
             ->in($this->match->id())
             ->go());
        $this->handle(EndBlocking::phase($this->match->id(), $this->playerOne));

        $this->assertCount(1, $this->battlefield->cardsInPlay($this->match->id()));
    }
}
