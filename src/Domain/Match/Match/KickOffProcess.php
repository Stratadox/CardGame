<?php declare(strict_types=1);

namespace Stratadox\CardGame\Match\Match;

use function assert;
use Stratadox\CardGame\EventBag;
use Stratadox\CommandHandling\Handler;

final class KickOffProcess implements Handler
{
    private $matches;
    private $whoStarts;
    private $eventBag;

    public function __construct(
        Matches $matches,
        DecidesWhoStarts $whoStarts,
        EventBag $eventBag
    ) {
        $this->matches = $matches;
        $this->whoStarts = $whoStarts;
        $this->eventBag = $eventBag;
    }

    public function handle(object $command): void
    {
        assert($command instanceof OkayLetsGo);

        $this->kickOff($this->matches->withId($command->match()));
    }

    private function kickOff(Match $theMatch): void
    {
        $theMatch->begin($this->whoStarts->chooseBetween(...$theMatch->players()));

        $this->eventBag->takeFrom($theMatch);
    }
}
