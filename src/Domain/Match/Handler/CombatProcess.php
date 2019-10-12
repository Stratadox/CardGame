<?php declare(strict_types=1);

namespace Stratadox\CardGame\Match\Handler;

use function assert;
use Stratadox\CardGame\EventBag;
use Stratadox\CardGame\Match\Command\EndBlocking;
use Stratadox\CardGame\Match\Match;
use Stratadox\CardGame\Match\Matches;
use Stratadox\Clock\Clock;
use Stratadox\CommandHandling\Handler;

final class CombatProcess implements Handler
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

    public function handle(object $command): void
    {
        assert($command instanceof EndBlocking);

        $this->timeForCombat(
            $command->player(),
            $this->matches->withId($command->match())
        );
    }

    private function timeForCombat(int $thePlayer, Match $theMatch): void
    {
        $theMatch->letTheCombatBegin($thePlayer, $this->clock->now());

        $this->eventBag->takeFrom($theMatch);
    }
}
