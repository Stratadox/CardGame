<?php declare(strict_types=1);

namespace Stratadox\CardGame\Match\Handler;

use function assert;
use Stratadox\CardGame\Command;
use Stratadox\CardGame\EventBag;
use Stratadox\CardGame\Match\Command\EndCardPlaying;
use Stratadox\CardGame\CommandHandler;
use Stratadox\CardGame\Match\Match;
use Stratadox\CardGame\Match\Matches;

final class EndPlayPhaseProcess implements CommandHandler
{
    private $matches;
    private $eventBag;

    public function __construct(Matches $matches, EventBag $eventBag)
    {
        $this->matches = $matches;
        $this->eventBag = $eventBag;
    }

    public function handle(Command $command): void
    {
        assert($command instanceof EndCardPlaying);

        $this->endPlayPhase(
            $command->player(),
            $this->matches->withId($command->match())
        );
    }

    private function endPlayPhase(int $playerNumber, Match $match): void
    {
        // @todo add clock
        $match->endCardPlayingPhaseFor($playerNumber);

        $this->eventBag->takeFrom($match);
    }
}
