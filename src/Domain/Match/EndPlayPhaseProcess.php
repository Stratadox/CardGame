<?php declare(strict_types=1);

namespace Stratadox\CardGame\Match;

use function assert;
use Stratadox\CardGame\EventBag;
use Stratadox\CommandHandling\Handler;

final class EndPlayPhaseProcess implements Handler
{
    private $matches;
    private $eventBag;

    public function __construct(Matches $matches, EventBag $eventBag)
    {
        $this->matches = $matches;
        $this->eventBag = $eventBag;
    }

    public function handle(object $command): void
    {
        assert($command instanceof EndCardPlaying);

        $this->endPlayPhase(
            $command->player(),
            $this->matches->withId($command->match())
        );
    }

    private function endPlayPhase(int $thePlayer, Match $theMatch): void
    {
        $theMatch->endCardPlayingPhaseFor($thePlayer);

        $this->eventBag->takeFrom($theMatch);
    }
}
