<?php declare(strict_types=1);

namespace Stratadox\CardGame\Test\Match;

use Stratadox\CardGame\Match\AttackWithCard;
use Stratadox\CardGame\Match\BlockTheAttacker;
use Stratadox\CardGame\Match\EndCardPlaying;
use Stratadox\CardGame\Match\EndBlocking;
use Stratadox\CardGame\Match\EndTheTurn;
use Stratadox\CardGame\Match\PlayerId;
use Stratadox\CardGame\Match\PlayTheCard;
use Stratadox\CardGame\Test\CardGameTest;

/**
 * @testdox fending off the enemy attackers
 */
class fending_off_the_enemy_attackers extends CardGameTest
{
    /** @var PlayerId */
    private $playerOne;
    /** @var PlayerId */
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
        $this->handle(PlayTheCard::number(0, $this->playerOne));
        $this->handle(PlayTheCard::number(1, $this->playerOne));
        $this->handle(EndCardPlaying::phase($this->playerOne));
        $this->handle(EndTheTurn::for($this->playerOne));

        $this->handle(PlayTheCard::number(0, $this->playerOne));
        $this->handle(PlayTheCard::number(1, $this->playerTwo));
        $this->handle(EndCardPlaying::phase($this->playerTwo));
        $this->handle(AttackWithCard::number(0, $this->playerTwo));
        $this->handle(AttackWithCard::number(1, $this->playerTwo));
        $this->handle(EndTheTurn::for($this->playerTwo));
    }

    /** @test */
    function blocking_the_enemy()
    {
        // @todo can we improve clarity here? Maybe:
        // $this->handle(Block::attacker(0)->withDefender(0)->as($player))

        $this->handle(BlockTheAttacker::number(0, 0, $this->playerOne));
        $this->handle(EndBlocking::phase($this->playerOne));

        $this->assertCount(1, $this->battlefield->cardsInPlay($this->match->id()));
    }
}
