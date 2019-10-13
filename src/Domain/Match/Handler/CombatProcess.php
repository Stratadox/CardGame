<?php declare(strict_types=1);

namespace Stratadox\CardGame\Match\Handler;

use function assert;
use Stratadox\CardGame\Command;
use Stratadox\CardGame\EventBag;
use Stratadox\CardGame\Match\Command\EndBlocking;
use Stratadox\CardGame\CommandHandler;
use Stratadox\CardGame\Match\Match;
use Stratadox\CardGame\Match\Matches;
use Stratadox\Clock\Clock;

final class CombatProcess implements CommandHandler
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
