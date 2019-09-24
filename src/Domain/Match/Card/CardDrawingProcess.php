<?php declare(strict_types=1);

namespace Stratadox\CardGame\Match\Card;

use function assert;
use Stratadox\CardGame\EventBag;
use Stratadox\CardGame\Match\Player\Player;
use Stratadox\CardGame\Match\Player\Players;
use Stratadox\CommandHandling\Handler;

final class CardDrawingProcess implements Handler
{
    private $cards;
    private $players;
    private $eventBag;

    public function __construct(Cards $cards, Players $players, EventBag $eventBag)
    {
        $this->cards = $cards;
        $this->players = $players;
        $this->eventBag = $eventBag;
    }

    public function handle(object $command): void
    {
        assert($command instanceof DrawCard);

        $this->draw(
            $this->cards->topMostCardIn($command->deck()),
            $this->players->withDeck($command->deck())
        );
    }

    private function draw(Card $card, Player $thePlayer): void
    {
        $card->draw(...$this->cards->inHandOf($thePlayer->id()));

        $this->eventBag->takeFrom($card);
    }
}
