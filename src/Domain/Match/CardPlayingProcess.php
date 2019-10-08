<?php declare(strict_types=1);

namespace Stratadox\CardGame\Match;

use function assert;
use Stratadox\CardGame\EventBag;
use Stratadox\Clock\Clock;
use Stratadox\CommandHandling\Handler;

final class CardPlayingProcess implements Handler
{
    private $matches;
    private $clock;
    private $eventBag;

    public function __construct(Matches $matches, Clock $clock, EventBag $eventBag)
    {
        $this->matches = $matches;
        $this->clock = $clock;
        $this->eventBag = $eventBag;
    }

    public function handle(object $command): void
    {
        assert($command instanceof PlayTheCard);

        $this->play(
            $this->matches->withId($command->match()),
            $command->player(),
            $command->cardNumber()
        );
    }

    private function play(Match $theMatch, int $player, int $cardNumber): void
    {
        $theMatch->playTheCard($cardNumber, $player, $this->clock->now());

        $this->eventBag->takeFrom($theMatch);
    }
}
