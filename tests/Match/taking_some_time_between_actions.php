<?php declare(strict_types=1);

namespace Stratadox\CardGame\Test\Match;

use DateInterval;
use Stratadox\CardGame\Match\Command\AttackWithCard;
use Stratadox\CardGame\Match\Command\EndCardPlaying;
use Stratadox\CardGame\Match\Command\PlayTheCard;
use Stratadox\CardGame\Test\CardGameTest;

/**
 * @testdox taking some time between actions
 */
class taking_some_time_between_actions extends CardGameTest
{
    /** @var DateInterval */
    private $underThePlayingTimeLimit;

    /** @var DateInterval */
    private $underTheAttackingTimeLimit;

    protected function setUp(): void
    {
        parent::setUp();
        $this->setUpNewMatch();
        $this->determineStartingPlayer();

        $this->underThePlayingTimeLimit = $this->interval(19);
        $this->underTheAttackingTimeLimit = $this->interval(9);
    }

    /** @test */
    function taking_the_time_to_play_cards_and_attack()
    {
        $this->clock->fastForward($this->underThePlayingTimeLimit);

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

        $this->clock->fastForward($this->underTheAttackingTimeLimit);

        $this->handle(AttackWithCard::number(
            0,
            $this->currentPlayer,
            $this->match->id(),
            $this->id
        ));

        $this->assertEmpty($this->refusals->for($this->id));
        $this->assertCount(
            1,
            $this->battlefield->attackers($this->match->id())
        );
    }
}
