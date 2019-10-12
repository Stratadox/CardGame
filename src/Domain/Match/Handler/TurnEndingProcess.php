<?php declare(strict_types=1);

namespace Stratadox\CardGame\Match\Handler;

use function assert;
use Stratadox\CardGame\EventBag;
use Stratadox\CardGame\Match\Command\EndTheTurn;
use Stratadox\CardGame\Match\Match;
use Stratadox\CardGame\Match\Matches;
use Stratadox\Clock\Clock;
use Stratadox\CommandHandling\Handler;

final class TurnEndingProcess implements Handler
{
    /** @var Matches */
    private $matches;
    /** @var Clock */
    private $clock;
    /** @var EventBag */
    private $eventBag;

    public function __construct(Matches $matches, Clock $clock, EventBag $eventBag)
    {
        $this->matches = $matches;
        $this->clock = $clock;
        $this->eventBag = $eventBag;
    }

    public function handle(object $command): void
    {
        assert($command instanceof EndTheTurn);

        $this->endTurn(
            $this->matches->withId($command->match()),
            $command->player()
        );
    }

    private function endTurn(Match $theMatch, int $player): void
    {
        $theMatch->endTurnOf($player, $this->clock->now());
        $this->eventBag->takeFrom($theMatch);
    }
}
