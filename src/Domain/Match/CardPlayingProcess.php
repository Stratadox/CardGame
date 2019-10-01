<?php declare(strict_types=1);

namespace Stratadox\CardGame\Match;

use function assert;
use Stratadox\CardGame\EventBag;
use Stratadox\CommandHandling\Handler;

final class CardPlayingProcess implements Handler
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
        assert($command instanceof PlayTheCard);

        $this->play(
            $this->matches->forPlayer($command->player()),
            $command->player(),
            $command->cardNumber()
        );
    }

    private function play(Match $theMatch, PlayerId $player, int $cardNumber): void
    {
        $theMatch->playTheCard($cardNumber, $player);

        $this->eventBag->takeFrom($theMatch);
    }
}
