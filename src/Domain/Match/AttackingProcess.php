<?php declare(strict_types=1);

namespace Stratadox\CardGame\Match;

use function assert;
use Stratadox\CardGame\EventBag;
use Stratadox\Clock\Clock;
use Stratadox\CommandHandling\Handler;

final class AttackingProcess implements Handler
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
        assert($command instanceof AttackWithCard);

        $this->sendIntoBattle(
            $this->matches->withId($command->match()),
            $command->player(),
            $command->cardNumber()
        );
    }

    public function sendIntoBattle(
        Match $theMatch,
        int $player,
        int $cardNumber
    ): void {
        $theMatch->attackWithCard($cardNumber, $player, $this->clock->now());

        $this->eventBag->takeFrom($theMatch);
    }
}
