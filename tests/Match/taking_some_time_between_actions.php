<?php declare(strict_types=1);

namespace Stratadox\CardGame\Test\Match;

use DateInterval;
use Stratadox\CardGame\CorrelationId;
use Stratadox\CardGame\Match\Command\AttackWithCard;
use Stratadox\CardGame\Match\Command\EndBlocking;
use Stratadox\CardGame\Match\Command\EndCardPlaying;
use Stratadox\CardGame\Match\Command\EndTheTurn;
use Stratadox\CardGame\Match\Command\PlayTheCard;
use Stratadox\CardGame\Test\CardGameTest;
use function var_export;

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

    public static function turnsBefore(): iterable
    {
        return [
            'In the first turn' => [0],
            'In the second turn' => [1],
            'In the third turn' => [2],
        ];
    }

    /**
     * @test
     * @dataProvider turnsBefore
     */
    function taking_the_time_to_play_cards_and_attack(int $turnsBefore)
    {
        for ($i = 0; $i < $turnsBefore; $i++) {
            $id = CorrelationId::from($i);
            $this->slowTurn($id, $i !== 0);
            $this->assertEquals([], $this->refusals->for($id));
        }

        $id = CorrelationId::from('SUT');
        $this->slowlyPlay($id, true);

        $this->assertCount(
            0,
            $this->battlefield->attackers($this->match->id()),
            var_export($this->battlefield->attackers($this->match->id()), true)
        );

        $this->handle(AttackWithCard::number(
            0,
            $this->currentPlayer,
            $this->match->id(),
            $id
        ));
        $this->assertEquals([], $this->refusals->for($id));
        $this->assertCount(
            1,
            $this->battlefield->attackers($this->match->id()),
            var_export($this->battlefield->attackers($this->match->id()), true)
        );
    }

    private function slowlyPlay(CorrelationId $id, bool $handleAttackers): void
    {
        $this->clock->fastForward($this->underThePlayingTimeLimit);

        if ($handleAttackers) {
            $this->handle(EndBlocking::phase(
                $this->match->id(),
                $this->currentPlayer,
                $id
            ));
        }

        $this->handle(PlayTheCard::number(
            0,
            $this->currentPlayer,
            $this->match->id(),
            $id
        ));
        $this->handle(EndCardPlaying::phase(
            $this->currentPlayer,
            $this->match->id(),
            $id
        ));

        $this->clock->fastForward($this->underTheAttackingTimeLimit);
    }

    private function slowTurn(CorrelationId $id, bool $handleAttackers): void
    {
        $this->slowlyPlay($id, $handleAttackers);


        $this->assertCount(
            0,
            $this->battlefield->attackers($this->match->id()),
            var_export($this->battlefield->attackers($this->match->id()), true)
        );

        $this->handle(AttackWithCard::number(
            0,
            $this->currentPlayer,
            $this->match->id(),
            $id
        ));
        $this->handle(
            EndTheTurn::for($this->match->id(), $this->currentPlayer, $id)
        );

        $playedBy = $this->currentPlayer;
        $this->currentPlayer = $this->otherPlayer;
        $this->otherPlayer = $playedBy;
    }
}
