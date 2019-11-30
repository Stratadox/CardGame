<?php declare(strict_types=1);

namespace Stratadox\CardGame\Match\Handler;

use Stratadox\CardGame\Command;
use Stratadox\CardGame\CommandHandler;
use Stratadox\CardGame\EventBag;
use Stratadox\CardGame\Match\Matches;
use Stratadox\Clock\Clock;

final class TurnPhaseExpirationProcess implements CommandHandler
{
    /** @var Matches */
    private $matches;
    /** @var Clock */
    private $clock;
    /** @var EventBag */
    private $eventBag;

    public function __construct(
        Matches $matches,
        Clock $clock,
        EventBag $eventBag
    ) {
        $this->matches = $matches;
        $this->clock = $clock;
        $this->eventBag = $eventBag;
    }

    public function handle(Command $command): void
    {
        foreach ($this->matches->ongoing() as $ongoingMatch) {
            $ongoingMatch->endExpiredTurnOrPhase($this->clock->now());
            $this->eventBag->takeFrom($ongoingMatch);
        }
    }
}
