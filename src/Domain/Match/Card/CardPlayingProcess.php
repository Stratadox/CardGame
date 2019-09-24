<?php declare(strict_types=1);

namespace Stratadox\CardGame\Match\Card;

use function assert;
use Stratadox\CardGame\EventBag;
use Stratadox\CommandHandling\Handler;

final class CardPlayingProcess implements Handler
{
    private $cards;
    private $eventBag;

    public function __construct(Cards $cards, EventBag $eventBag)
    {
        $this->cards = $cards;
        $this->eventBag = $eventBag;
    }

    public function handle(object $command): void
    {
        assert($command instanceof  PutIntoPlay);

        $this->play($this->cards->withId($command->card()));
    }

    private function play(Card $theCard): void
    {
        $theCard->play();

        $this->eventBag->takeFrom($theCard);
    }
}
