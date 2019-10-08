<?php declare(strict_types=1);

namespace Stratadox\CardGame\EventHandler;

use function assert;
use Stratadox\CardGame\DomainEvent;
use Stratadox\CardGame\Match\NextTurnBegan;
use Stratadox\CardGame\ReadModel\Match\OngoingMatch;
use Stratadox\CardGame\ReadModel\Match\OngoingMatches;

final class TurnSwitcher implements EventHandler
{
    /** @var OngoingMatches */
    private $ongoingMatches;

    public function __construct(OngoingMatches $ongoingMatches)
    {
        $this->ongoingMatches = $ongoingMatches;
    }

    public function handle(DomainEvent $nextTurn): void
    {
        assert($nextTurn instanceof NextTurnBegan);

        $this->becameTheTurnOf(
            $nextTurn->player(),
            $this->ongoingMatches->withId($nextTurn->match())
        );
    }

    private function becameTheTurnOf(
        int $theNextPlayer,
        OngoingMatch $inTheMatch
    ): void {
        $inTheMatch->beganTheTurnOf($theNextPlayer);
    }
}
