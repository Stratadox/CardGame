<?php declare(strict_types=1);

namespace Stratadox\CardGame\EventHandler;

use function assert;
use Stratadox\CardGame\DomainEvent;
use Stratadox\CardGame\Match\Event\AttackPhaseStarted;
use Stratadox\CardGame\Match\Event\DefendPhaseStarted;
use Stratadox\CardGame\Match\Event\NextTurnStarted;
use Stratadox\CardGame\Match\Event\PlayPhaseStarted;
use Stratadox\CardGame\Match\MatchEvent;
use Stratadox\CardGame\ReadModel\Match\OngoingMatches;

final class TurnSwitcher implements EventHandler
{
    /** @var OngoingMatches */
    private $ongoingMatches;

    public function __construct(OngoingMatches $ongoingMatches)
    {
        $this->ongoingMatches = $ongoingMatches;
    }

    public function events(): iterable
    {
        return [
            NextTurnStarted::class,
            DefendPhaseStarted::class,
            PlayPhaseStarted::class,
            AttackPhaseStarted::class,
        ];
    }

    public function handle(DomainEvent $turn): void
    {
        assert($turn instanceof MatchEvent);
        $match = $this->ongoingMatches->withId($turn->aggregateId());

        if ($turn instanceof NextTurnStarted) {
            $match->startTurnOf($turn->player());
        } elseif ($turn instanceof DefendPhaseStarted) {
            $match->startDefendPhase();
        } elseif ($turn instanceof PlayPhaseStarted) {
            $match->startPlayPhase();
        } elseif ($turn instanceof AttackPhaseStarted) {
            $match->startAttackPhase();
        }
    }
}
